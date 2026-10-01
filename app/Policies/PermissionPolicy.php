<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/** Permissions are defined in App\Enums\Permission and checked in code, so the panel only lists them. */
class PermissionPolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageRoles;

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Model $record): bool
    {
        return false;
    }

    public function delete(User $user, Model $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
