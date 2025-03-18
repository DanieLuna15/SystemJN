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
                'name' => 'Emils',
                'last_name' => 'Callisaya',
                'email' => 'emils@example.com',
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '13312958',
                'phone' => '74781231'
            ],
            [
                'name' => 'Ariana',
                'last_name' => 'Porce',
                'email' => 'ariana@example.com',
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '13959831',
                'phone' => '74781232'
            ],
            [
                'name' => 'Kevin',
                'last_name' => 'Condori',
                'email' => 'kevin@example.com',
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '13692223',
                'phone' => '74781233'
            ],
            [
                'name' => 'César',
                'last_name' => 'Yujra',
                'email' => 'cesar@example.com',
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '7031339',
                'phone' => '74781234'
            ],
            [
                'name' => 'Silvia',
                'last_name' => 'Mamani',
                'email' => 'silvia@example.com',
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '12544603',
                'phone' => '74781235'
            ],
            [
                'name' => 'Jazmin',
                'last_name' => 'Mejia',
                'email' => 'jaz@example.com',
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '13675884',
                'phone' => '74781236'
            ],
            [
                'name' => 'Rafael',
                'last_name' => 'Chambilla',
                'email' => 'rafael@example.com',
                'role' => 'Servidor',
                'address' => 'Calle Servidores 789, Ciudad',
                'ci' => '10080310',
                'phone' => '74781237'
            ],
        ];

        foreach ($users as $userData) {
            $generatedPassword = generatePassword($userData['name'], $userData['last_name'], $userData['ci'], $userData['phone']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'last_name' => $userData['last_name'],
                    'address' => $userData['address'],
                    'ci' => $userData['ci'],
                    'password' => Hash::make($generatedPassword),
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
