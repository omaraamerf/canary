<?php

namespace App\Policies;

use App\Enums\Permission;

class ArticlePolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageArticles;
}
