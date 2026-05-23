<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:restaurants,slug',
            'address'     => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'district'    => 'nullable|string|max:100',
            'phone'       => ['required', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/'],
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'image'       => 'required|mimes:jpg,jpeg,png,webp|max:2048',
            'open_time'   => 'required|date_format:H:i',
            'close_time'  => 'required|date_format:H:i|after:open_time',
            'price_range' => 'required|numeric|min:0',
            'status'      => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Vui lòng nhập tên nhà hàng.',
            'name.max'             => 'Tên nhà hàng không được vượt quá 255 ký tự.',
            'slug.unique'          => 'Đường dẫn (slug) này đã tồn tại, vui lòng chọn tên khác.',
            'address.required'     => 'Vui lòng nhập địa chỉ nhà hàng.',
            'city.required'        => 'Vui lòng chọn hoặc nhập tỉnh/thành phố.',
            'phone.required'       => 'Vui lòng nhập số điện thoại liên hệ.',
            'phone.regex'          => 'Số điện thoại không đúng định dạng (VD: 098xxxxxxx).',
            'category_id.required' => 'Vui lòng chọn danh mục cho nhà hàng.',
            'category_id.exists'   => 'Danh mục đã chọn không hợp lệ.',
            'description.max'      => 'Mô tả nhà hàng không được vượt quá 2000 ký tự.',
            'image.required'       => 'Vui lòng tải lên hình ảnh nhà hàng.',
            'image.mimes'          => 'Hình ảnh phải có định dạng: jpg, jpeg, png hoặc webp.',
            'image.max'            => 'Dung lượng hình ảnh không được vượt quá 2MB.',
            'open_time.required'   => 'Vui lòng nhập giờ mở cửa.',
            'open_time.date_format'=> 'Giờ mở cửa không đúng định dạng (HH:mm).',
            'close_time.required'  => 'Vui lòng nhập giờ đóng cửa.',
            'close_time.date_format'=>'Giờ đóng cửa không đúng định dạng (HH:mm).',
            'close_time.after'     => 'Giờ đóng cửa phải sau giờ mở cửa.',
            'price_range.required' => 'Vui lòng nhập khoảng giá trung bình.',
            'price_range.numeric'  => 'Khoảng giá phải là một con số.',
            'price_range.min'      => 'Khoảng giá không được nhỏ hơn 0.',
        ];
    }
}