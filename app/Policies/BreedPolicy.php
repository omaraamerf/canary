<?php

namespace App\Policies;

use App\Enums\Permission;

class BreedPolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageBreeds;
}
