<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'phone'            => ['required', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/'],
            'current_password' => 'required_with:new_password',
            'new_password'     => 'nullable|string|min:8|confirmed'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                 => 'Vui lòng nhập họ và tên.',
            'name.max'                      => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.required'                => 'Vui lòng nhập số điện thoại.',
            'phone.regex'                   => 'Số điện thoại không đúng định dạng (VD: 098xxxxxxx).',
            'current_password.required_with'=> 'Vui lòng nhập mật khẩu hiện tại nếu bạn muốn đổi mật khẩu mới.',
            'new_password.min'              => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed'        => 'Xác nhận mật khẩu mới không khớp.'
        ];
    }
}