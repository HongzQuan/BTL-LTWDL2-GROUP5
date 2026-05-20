<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách danh mục
        $categories = Category::all();
        
        // Lấy 6 nhà hàng đang hoạt động mới nhất
        $restaurants = Restaurant::active()->latest()->take(6)->get();

        return view('home', compact('categories', 'restaurants'));
    }
}