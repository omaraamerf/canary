<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionName;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(PermissionName::cases())
            ->mapWithKeys(fn (PermissionName $permission) => [
                $permission->value => Permission::findOrCreate($permission->value, 'web'),
            ]);

        $admin = Role::findOrCreate(UserRole::Admin->value, 'web');
        $admin->syncPermissions(
            $permissions->except(PermissionName::AccessSellerPanel->value)->values()
        );

        $seller = Role::findOrCreate(UserRole::Seller->value, 'web');
        $seller->syncPermissions($permissions->only([
            PermissionName::AccessSellerPanel->value,
            PermissionName::ViewOwnBirds->value,
            PermissionName::ViewBird->value,
            PermissionName::CreateBird->value,
            PermissionName::UpdateOwnBird->value,
            PermissionName::DeleteOwnBird->value,
            PermissionName::ViewOwnOrders->value,
            PermissionName::ViewOrder->value,
            PermissionName::UpdateOwnOrder->value,
        ])->values());

        $member = Role::findOrCreate(UserRole::Member->value, 'web');
        $member->syncPermissions([]);

        User::query()->where('role', UserRole::Member->value)->each(
            fn (User $user) => $user->syncRoles([$member])
        );
        User::query()->where('role', UserRole::Admin->value)->each(
            fn (User $user) => $user->syncRoles([$admin])
        );
        User::query()->where('role', UserRole::Seller->value)->each(
            fn (User $user) => $user->syncRoles([$seller])
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
