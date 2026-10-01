<?php

namespace App\Services;

use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function save(array $data, ?Role $role = null): Role
    {
        return DB::transaction(function () use ($data, $role): Role {
            $role ??= new Role(['guard_name' => 'web']);

            if (! UserRole::isBuiltIn($role->name ?? '')) {
                $role->name = $data['name'];
            }

            $role->save();

            // syncPermissions also clears Spatie's permission cache, which a plain pivot sync would not.
            $role->syncPermissions(
                collect($data['permissions'] ?? [])->map(fn (string $name) => Permission::findOrCreate($name, 'web'))
            );

            return $role;
        });
    }
}
