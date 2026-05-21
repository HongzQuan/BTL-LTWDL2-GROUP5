<?php
// app/Http/Middleware/EnsureBookingOwner.php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBookingOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Lấy booking_id từ route parameter (tên param có thể là 'id' hoặc 'booking')
        $bookingId = $request->route('id') ?? $request->route('booking');

        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền truy cập đơn đặt bàn này.');
        }

        // Gắn booking vào request để tái sử dụng, tránh query lại
        $request->attributes->set('booking', $booking);

        return $next($request);
    }
}
