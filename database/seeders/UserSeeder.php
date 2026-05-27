<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@dewakoding.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
            'email_verified_at' => now(),
        ]);

        // Regular User
        User::create([
            'name' => 'User',
            'email' => 'user@dewakoding.com',
            'password' => Hash::make('password'),
            'is_admin' => 0,
            'email_verified_at' => now(),
        ]);

        echo "✓ 2 users created (1 admin, 1 regular user)\n";
    }
}
