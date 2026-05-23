<?php
// app/Http/Controllers/ReviewController.php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Restaurant;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // ── 1. Validate input ──────────────────────────────────────────────
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'restaurant_id.exists' => 'Nhà hàng không tồn tại.',
            'rating.min'           => 'Đánh giá tối thiểu là 1 sao.',
            'rating.max'           => 'Đánh giá tối đa là 5 sao.',
            'comment.min'          => 'Nhận xét phải có ít nhất 10 ký tự.',
            'comment.max'          => 'Nhận xét không được quá 500 ký tự.',
        ]);

        $userId       = Auth::id();
        $restaurantId = $validated['restaurant_id'];

        // ── 2. Kiểm tra đã có booking completed tại nhà hàng này ──────────
        $hasCompletedBooking = Booking::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'completed')   // ← điều chỉnh giá trị status theo project
            ->exists();

        if (! $hasCompletedBooking) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bạn cần đã đặt bàn và hoàn thành để đánh giá.');
        }

        // ── 3. Tạo mới hoặc cập nhật review ──────────────────────────────
        Review::updateOrCreate(
            [
                'user_id'       => $userId,
                'restaurant_id' => $restaurantId,
            ],
            [
                'rating'  => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        // ── 4. Redirect về trang nhà hàng, cuộn xuống #reviews ───────────
        return redirect()
            ->to(route('restaurants.show', $restaurantId) . '#reviews')
            ->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}
