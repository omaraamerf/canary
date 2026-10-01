<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RolePolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageRoles;

    /** The admin role stays complete so no one can lock the admins out of the panel. */
    public function update(User $user, Model $record): bool
    {
        return $this->allowed($user) && $record->name !== UserRole::Admin->value;
    }

    /** Built-in roles are used by name in the code, and roles still held by users would leave them without access. */
    public function delete(User $user, Model $record): bool
    {
        return $this->allowed($user)
            && ! UserRole::isBuiltIn($record->name)
            && ! $record->users()->exists();
    }
}
