<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagerUserRepository
{
    public function getAllUsers()
    {
        return User::select(['id', 'name', 'email', 'role', 'email_verified_at'])
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public function findById(int $id)
    {
        return User::find($id);
    }

    public function findWithTrashed(int $id)
    {
        return User::withTrashed()->find($id);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(User $user): bool
    {
        return $user->restore();
    }

    public function forceDelete(User $user): bool
    {
        return $user->forceDelete();
    }

    public function updatePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    public function getTrashed()
    {
        return User::onlyTrashed()->get();
    }
}