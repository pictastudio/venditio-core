<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function PictaStudio\VenditioCore\Helpers\Functions\auth_manager;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = auth_manager()->getPermissions();

        foreach ($permissions as $key => $permission) {
            Permission::findOrCreate($permission);
        }

        foreach (config('venditio-core.auth.roles') as $key => $name) {
            $role = Role::findOrCreate($name);

            if ($key === 'root') {
                $role->givePermissionTo($permissions);
                // User::firstWhere('email', config('venditio-core.auth.root_user.email'))?->assignRole($role);
            }
        }
    }
}
