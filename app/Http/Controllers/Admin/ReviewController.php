<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Restaurant;

class ReviewController extends Controller
{

    public function index(Request $request)
    {
        // Eager load user và restaurant
        $query = Review::with(['user', 'restaurant']);

        // Lọc theo nhà hàng
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // Lọc theo số sao (1-5)
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'DESC')->paginate(20)->appends($request->all());
        $restaurants = Restaurant::all();

        // Lấy thống kê tổng số lượng review theo từng mức sao (để hiển thị badge)
        $ratingStats = Review::selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray();

        return view('admin.reviews.index', compact('reviews', 'restaurants', 'ratingStats'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã xóa đánh giá');
    }
}
