<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Normal User',
                'email' => 'normal@example.com',
                'password' => bcrypt('password'),
                'role' => 'normal'
            ],
            [
                'name' => 'Silver User',
                'email' => 'silver@example.com',
                'password' => bcrypt('password'),
                'role' => 'silver'
            ],
            [
                'name' => 'Gold User',
                'email' => 'gold@example.com',
                'password' => bcrypt('password'),
                'role' => 'gold'
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::create($userData);
            $user->assignRole($role);
        }
    }
}
