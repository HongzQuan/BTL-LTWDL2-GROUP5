<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => 'required|exists:restaurants,id',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'required|string|min:10|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'restaurant_id.required' => 'Không thể gửi đánh giá do thiếu thông tin nhà hàng.',
            'restaurant_id.exists'   => 'Nhà hàng này không tồn tại trên hệ thống.',
            'rating.required'        => 'Vui lòng chọn số sao đánh giá.',
            'rating.integer'         => 'Điểm đánh giá phải là số nguyên.',
            'rating.min'             => 'Điểm đánh giá tối thiểu là 1 sao.',
            'rating.max'             => 'Điểm đánh giá tối đa là 5 sao.',
            'comment.required'       => 'Vui lòng viết nội dung đánh giá.',
            'comment.min'            => 'Nội dung đánh giá quá ngắn, vui lòng nhập ít nhất 10 ký tự.',
            'comment.max'            => 'Nội dung đánh giá không được vượt quá 500 ký tự.'
        ];
    }
}