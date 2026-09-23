<?php

namespace App\Policies;

use App\Enums\Permission;

class ArticleCategoryPolicy extends ManageResourcePolicy
{
    protected Permission $permission = Permission::ManageArticleCategories;
}
