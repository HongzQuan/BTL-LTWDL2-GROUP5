<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Restaurant::with('category');

        // Lọc theo thành phố
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $restaurants = Restaurant::paginate(12);

        // Cung cấp 2 biến này sang cho View
        $cities = Restaurant::select('city')->distinct()->pluck('city')->filter();
        $categories = Category::all();

        return view('admin.restaurants.index', compact('restaurants', 'cities', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.restaurants.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'phone'       => ['nullable', 'regex:/^0[0-9]{9,10}$/'],
            'open_time'   => 'required|date_format:H:i',
            'close_time'  => 'required|date_format:H:i|after:open_time',
            'price_min'   => 'required|numeric|min:0',
            'price_max'   => 'required|numeric|gte:price_min', // Lấy theo biến ở bài trước
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
            // Nếu bạn dùng accessor $restaurant->image_url ở view thì lưu path vào DB
        }

        $validated['status'] = 1; // Mặc định active khi mới tạo
        $validated['slug'] = Str::slug($validated['name']);
        Restaurant::create($validated);

        return redirect()->route('admin.restaurants.index')->with('success', 'Thêm nhà hàng thành công!');
    }

    // Hiển thị form Sửa nhà hàng
    public function edit($id)
    {
        $restaurant = \App\Models\Restaurant::findOrFail($id);
        $categories = \App\Models\Category::all();

        // Trỏ đúng vào view của Admin
        return view('admin.restaurants.edit', compact('restaurant', 'categories'));
    }

    // Xử lý lưu dữ liệu khi cập nhật
    public function update(Request $request, $id)
    {
        $restaurant = \App\Models\Restaurant::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
        ]);

        $restaurant->name = $request->name;
        $restaurant->category_id = $request->category_id;
        $restaurant->city = $request->city;
        $restaurant->district = $request->district ?? '';
        $restaurant->address = $request->address;
        $restaurant->phone = $request->phone;
        $restaurant->open_time = $request->open_time;
        $restaurant->close_time = $request->close_time;
        $restaurant->price_min = $request->price_min ?? 0;
        $restaurant->price_max = $request->price_max ?? 0;
        $restaurant->description = $request->description;

        // Xử lý upload ảnh mới (nếu có chọn ảnh khác)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('restaurants', 'public');
            $restaurant->image_url = '/storage/' . $path;
            // Ở cột CSDL của em nếu dùng 'image' thì sửa thành: $restaurant->image = $path;
        }

        $restaurant->save();

        return redirect()->route('admin.restaurants.index')->with('success', 'Đã cập nhật thông tin nhà hàng thành công!');
    }

    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // Kiểm tra booking pending/confirmed
        // Giả sử quan hệ trong Model Restaurant là: public function bookings() { return $this->hasMany(Booking::class); }
        $hasActiveBookings = $restaurant->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->route('admin.restaurants.index')->with('error', 'Không thể vô hiệu hóa! Nhà hàng đang có đơn đặt bàn chờ xử lý hoặc đã xác nhận.');
        }

        // Xóa ảnh cũ (nếu muốn giữ lại ảnh cho lịch sử thì bỏ đoạn này)
        if ($restaurant->image) {
            Storage::disk('public')->delete($restaurant->image);
        }

        // Soft disable
        $restaurant->update([
            'status' => 0,
            'image'  => null
        ]);

        return redirect()->route('admin.restaurants.index')->with('success', 'Đã vô hiệu hóa nhà hàng thành công!');
    }

    public function show($id)
    {
        // 1. Tải thông tin Nhà hàng, GỘP LẤY LUÔN cả Category, MenuItems và Reviews
        // Dùng with() giúp tối ưu hóa, gọi Database 1 lần lấy được hết dữ liệu liên quan
        $restaurant = \App\Models\Restaurant::with(['category', 'menuItems', 'reviews.user'])->findOrFail($id);

        // 2. Logic tính toán Đánh giá (Rating)
        $averageRating = $restaurant->reviews->avg('rating') ?? 0;

        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($restaurant->reviews as $review) {
            $ratingCounts[$review->rating]++;
        }

        // 3. Logic lấy Nhà hàng tương tự (Gợi ý)
        // Tìm các nhà hàng CÙNG DANH MỤC, KHÁC ID hiện tại, lấy ngẫu nhiên 4 cái
        $similarRestaurants = \App\Models\Restaurant::where('category_id', $restaurant->category_id)
            ->where('id', '!=', $id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Nếu Database chưa có nhà hàng nào cùng danh mục, tự động lấy ngẫu nhiên nhà hàng bất kỳ
        if ($similarRestaurants->isEmpty()) {
            $similarRestaurants = \App\Models\Restaurant::where('id', '!=', $id)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        // 4. Logic kiểm tra quyền viết Review (Chỉ ai đã đặt bàn thành công mới được viết)
        $canReview = false;
        if (auth()->check()) {
            $canReview = \App\Models\Booking::where('user_id', auth()->id())
                ->where('restaurant_id', $id)
                ->where('status', 'completed')
                ->exists();
        }

        // Truyền toàn bộ biến ra ngoài giao diện (View)
        return view('restaurants.show', compact(
            'restaurant',
            'averageRating',
            'ratingCounts',
            'similarRestaurants',
            'canReview'
        ));
    }
}
