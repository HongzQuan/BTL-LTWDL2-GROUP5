<?php
// app/Http/Controllers/Admin/ReviewController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    // ── Danh sách reviews ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Review::with(['user', 'restaurant'])
            ->latest();

        // Filter theo nhà hàng
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->integer('restaurant_id'));
        }

        // Filter theo rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->integer('rating'));
        }

        $reviews     = $query->paginate(20)->withQueryString();
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', compact('reviews', 'restaurants'));
    }

    // ── Xóa review ────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Đã xóa đánh giá thành công.');
    }
}
