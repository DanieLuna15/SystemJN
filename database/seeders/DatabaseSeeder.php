<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Definir roles y permisos
        $rolesPermissions = [
            'Pastor' => [
                'gestionar congregación',
                'ver reportes generales',
                'crear usuarios',
                'ver usuarios',
                'editar usuarios',
                'eliminar usuarios',
                'crear eventos',
                'ver eventos',
                'editar eventos',
                'eliminar eventos',
                'crear grupos',
                'ver grupos',
                'editar grupos',
                'eliminar grupos',
            ],
            'Líder' => [
                'gestionar grupos',
                'ver reportes',
                'crear eventos',
                'ver eventos',
                'editar eventos',
                'crear grupos',
                'ver grupos',
                'editar grupos',
            ],
            'Miembro' => [
                'ver eventos',
                'ver reportes',
                'ver grupos',
            ],
        ];

        // Crear permisos y roles
        foreach ($rolesPermissions as $role => $permissions) {
            $roleCreated = Role::firstOrCreate(['name' => $role]);

            foreach ($permissions as $permission) {
                $permissionCreated = Permission::firstOrCreate(['name' => $permission]);
                $roleCreated->givePermissionTo($permissionCreated);
            }
        }

        // Crear usuarios de prueba con sus respectivos roles
        $users = [
            [
                'name' => 'Pastor Principal',
                'email' => 'pastor@example.com',
                'password' => Hash::make('password123'),
                'role' => 'Pastor',
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
                ['email' => $userData['email']], // Verifica si ya existe
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            // Asignar rol si no lo tiene
            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
