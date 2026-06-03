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
            $file = $request->file('image');

            // Tạo tên file độc nhất để không bị trùng (ví dụ: 1716728400_bun-cha.jpg)
            $filename = time() . '_' . $file->getClientOriginalName();

            // Di chuyển file ảnh THẲNG vào thư mục public/uploads/restaurants/
            $file->move(public_path('uploads/restaurants'), $filename);

            // Lưu đường dẫn này vào Database: "uploads/restaurants/tên_file.jpg"
            $restaurant->image = 'uploads/restaurants/' . $filename;
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
            // Xóa ảnh cũ
            if ($restaurant->image && file_exists(public_path($restaurant->image))) {
                unlink(public_path($restaurant->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            $file->move(public_path('uploads/restaurants'), $filename);
            
            $restaurant->image = 'uploads/restaurants/' . $filename;
        }

        $restaurant->save();

        return redirect()->route('admin.restaurants.index')->with('success', 'Đã cập nhật thông tin nhà hàng thành công!');
    }

    public function destroy($id)
    {
        $restaurant = \App\Models\Restaurant::findOrFail($id);

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
        $restaurant->delete();
        return redirect()->route('admin.restaurants.index')->with('success', 'Đã xóa nhà hàng thành công!');
    }

    public function show($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // Tìm các nhà hàng CÙNG DANH MỤC, loại trừ nhà hàng hiện tại
        $similar = Restaurant::where('category_id', $restaurant->category_id)
            ->where('id', '!=', $restaurant->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // DỰ PHÒNG: Nếu chuyên mục này ít quá không có nhà hàng nào giống, thì lấy ĐẠI 4 nhà hàng ngẫu nhiên trong hệ thống cho giao diện khỏi bị trống
        if ($similar->isEmpty()) {
            $similar = Restaurant::where('id', '!=', $restaurant->id)->inRandomOrder()->take(4)->get();
        }

        return view('restaurants.show', compact('restaurant', 'similar'));
    }
}
