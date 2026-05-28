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

        // CHỐT CHẶN 1: Kiểm tra nếu ngày giờ chọn ở trang chi tiết đã trôi qua
        $bookingDateTime = Carbon::parse($date . ' ' . $time);
        if ($bookingDateTime->isPast()) {
            return redirect()->back()
                ->with('error', 'Thời gian đặt bàn đã qua so với hiện tại. Vui lòng chọn giờ khác trong tương lai!');
        }

        $restaurant = Restaurant::findOrFail($restaurantId);

        $availableTables = Table::where('restaurant_id', $restaurantId)
            ->where('capacity', '>=', $guests)
            ->whereDoesntHave('bookings', function ($query) use ($date, $time) {
                $query->where('booking_date', $date)
                    ->where('booking_time', $time)
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
        // CHỐT CHẶN 2: Kiểm tra một lần nữa trước khi ghi dữ liệu vào Database để chống hack form
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

        $booking = DB::transaction(function () use ($validated) {
            $table = Table::where('id', $validated['table_id'])
                ->where('restaurant_id', $validated['restaurant_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $isBooked = Booking::where('table_id', $table->id)
                ->where('booking_date', $validated['booking_date'])
                ->where('booking_time', $validated['booking_time'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($isBooked) {
                throw ValidationException::withMessages(['table_id' => 'Bàn này vừa được đặt bởi người khác.']);
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
            ->with('success', "Đặt bàn thành công! Mã đơn #{$booking->id}");
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
}