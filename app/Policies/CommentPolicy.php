<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->moderates($user);
    }

    public function view(User $user, Comment $comment): bool
    {
        return $this->moderates($user);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $this->moderates($user);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->isOwnedBy($user) || $this->moderates($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->moderates($user);
    }

    private function moderates(User $user): bool
    {
        return $user->can(Permission::ManageCommunity->value);
    }
}
