<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewAnyOrders->value)
            || $user->can(Permission::ViewOwnOrders->value);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can(Permission::ViewAnyOrders->value)
            || ($user->can(Permission::ViewOwnOrders->value) && $this->owns($user, $order));
    }

    public function update(User $user, Order $order): bool
    {
        return $user->can(Permission::UpdateAnyOrder->value)
            || ($user->can(Permission::UpdateOwnOrder->value) && $this->owns($user, $order));
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Order $order): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function owns(User $user, Order $order): bool
    {
        return $order->bird()->where('seller_id', $user->id)->exists();
    }
}
