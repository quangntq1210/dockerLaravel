<?php

namespace App\Http\Services;

use App\Repositories\Eloquent\ManagerUserRepository;
use Exception;

class ManagerUserService
{
    protected $userRepository;

    public function __construct(ManagerUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserListData()
    {
        return $this->userRepository->getAllUsers();
    }

  
    // DELETE USER
  
    public function deleteUser(int $id)
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new Exception("User không tồn tại");
        }

        if ($user->role === 'admin') {
            throw new Exception("Không thể xoá tài khoản admin");
        }

        return $this->userRepository->delete($user);
    }

    // RESTORE USER
  
    public function restoreUser(int $id)
    {
        $user = $this->userRepository->findWithTrashed($id);

        if (!$user) {
            throw new Exception("User không tồn tại");
        }

        return $this->userRepository->restore($user);
    }

  
    // FORCE DELETE
 
    public function forceDeleteUser(int $id)
    {
        $user = $this->userRepository->findWithTrashed($id);

        if (!$user) {
            throw new Exception("User không tồn tại");
        }

        if ($user->role === 'admin') {
            throw new Exception("Không thể xoá admin");
        }

        return $this->userRepository->forceDelete($user);
    }
}