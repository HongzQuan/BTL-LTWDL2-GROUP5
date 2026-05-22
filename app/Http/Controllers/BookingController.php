<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Áp dụng auth middleware cho toàn bộ controller.
     */
    // =========================================================
    // CREATE — Hiển thị form đặt bàn
    // GET /bookings/create?restaurant_id=&date=&time=&guests=
    // =========================================================
    public function create(Request $request)
    {
        // --- Validate query string đầu vào ---
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

        // --- Load nhà hàng ---
        $restaurant = Restaurant::findOrFail($restaurantId);

        // --- Lấy danh sách bàn còn trống ---
        // Điều kiện:
        //   1. Thuộc nhà hàng này
        //   2. capacity >= số khách
        //   3. Chưa có booking nào cùng date + time với status pending/confirmed
        $availableTables = Table::where('restaurant_id', $restaurantId)
            ->where('capacity', '>=', $guests)
            ->whereDoesntHave('bookings', function ($query) use ($date, $time) {
                $query->where('booking_date', $date)
                      ->where('booking_time', $time)
                      ->whereIn('status', ['pending', 'confirmed']);
            })
            ->orderBy('capacity')
            ->get();

        return view('bookings.create', compact(
            'restaurant',
            'availableTables',
            'date',
            'time',
            'guests'
        ));
    }

    // =========================================================
    // STORE — Xử lý submit form đặt bàn
    // POST /bookings
    // =========================================================
    public function store(Request $request)
    {
        // --- Validate dữ liệu form ---
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'table_id'      => ['required', 'integer', 'exists:tables,id'],
            'booking_date'  => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'booking_time'  => ['required', 'date_format:H:i'],
            'guests'        => ['required', 'integer', 'min:1', 'max:50'],
            'note'          => ['nullable', 'string', 'max:500'],
        ]);

        // --- Dùng DB Transaction + lock để tránh race condition ---
        $booking = DB::transaction(function () use ($validated) {

            // Lock row của bàn lại trong transaction
            $table = Table::where('id', $validated['table_id'])
                ->where('restaurant_id', $validated['restaurant_id'])
                ->lockForUpdate()   // <-- SELECT ... FOR UPDATE
                ->firstOrFail();

            // Kiểm tra lại bàn còn trống tại thời điểm submit
            $isBooked = Booking::where('table_id', $table->id)
                ->where('booking_date', $validated['booking_date'])
                ->where('booking_time', $validated['booking_time'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($isBooked) {
                // Ném exception để rollback transaction và hiển thị lỗi
                throw ValidationException::withMessages([
                    'table_id' => 'Bàn này vừa được đặt bởi người khác. Vui lòng chọn bàn khác.',
                ]);
            }

            // Kiểm tra capacity
            if ($table->capacity < $validated['guests']) {
                throw ValidationException::withMessages([
                    'guests' => "Bàn này chỉ phục vụ tối đa {$table->capacity} khách.",
                ]);
            }

            // Tạo booking
            return Booking::create([
                'user_id'       => auth()->id(),
                'restaurant_id' => $validated['restaurant_id'],
                'table_id'      => $table->id,
                'booking_date'  => $validated['booking_date'],
                'booking_time'  => $validated['booking_time'],
                'guests'        => $validated['guests'],
                'note'          => $validated['note'] ?? null,
                'status'        => 'pending',
            ]);
        });

        return redirect()
            ->route('bookings.show', $booking->id)
            ->with('success', "Đặt bàn thành công! Mã đơn #{$booking->id}");
    }

    // =========================================================
    // INDEX — Danh sách đặt bàn của user
    // GET /bookings?status=
    // =========================================================
    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:pending,confirmed,cancelled,completed'],
        ]);

        $bookings = Booking::with(['restaurant', 'table'])
            ->where('user_id', auth()->id())
            // Lọc theo status nếu có
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(10)
            ->withQueryString(); // Giữ query string khi chuyển trang

        // Truyền status hiện tại để highlight tab/filter trên view
        $currentStatus = $request->input('status');

        return view('bookings.index', compact('bookings', 'currentStatus'));
    }

    // =========================================================
    // SHOW — Chi tiết một đơn đặt bàn
    // GET /bookings/{id}
    // (Đã qua middleware booking.owner)
    // =========================================================
    public function show(Request $request, int $id)
    {
        // Middleware đã query và gắn sẵn booking vào request attributes
        // Dùng lại để tránh query lần 2
        /** @var Booking $booking */
        $booking = $request->attributes->get('booking');

        // Load thêm relationships cần thiết
        if (!$booking) {
            $booking = Booking::with([
                'restaurant',
                'table',
                'user'
            ])->findOrFail($id);
        }

        $booking->load([
            'restaurant',
            'table',
            'user'
        ]);

        // Có thể hủy không?
        $canCancel = $booking->status === 'pending';

        // Có thể review không?
        // Điều kiện: đã hoàn thành VÀ chưa có review của user này tại nhà hàng này
        $canReview = false;
        if (
            $booking->status === 'completed'
            && $booking->restaurant
            && method_exists($booking->restaurant, 'reviews')
        ) {
            $canReview = ! $booking->restaurant
                ->reviews()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->exists();
        }

        return view('bookings.show', compact('booking', 'canCancel', 'canReview'));
    }

    // =========================================================
    // CANCEL — Hủy đặt bàn
    // PUT /bookings/{id}/cancel
    // (Đã qua middleware booking.owner)
    // =========================================================
    public function cancel(Request $request, int $id)
    {
        /** @var Booking $booking */
        $booking = $request->attributes->get('booking');
        if (!$booking) {
            $booking = Booking::findOrFail($id);
        }
        // Chỉ cho hủy khi đang ở trạng thái pending
        if ($booking->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Chỉ có thể hủy đơn đang chờ xác nhận.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()
            ->back()
            ->with('success', "Đã hủy đơn đặt bàn #{$booking->id} thành công.");
    }
}
