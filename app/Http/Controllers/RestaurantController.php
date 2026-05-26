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
        // Sử dụng withAvg để tính trung bình rating ngay từ database, giúp việc sort chính xác và tối ưu hơn
        $query = Restaurant::active()->with('category')->withAvg('reviews', 'rating');

        // 1. Lọc theo Thành phố (City)
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // 2. Lọc theo Danh mục (Category)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Lọc theo Khoảng giá (Giả định price_range lưu số tiền, vd: 500000)
        if ($request->filled('price_min')) {
            $query->where('price_range', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price_range', '<=', $request->price_max);
        }

        // 4. Tìm kiếm từ khóa (q)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($subQuery) use ($q) {
                // 1. Tìm trong tên nhà hàng
                $subQuery->where('name', 'like', "%{$q}%")
                    // 2. Tìm trong địa chỉ
                    ->orWhere('address', 'like', "%{$q}%")
                    // 3. MỚI: Tìm luôn trong Tên danh mục (Category)
                    ->orWhereHas('category', function ($catQuery) use ($q) {
                        $catQuery->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // 5. Sắp xếp (Sort)
        switch ($request->sort) {
            case 'rating':
                $query->orderByDesc('reviews_avg_rating'); // Dùng cột tính toán từ withAvg
                break;
            case 'price_asc':
                $query->orderBy('price_range', 'asc');
                break;
            case 'price_desc':
                $query->orderByDesc('price_range');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        // Phân trang 12 item và giữ lại toàn bộ query string (để chuyển trang không mất filter)
        $restaurants = $query->paginate(12)->withQueryString();

        // Lấy dữ liệu cho bộ lọc ở Sidebar
        $categories = Category::all();
        // Lấy danh sách các thành phố độc nhất đang có nhà hàng active
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

        // DỰ PHÒNG: Nếu không có nhà hàng cùng danh mục, lấy ngẫu nhiên 4 nhà hàng khác
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

        // Truyền đúng tên biến sang View
        return view('restaurants.show', compact('restaurant', 'ratingDistribution', 'similarRestaurants', 'canReview'));
    }
    /**
     * Tìm kiếm nhà hàng (Yêu cầu phải có từ khóa q)
     */
    public function search(Request $request)
    {
        // Bắt buộc phải nhập từ khóa
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        // Tận dụng lại logic của hàm index để tận dụng luôn filter & sort
        // View index sẽ tự động nhận diện có request('q') để xử lý highlight
        return $this->index($request);
    }
}
