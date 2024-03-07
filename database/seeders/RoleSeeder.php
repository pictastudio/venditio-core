<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = config('venditio-core.auth.manager')::getPermissions();

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

        // $this->assignRoles();
    }

    private function assignRoles(): void
    {
        $rootUser = User::firstWhere('email', config('venditio-core.auth.root_user.email'));
        $rootRole = Role::findByName('root');

        if ($rootUser) {
            $rootUser->assignRole($rootRole);
        }
    }
}
