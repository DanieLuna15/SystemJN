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
                'last_name' => 'Pérez',
                'email' => 'pastor@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Pastor/Admin',
                'address' => 'Calle Ficticia 123, Ciudad',
                'ci' => '1234567',
                'profile_image' => null,
            ],
            [
                'name' => 'Líder de Jóvenes',
                'last_name' => 'Gómez',
                'email' => 'lider@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Líder',
                'address' => 'Calle Juvenil 456, Ciudad',
                'ci' => '8765432',
                'profile_image' => null,
            ],
            [
                'name' => 'Miembro de Congregación',
                'last_name' => 'López',
                'email' => 'miembro@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Miembro',
                'address' => 'Calle Miembros 789, Ciudad',
                'ci' => '1122334',
                'profile_image' => null,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'last_name' => $userData['last_name'],
                    'address' => $userData['address'],
                    'ci' => $userData['ci'],
                    'profile_image' => $userData['profile_image'],
                    'password' => $userData['password'],
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}



