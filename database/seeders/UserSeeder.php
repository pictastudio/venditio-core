<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use PictaStudio\Venditio\Managers\AuthManager;
use Spatie\Permission\Models\Role;

use function PictaStudio\Venditio\Helpers\Functions\query;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('venditio.auth.root_user.email');
        $password = config('venditio.auth.root_user.password');

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
