<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Các Metric thống kê nhanh
        $totalRestaurants = Restaurant::count();
        $todayBookings = Booking::whereDate('created_at', Carbon::today())->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalUsers = User::where('role', 'user')->count();

        // 2. 10 Booking mới nhất (Eager loading để tránh N+1 query)
        $recentBookings = Booking::with(['user', 'restaurant', 'table'])
            ->latest()
            ->take(10)
            ->get();

        // 3. Top 5 nhà hàng được đặt nhiều nhất trong 7 ngày qua (JOIN + GROUP BY + COUNT)
        $topRestaurants = Restaurant::select('restaurants.id', 'restaurants.name', DB::raw('COUNT(bookings.id) as bookings_count'))
            ->join('bookings', 'restaurants.id', '=', 'bookings.restaurant_id')
            ->where('bookings.created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('restaurants.id', 'restaurants.name')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        // 4. Dữ liệu biểu đồ đơn đặt 7 ngày gần nhất
        $labels = [];
        $data = [];
        
        // Tạo mảng 7 ngày gần nhất để đảm bảo ngày nào không có đơn sẽ hiển thị số 0
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d/m');
            
            $count = Booking::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        $chartData = [
            'labels' => $labels,
            'data' => $data
        ];

        // Pass tất cả data lên view
        return view('admin.dashboard.index', compact(
            'totalRestaurants', 
            'todayBookings', 
            'pendingBookings', 
            'totalUsers', 
            'recentBookings', 
            'topRestaurants', 
            'chartData'
        ));
    }
}