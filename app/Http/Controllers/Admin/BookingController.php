<?php

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
        // ĐÃ FIX LỖI SỐ 1: Sắp xếp theo ID giảm dần để mã đơn mới nhất luôn nằm trên cùng
        $query = Booking::with(['user', 'restaurant', 'table'])
            ->orderBy('id', 'desc');

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

        // ── Fix logic: Gộp cả Ngày và Giờ để check xung đột chính xác ──
        $dateOnly = Carbon::parse($booking->booking_date)->format('Y-m-d');
        $bookingDateTime = Carbon::parse($dateOnly . ' ' . $booking->booking_time);

        // Đơn ăn kéo dài 2 tiếng: lùi 119 phút và tiến 119 phút quanh giờ hẹn đặt bàn
        $startTime   = $bookingDateTime->copy()->subMinutes(119);
        $endTime     = $bookingDateTime->copy()->addMinutes(119);

        $conflict = Booking::where('id', '!=', $booking->id)
            ->where('table_id', $booking->table_id)
            ->where('status', 'confirmed') // Chỉ check với đơn ĐÃ XÁC NHẬN thực tế
            ->where(function ($q) use ($startTime, $endTime) {
                // Phải nối ngày và giờ trong DB để so sánh chính xác
                $q->whereRaw("CONCAT(booking_date, ' ', booking_time) BETWEEN ? AND ?", [$startTime, $endTime]);
            })
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
                ->with('error', 'Chỉ đơn hàng Đã xác nhận mới có thể chuyển sang Hoàn thành.');
        }

        // ── Fix logic: So sánh chính xác đến giờ phút hiện tại ─────────
        $dateOnly = Carbon::parse($booking->booking_date)->format('Y-m-d');
        $bookingDateTime = Carbon::parse($dateOnly . ' ' . $booking->booking_time);

        // Nếu thời gian hẹn ở TƯƠNG LAI (lớn hơn thời gian thực tế hiện tại)
        if ($bookingDateTime->greaterThan(Carbon::now())) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Chưa đến giờ hẹn đặt bàn thực tế của khách, không thể hoàn thành sớm.');
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'completed';
            $booking->save();
        });

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Tuyệt vời! Đã cập nhật trạng thái khách dùng bữa thành công. Khách hàng giờ đây có thể đánh giá nhà hàng.');
    }
}
