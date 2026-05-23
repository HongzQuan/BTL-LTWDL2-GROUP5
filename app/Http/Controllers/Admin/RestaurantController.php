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
            'phone'       => ['required', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'],
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

        return redirect()->route('restaurants.index')->with('success', 'Thêm nhà hàng thành công!');
    }

    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $categories = Category::all();
        return view('admin.restaurants.edit', compact('restaurant', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'phone'       => ['required', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'],
            'open_time'   => 'required|date_format:H:i',
            'close_time'  => 'required|date_format:H:i',
            'price_min'   => 'required|numeric|min:0',
            'price_max'   => 'required|numeric|gte:price_min',
            'status'      => 'required|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }
            // Upload ảnh mới
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($validated);

        return redirect()->route('restaurants.index')->with('success', 'Cập nhật nhà hàng thành công!');
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
            return redirect()->route('restaurants.index')->with('error', 'Không thể vô hiệu hóa! Nhà hàng đang có đơn đặt bàn chờ xử lý hoặc đã xác nhận.');
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

        return redirect()->route('restaurants.index')->with('success', 'Đã vô hiệu hóa nhà hàng thành công!');
    }
}
