<?php
// app/Http/Controllers/Admin/BookingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Hiển thị danh sách booking với filter & sort
     */
    public function index(Request $request)
    {
        // ── Thống kê nhanh theo status ──────────────────────────────
        $stats = Booking::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statSummary = [
            'pending'   => $stats->get('pending', 0),
            'confirmed' => $stats->get('confirmed', 0),
            'completed' => $stats->get('completed', 0),
            'cancelled' => $stats->get('cancelled', 0),
        ];

        // ── Query chính ─────────────────────────────────────────────
        $query = Booking::with(['user', 'restaurant', 'table'])
            ->orderBy('booking_date', 'desc');

        // Filter: restaurant_id
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // Filter: status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: date_from
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        // Filter: date_to
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // Filter: q — tìm theo tên user hoặc SĐT
        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                  ->orWhere('phone', 'like', $keyword);
            });
        }

        $bookings    = $query->paginate(15)->withQueryString();
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        return view('admin.bookings.index', compact(
            'bookings',
            'restaurants',
            'statSummary'
        ));
    }

    /**
     * Xác nhận booking — chỉ khi status = pending
     */
    public function confirm(Request $request, $id)
    {
        $booking = Booking::with('table')->findOrFail($id);

        // ── Guard: chỉ pending mới được confirm ─────────────────────
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Chỉ có thể xác nhận đơn đang chờ xử lý.');
        }

        // ── Sửa lỗi 1 & 3: Kiểm tra khoảng thời gian xung đột (Ví dụ: cách nhau 2 tiếng) ──
        // Đơn ăn kéo dài 2 tiếng: lùi 119 phút và tiến 119 phút quanh giờ hẹn đặt bàn
        $bookingTime = Carbon::parse($booking->booking_date);
        $startTime   = $bookingTime->copy()->subMinutes(119);
        $endTime     = $bookingTime->copy()->addMinutes(119);

        $conflict = Booking::where('id', '!=', $booking->id)
            ->where('table_id', $booking->table_id)
            ->where('status', 'confirmed') // Chỉ check với đơn ĐÃ XÁC NHẬN thực tế
            ->whereBetween('booking_date', [$startTime, $endTime])
            ->exists();

        if ($conflict) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Bàn này đã được một khách khác đặt thành công trong khung giờ này (mỗi lượt ăn cách nhau 2 tiếng). Không thể xác nhận.');
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'confirmed';
            $booking->save();
        });

        return redirect()->route('admin.bookings.index')
            ->with('success', "Đã xác nhận đơn đặt bàn #$booking->id thành công.");
    }

    /**
     * Hủy booking — cho phép khi status = pending | confirmed
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // ── Guard: chỉ pending / confirmed mới hủy được ─────────────
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Không thể hủy đơn ở trạng thái hiện tại.');
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'cancelled';
            $booking->save();
        });

        return redirect()->route('admin.bookings.index')
            ->with('success', "Đã hủy đơn đặt bàn #$booking->id.");
    }

    /**
     * Hoàn thành booking — chỉ khi status = confirmed VÀ giờ hẹn đã đến hoặc đã qua
     */
    public function complete(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // ── Guard 1: phải là confirmed ───────────────────────────────
        if ($booking->status !== 'confirmed') {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Chỉ đơn đã xác nhận mới được đánh dấu hoàn thành.');
        }

        // ── Sửa lỗi 2: So sánh chính xác đến giờ phút hiện tại ─────────
        // Khách phải đến đúng giờ hẹn hoặc muộn hơn giờ hẹn (quá khứ) thì mới được bấm hoàn thành
        if (Carbon::parse($booking->booking_date)->greaterThan(Carbon::now())) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Chưa đến giờ hẹn đặt bàn thực tế của khách, không thể hoàn thành sớm.');
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'completed';
            $booking->save();
        });

        return redirect()->route('admin.bookings.index')
            ->with('success', "Đơn đặt bàn #$booking->id đã được đánh dấu hoàn thành.");
    }
}