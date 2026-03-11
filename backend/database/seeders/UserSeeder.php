<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table.
     */
    public function run(): void
    {
        $users = [
            ['id' => 1, 'name' => 'Alice Mercer', 'email' => 'alice@example.com'],
            ['id' => 2, 'name' => 'Bob Tanaka', 'email' => 'bob@example.com'],
            ['id' => 3, 'name' => 'Clara Voss', 'email' => 'clara@example.com'],
            ['id' => 4, 'name' => 'Dave Park', 'email' => 'dave@example.com'],
            ['id' => 5, 'name' => 'Eve Santos', 'email' => 'eve@example.com'],
        ];

        foreach ($users as $user) {
            User::create([
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
