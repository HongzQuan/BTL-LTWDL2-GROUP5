<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Hiển thị danh sách nhà hàng với Filter và Sort
     */
    public function index(Request $request)
    {
        // Khởi tạo query với scopeActive và Eager Loading category
        // Sử dụng withAvg để tính trung bình rating ngay từ database
        $query = Restaurant::active()->with('category')->withAvg('reviews', 'rating');

        // 1. Lọc theo Thành phố (City)
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // 2. Lọc theo Danh mục (Category)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Lọc theo Khoảng giá (Đã sửa lại đúng cột price_min và price_max)
        if ($request->filled('price_min')) {
            $query->where('price_min', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price_max', '<=', $request->price_max);
        }

        // 4. Tìm kiếm từ khóa (q)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($subQuery) use ($q) {
                // 1. Tìm trong tên nhà hàng
                $subQuery->where('name', 'like', "%{$q}%")
                    // 2. Tìm trong địa chỉ
                    ->orWhere('address', 'like', "%{$q}%")
                    // 3. Tìm luôn trong Tên danh mục (Category)
                    ->orWhereHas('category', function ($catQuery) use ($q) {
                        $catQuery->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // 5. Sắp xếp (Sort) - Đã sửa lại đúng cột price_min
        switch ($request->sort) {
            case 'rating':
                $query->orderByDesc('reviews_avg_rating'); 
                break;
            case 'price_asc':
                $query->orderBy('price_min', 'asc'); // Sắp xếp theo giá thấp nhất tăng dần
                break;
            case 'price_desc':
                $query->orderByDesc('price_min'); // Sắp xếp theo giá thấp nhất giảm dần
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        // Phân trang 12 item và giữ lại toàn bộ query string
        $restaurants = $query->paginate(12)->withQueryString();

        // Lấy dữ liệu cho bộ lọc ở Sidebar
        $categories = Category::all();
        $cities = Restaurant::active()->select('city')->distinct()->pluck('city')->filter();

        return view('restaurants.index', compact('restaurants', 'categories', 'cities'));
    }

    /**
     * Hiển thị chi tiết một nhà hàng
     */
    public function show($id)
    {
        $restaurant = Restaurant::active()
            ->with(['category', 'tables', 'menuItems', 'reviews.user'])
            ->findOrFail($id);

        $ratingDistribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($restaurant->reviews as $review) {
            if (isset($ratingDistribution[$review->rating])) {
                $ratingDistribution[$review->rating]++;
            }
        }

        // Lấy 4 nhà hàng tương tự CÙNG DANH MỤC
        $similarRestaurants = Restaurant::active()
            ->where('category_id', $restaurant->category_id)
            ->where('id', '!=', $restaurant->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // DỰ PHÒNG
        if ($similarRestaurants->isEmpty()) {
            $similarRestaurants = Restaurant::active()
                ->where('id', '!=', $restaurant->id)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        $canReview = false;
        if (Auth::check()) {
            $canReview = \App\Models\Booking::where('user_id', Auth::id())
                ->where('restaurant_id', $id)
                ->where('status', 'completed')
                ->exists();
        }

        return view('restaurants.show', compact('restaurant', 'ratingDistribution', 'similarRestaurants', 'canReview'));
    }

    /**
     * Tìm kiếm nhà hàng
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        return $this->index($request);
    }
}