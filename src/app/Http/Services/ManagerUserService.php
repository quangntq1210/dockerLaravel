<?php
namespace App\Http\Services;

use App\Repositories\Eloquent\ManagerUserRepository;


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
}