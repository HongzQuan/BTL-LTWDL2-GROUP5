<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBookingOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Lấy model booking từ route: ví dụ Route::get('/bookings/{booking}', ...)
        $booking = $request->route('booking'); 

        // Nếu booking tồn tại và người đang đăng nhập không phải chủ sở hữu (và không phải admin)
        if ($booking && $booking->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập thông tin đặt bàn này.');
        }

        return $next($request);
    }
}