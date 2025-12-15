<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default roles/users
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Site Administrator',
                'password' => Hash::make('password'),
                'photo' => null,
                'role' => User::ROLE_ADMIN,
                'phone_number' => '0800000000',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Demo Seller',
                'password' => Hash::make('password'),
                'photo' => null,
                'role' => User::ROLE_SELLER,
                'phone_number' => '0811111111',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'photo' => null,
                'role' => User::ROLE_CUSTOMER,
                'phone_number' => '0822222222',
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            FakeOrderSeeder::class,
        ]);
    }
}
