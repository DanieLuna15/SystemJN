<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    public function run()
    {
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

        foreach ($rolesPermissions as $role => $permissions) {
            $roleCreated = Role::firstOrCreate(['name' => $role]);

            foreach ($permissions as $permission) {
                $permissionCreated = Permission::firstOrCreate(['name' => $permission]);
                $roleCreated->givePermissionTo($permissionCreated);
            }
        }
    }
}
