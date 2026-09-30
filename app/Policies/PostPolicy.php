<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Enums\UserStatus;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->moderates($user);
    }

    public function view(?User $user, Post $post): bool
    {
        return $post->status === PostStatus::Published
            || $post->isOwnedBy($user)
            || ($user !== null && $this->moderates($user));
    }

    public function create(User $user): bool
    {
        return $user->status === UserStatus::Active->value;
    }

    public function comment(User $user, Post $post): bool
    {
        return $user->status === UserStatus::Active->value && $post->status === PostStatus::Published;
    }

    public function acceptComment(User $user, Post $post): bool
    {
        return $post->isOwnedBy($user);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->moderates($user);
    }

    public function delete(User $user, Post $post): bool
    {
        return $post->isOwnedBy($user) || $this->moderates($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->moderates($user);
    }

    public function restore(User $user, Post $post): bool
    {
        return $this->moderates($user);
    }

    public function restoreAny(User $user): bool
    {
        return $this->moderates($user);
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $this->moderates($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->moderates($user);
    }

    private function moderates(User $user): bool
    {
        return $user->can(Permission::ManageCommunity->value);
    }
}
