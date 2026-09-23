<?php

namespace App\Policies;

use App\Enums\Permission;

class RegionPolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageRegions;
}
