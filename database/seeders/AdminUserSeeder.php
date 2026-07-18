<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@supplychain.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_admin' => 1,
            ]
        );
    }
}