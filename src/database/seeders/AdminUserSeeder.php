<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Tạo tài khoản Admin mẫu
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Kiểm tra nếu email này chưa có thì mới tạo
            [
                'name' => 'Quang Admin',
                'password' => Hash::make('12345678'), // Luôn phải Hash mật khẩu
                'role' => 'admin',
            ]
        );

        // (Tùy chọn) Tạo thêm 1 tài khoản User thường để test phân quyền
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Quang User',
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ]
        );
    }
}