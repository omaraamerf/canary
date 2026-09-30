<?php

namespace App\Policies;

use App\Enums\Permission;

class CountryPolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageRegions;
}
