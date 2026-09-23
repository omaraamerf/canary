<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Bird;
use App\Models\User;

class BirdPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewAnyBirds->value)
            || $user->can(Permission::ViewOwnBirds->value);
    }

    public function view(User $user, Bird $bird): bool
    {
        return $user->can(Permission::ViewAnyBirds->value)
            || ($user->can(Permission::ViewOwnBirds->value) && $this->owns($user, $bird));
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CreateBird->value);
    }

    public function update(User $user, Bird $bird): bool
    {
        return $user->can(Permission::UpdateAnyBird->value)
            || ($user->can(Permission::UpdateOwnBird->value) && $this->owns($user, $bird));
    }

    public function delete(User $user, Bird $bird): bool
    {
        return $user->can(Permission::DeleteAnyBird->value)
            || ($user->can(Permission::DeleteOwnBird->value) && $this->owns($user, $bird));
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(Permission::DeleteAnyBird->value);
    }

    public function restore(User $user, Bird $bird): bool
    {
        return $user->can(Permission::RestoreBird->value);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can(Permission::RestoreBird->value);
    }

    public function forceDelete(User $user, Bird $bird): bool
    {
        return $user->can(Permission::ForceDeleteBird->value);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can(Permission::ForceDeleteBird->value);
    }

    private function owns(User $user, Bird $bird): bool
    {
        return $bird->seller_id === $user->id;
    }
}
