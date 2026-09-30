<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = new User;
            $user->forceFill($data)->save();
            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            }

            $user->forceFill($data)->save();
            $user->syncRoles([$data['role']]);

            return $user->refresh();
        });
    }
}
