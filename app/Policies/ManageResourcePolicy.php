<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class ManageResourcePolicy
{
    protected Permission $permission;

    public function viewAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function view(User $user, Model $record): bool
    {
        return $this->allowed($user);
    }

    public function create(User $user): bool
    {
        return $this->allowed($user);
    }

    public function update(User $user, Model $record): bool
    {
        return $this->allowed($user);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->allowed($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allowed($user);
    }

    protected function allowed(User $user): bool
    {
        return $user->can($this->permission->value);
    }
}
