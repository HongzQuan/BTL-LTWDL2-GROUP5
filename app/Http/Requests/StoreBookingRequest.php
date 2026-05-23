<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_id'      => 'required|exists:tables,id',
            'booking_date'  => 'required|date|after_or_equal:today',
            'booking_time'  => 'required|date_format:H:i',
            'guests'        => 'required|integer|min:1|max:50',
            'note'          => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'restaurant_id.required' => 'Hệ thống không xác định được nhà hàng, vui lòng thử lại.',
            'restaurant_id.exists'   => 'Nhà hàng không tồn tại trên hệ thống.',
            'table_id.required'      => 'Vui lòng chọn bàn muốn đặt.',
            'table_id.exists'        => 'Bàn đã chọn không tồn tại hoặc đã bị xóa.',
            'booking_date.required'  => 'Vui lòng chọn ngày đến.',
            'booking_date.date'      => 'Ngày đặt bàn không hợp lệ.',
            'booking_date.after_or_equal' => 'Bạn không thể đặt bàn cho ngày trong quá khứ.',
            'booking_time.required'  => 'Vui lòng chọn giờ đến.',
            'booking_time.date_format'=> 'Định dạng giờ không hợp lệ (HH:mm).',
            'guests.required'        => 'Vui lòng nhập số lượng khách.',
            'guests.integer'         => 'Số lượng khách phải là số nguyên.',
            'guests.min'             => 'Số lượng khách tối thiểu là 1 người.',
            'guests.max'             => 'Số lượng khách không được vượt quá 50 người.',
            'note.max'               => 'Ghi chú không được dài quá 500 ký tự.'
        ];
    }
}