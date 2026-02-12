<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\User;
use Spatie\Permission\Models\{Permission, Role};

use function PictaStudio\Venditio\Helpers\Functions\auth_manager;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = auth_manager()->getPermissions();

        foreach ($permissions as $key => $permission) {
            Permission::findOrCreate($permission);
        }

        foreach (config('venditio.auth.roles') as $key => $name) {
            $role = Role::findOrCreate($name);

            if ($key === 'root') {
                $role->givePermissionTo($permissions);
                // User::firstWhere('email', config('venditio.auth.root_user.email'))?->assignRole($role);
            }
        }
    }
}
