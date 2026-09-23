<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewSellers->value);
    }

    public function view(User $user, User $seller): bool
    {
        return $user->can(Permission::ViewSellers->value);
    }

    public function update(User $user, User $seller): bool
    {
        return $user->can(Permission::UpdateSellers->value);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, User $seller): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
