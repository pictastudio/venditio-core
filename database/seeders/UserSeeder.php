<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\{Hash};
use PictaStudio\VenditioCore\Managers\AuthManager;
use Spatie\Permission\Models\Role;

use function PictaStudio\VenditioCore\Helpers\Functions\{query};

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('venditio-core.auth.root_user.email');
        $password = config('venditio-core.auth.root_user.password');

        if ($email && $password) {
            $rootUser = query('user')->create([
                // 'first_name' => 'Root',
                // 'last_name' => 'User',
                'name' => 'Root User',
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->assignRoles($rootUser);
        }
    }

    private function assignRoles(User $rootUser): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rootRole = Role::findByName(AuthManager::ROLE_ROOT);

        if ($rootUser && $rootRole) {
            $rootUser->assignRole($rootRole);
        }
    }
}
