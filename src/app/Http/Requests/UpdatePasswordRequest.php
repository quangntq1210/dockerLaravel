<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'current_password' => 'required', // PHẢI THÊM DÒNG NÀY
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'Email này không tồn tại trong hệ thống.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
        ];
    }
}