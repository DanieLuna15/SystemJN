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
                'name' => 'Usuario Administrador',
                'last_name' => 'Lopez',
                'email' => 'pastor@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Pastor/Admin',
                'address' => 'Calle Ficticia 123, Ciudad',
                'ci' => '1234567',
                'profile_image' => null,
            ],
            [
                'name' => 'Usuario Líder',
                'last_name' => 'Gómez',
                'email' => 'lider@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Líder',
                'address' => 'Calle Lider 456, Ciudad',
                'ci' => '8765432',
                'profile_image' => null,
            ],
            [
                'name' => 'Usuario Servidor',
                'last_name' => 'Martinez',
                'email' => 'servidor@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '1122334',
                'profile_image' => null,
            ],
            [
                'name' => 'Usuario Miembro',
                'last_name' => 'Vargas',
                'email' => 'miembro@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Miembro',
                'address' => 'Calle Miembros 789, Ciudad',
                'ci' => '12344551',
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



