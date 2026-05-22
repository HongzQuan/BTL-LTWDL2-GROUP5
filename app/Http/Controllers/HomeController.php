<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::all();

        // Top 8 theo lượt đặt bàn hoặc đánh giá (giả sử có cột rating)
        $topRestaurants = \App\Models\Restaurant::orderBy('created_at', 'DESC')->take(8)->get();

        // 6 nhà hàng mới nhất
        $newRestaurants = \App\Models\Restaurant::orderBy('created_at', 'DESC')->take(6)->get();

        return view('home', compact('categories', 'topRestaurants', 'newRestaurants'));
    }

    public function category(string $slug)
    {
        // 1. Tìm danh mục khớp với đường dẫn (slug) khách vừa bấm
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();

        // 2. Tìm tất cả nhà hàng thuộc danh mục đó (và đang được phép hoạt động)
        $restaurants = \App\Models\Restaurant::where('category_id', $category->id)
            ->where('status', 1)
            ->get();

        // 3. Tạm thời in ra màn hình để test xem đã bắt đúng bệnh chưa
        return "Em đang xem danh mục: " . $category->name . " | Hệ thống tìm thấy " . $restaurants->count() . " nhà hàng.";
    }
}
