<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * CREATE — Hiển thị form đặt bàn
     */
    public function create(Request $request)
    {
        $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'date'          => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time'          => ['required', 'date_format:H:i'],
            'guests'        => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $restaurantId = $request->integer('restaurant_id');
        $date         = $request->input('date');
        $time         = $request->input('time');
        $guests       = $request->integer('guests');

        // CHỐT CHẶN 1: Kiểm tra nếu ngày giờ chọn đã trôi qua
        $bookingDateTime = Carbon::parse($date . ' ' . $time);
        if ($bookingDateTime->isPast()) {
            return redirect()->back()
                ->with('error', 'Thời gian đặt bàn đã qua so với hiện tại. Vui lòng chọn giờ khác trong tương lai!');
        }

        $restaurant = Restaurant::findOrFail($restaurantId);

        // CHỐT CHẶN 2: Kiểm tra giờ mở/đóng cửa ngay từ vòng gửi xe
        $bookingTimeStr = strtotime($time);
        $openTimeStr = strtotime($restaurant->open_time);
        $closeTimeStr = strtotime($restaurant->close_time);

        if ($bookingTimeStr < $openTimeStr || $bookingTimeStr > $closeTimeStr) {
            return redirect()->back()
                ->with('error', "Rất tiếc! Nhà hàng chỉ mở cửa từ {$restaurant->open_time} đến {$restaurant->close_time}. Vui lòng chọn giờ khác.");
        }

        // CHỐT CHẶN 3: Lọc bàn trống (cách nhau 2 tiếng) ở bước hiển thị form
        $requestedTime = Carbon::parse($time);
        $startTime = $requestedTime->copy()->subMinutes(119)->format('H:i:s');
        $endTime   = $requestedTime->copy()->addMinutes(119)->format('H:i:s');

        $availableTables = Table::where('restaurant_id', $restaurantId)
            ->where('capacity', '>=', $guests)
            ->whereDoesntHave('bookings', function ($query) use ($date, $startTime, $endTime) {
                $query->where('booking_date', $date)
                    ->whereBetween('booking_time', [$startTime, $endTime])
                    ->whereIn('status', ['pending', 'confirmed']);
            })
            ->orderBy('capacity')
            ->get();

        return view('bookings.create', compact('restaurant', 'availableTables', 'date', 'time', 'guests'));
    }

    /**
     * STORE — Xử lý submit form đặt bàn
     */
    public function store(Request $request)
    {
        // Kiểm tra một lần nữa trước khi ghi dữ liệu vào Database để chống hack form
        if ($request->filled(['booking_date', 'booking_time'])) {
            $bookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);

            if ($bookingDateTime->isPast()) {
                return back()
                    ->with('error', 'Lỗi: Thời gian đặt bàn đã qua. Vui lòng quay lại chọn giờ khác trong tương lai!')
                    ->withInput();
            }
        }

        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'table_id'      => ['required', 'integer', 'exists:tables,id'],
            'booking_date'  => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'booking_time'  => ['required', 'date_format:H:i'],
            'guests'        => ['required', 'integer', 'min:1', 'max:50'],
            'note'          => ['nullable', 'string', 'max:500'],
        ]);

        // KIỂM TRA LẠI GIỜ MỞ/ĐÓNG CỬA CỦA NHÀ HÀNG KHI LƯU
        $restaurant = Restaurant::findOrFail($validated['restaurant_id']);
        $bookingTimeStr = strtotime($validated['booking_time']);
        $openTimeStr = strtotime($restaurant->open_time);
        $closeTimeStr = strtotime($restaurant->close_time);

        if ($bookingTimeStr < $openTimeStr || $bookingTimeStr > $closeTimeStr) {
            return back()
                ->with('error', "Rất tiếc! Nhà hàng chỉ mở cửa từ {$restaurant->open_time} đến {$restaurant->close_time}. Vui lòng chọn giờ khác.")
                ->withInput();
        }

        $booking = DB::transaction(function () use ($validated) {
            $table = Table::where('id', $validated['table_id'])
                ->where('restaurant_id', $validated['restaurant_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // CHỐT CHẶN 4: Kiểm tra lại khoảng cách 2 tiếng trước khi lưu Database
            $requestedTimeStore = Carbon::parse($validated['booking_time']);
            $startTimeStore = $requestedTimeStore->copy()->subMinutes(119)->format('H:i:s');
            $endTimeStore   = $requestedTimeStore->copy()->addMinutes(119)->format('H:i:s');

            $isBooked = Booking::where('table_id', $table->id)
                ->where('booking_date', $validated['booking_date'])
                ->whereBetween('booking_time', [$startTimeStore, $endTimeStore])
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($isBooked) {
                throw ValidationException::withMessages(['table_id' => 'Bàn này đã có khách đặt. Các ca đặt bàn phải cách nhau ít nhất 2 tiếng!']);
            }

            if ($table->capacity < $validated['guests']) {
                throw ValidationException::withMessages(['guests' => "Bàn này chỉ phục vụ tối đa {$table->capacity} khách."]);
            }

            return Booking::create(array_merge($validated, [
                'user_id' => auth()->id(),
                'status'  => 'pending',
            ]));
        });

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', "Đặt bàn thành công! Vui lòng thanh toán tiền cọc để giữ chỗ.");
    }

    /**
     * INDEX — Danh sách đặt bàn của user
     */
    public function index(Request $request)
    {
        $status = $request->input('status');

        $bookings = Booking::with(['restaurant', 'table'])
            ->where('user_id', auth()->id())
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('booking_date')
            ->paginate(10)
            ->withQueryString();

        return view('bookings.index', compact('bookings', 'status'));
    }

    /**
     * SHOW — Chi tiết đơn
     */
    public function show(Request $request, int $id)
    {
        $booking = Booking::with(['restaurant', 'table', 'user'])->where('user_id', auth()->id())->findOrFail($id);

        $canCancel = $booking->status === 'pending';

        $canReview = ($booking->status === 'completed') &&
            !$booking->restaurant->reviews()->where('user_id', auth()->id())->exists();

        return view('bookings.show', compact('booking', 'canCancel', 'canReview'));
    }

    /**
     * CANCEL — Hủy đặt bàn
     */
    public function cancel(int $id)
    {
        $booking = Booking::where('user_id', auth()->id())->findOrFail($id);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Không thể hủy đơn ở trạng thái hiện tại.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy đơn đặt bàn thành công.');
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $inputData = array();

        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $bookingId = $inputData['vnp_TxnRef'];

        // Tìm lại đơn hàng khách vừa đặt
        $booking = Booking::findOrFail($bookingId);

        if ($secureHash == $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // 1. THANH TOÁN THÀNH CÔNG -> Cập nhật trạng thái
                $booking->update(['status' => 'confirmed']); // Chuyển từ pending sang confirmed

                // 2. GỬI EMAIL TỰ ĐỘNG
                try {
                    \Illuminate\Support\Facades\Mail::to($booking->user->email)
                        ->send(new \App\Mail\BookingConfirmedMail($booking));
                } catch (\Exception $e) {
                    // Nếu lỗi mail (do mạng yếu) thì vẫn cho qua, không làm chết trang
                    \Illuminate\Support\Facades\Log::error('Lỗi gửi mail: ' . $e->getMessage());
                }

                return redirect()->route('bookings.show', $booking->id)
                    ->with('success', 'Thanh toán thành công! Chúng tôi đã gửi một email xác nhận đến bạn.');
            } else {
                // THANH TOÁN THẤT BẠI HOẶC KHÁCH BẤM HỦY
                $booking->update(['status' => 'cancelled']);
                return redirect()->route('bookings.show', $booking->id)
                    ->with('error', 'Giao dịch không thành công hoặc đã bị hủy.');
            }
        } else {
            return redirect()->route('bookings.index')
                ->with('error', 'Lỗi bảo mật: Chữ ký không hợp lệ!');
        }
    }

    public function createPayment($id)
    {
        $booking = Booking::findOrFail($id);

        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = route('vnpay.return');
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_TxnRef = $booking->id;
        $vnp_OrderInfo = "Thanh toan tien coc dat ban don hang " . $booking->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = 200000 * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }
}
