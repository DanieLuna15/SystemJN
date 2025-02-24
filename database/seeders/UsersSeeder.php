<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Pastor Principal',
                'email' => 'pastor@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Pastor/Admin',
            ],
            [
                'name' => 'Líder de Jóvenes',
                'email' => 'lider@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Líder',
            ],
            [
                'name' => 'Miembro de Congregación',
                'email' => 'miembro@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Miembro',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
