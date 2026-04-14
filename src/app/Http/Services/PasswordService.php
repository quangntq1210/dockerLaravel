<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Repositories\Eloquent\UserRepository;

class PasswordService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function quickChangePassword(array $data): void
    {
      
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user) {
            throw new Exception('Email không tồn tại trên hệ thống.');
        }

       
        if (!Hash::check($data['current_password'], $user->password)) {
            throw new Exception('Mật khẩu cũ (hiện tại) không chính xác.');
        }

    
        $this->userRepository->updatePassword($user, $data['password']);
    }
}