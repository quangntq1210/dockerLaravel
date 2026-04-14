<?php
namespace App\Repositories\Eloquent;

use App\Models\User;

class ManagerUserRepository
{
    public function getAllUsers()
    {
       return User::select(['id', 'name', 'email',  'role', 'email_verified_at'])
                   ->orderBy('id', 'desc')
                  ->paginate(10);
                //    ->makeVisible(['password']);
    }
}