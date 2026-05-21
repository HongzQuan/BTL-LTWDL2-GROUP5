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
                $subQuery->where('name', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
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
        // Load nhà hàng kèm các quan hệ cần thiết
        $restaurant = Restaurant::active()
            ->with(['category', 'tables', 'menuItems', 'reviews.user'])
            ->findOrFail($id);

        // Tính phân phối rating (Đếm số lượng 1-5 sao)
        $ratingDistribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($restaurant->reviews as $review) {
            if (isset($ratingDistribution[$review->rating])) {
                $ratingDistribution[$review->rating]++;
            }
        }

        // Lấy 4 nhà hàng tương tự (cùng category, khác nhà hàng hiện tại)
        $similarRestaurants = Restaurant::active()
            ->where('category_id', $restaurant->category_id)
            ->where('id', '!=', $restaurant->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Kiểm tra quyền đánh giá: User đã login & có booking hoàn thành tại đây
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
