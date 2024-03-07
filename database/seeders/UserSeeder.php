<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PictaStudio\VenditioCore\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Root',
            'last_name' => 'User',
            'email' => config('venditio-core.auth.root_user.email'),
            'password' => Hash::make(config('venditio-core.auth.root_user.password')),
        ]);
    }
}
