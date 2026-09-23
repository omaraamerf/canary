<?php

namespace App\Policies;

use App\Enums\Permission;

class SettingPolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageSettings;
}
