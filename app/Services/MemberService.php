<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Support\LocationOptions;
use Illuminate\Support\Facades\DB;

class MemberService
{
    public function __construct(private readonly LocationOptions $locations) {}

    public function register(array $data): User
    {
        $data = $this->locations->normalize($data);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => UserRole::Member->value,
                'status' => UserStatus::Active->value,
                'country_id' => $data['country_id'] ?? null,
                'region_id' => $data['region_id'] ?? null,
            ]);

            $user->assignRole(UserRole::Member->value);

            return $user;
        });
    }
}
