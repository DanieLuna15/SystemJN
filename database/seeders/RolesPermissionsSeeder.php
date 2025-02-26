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
            'Pastor/Admin' => [
                'gestionar configuracion del sistema',
                'ver reportes generales',
                //------------------------
                'crear usuarios',
                'ver usuarios',
                'editar usuarios',
                'cambiar estado usuarios',
                'eliminar usuarios',
                //------------------------
                'crear ministerios',
                'ver ministerios',
                'cambiar estado ministerios',
                'editar ministerios',
                'eliminar ministerios',
                //------------------------
                'crear horarios',
                'ver horarios',
                'cambiar estado horarios',
                'editar horarios',
                'eliminar horarios',
                //------------------------
                'crear actividades_servicios',
                'ver actividades_servicios',
                'cambiar estado actividades_servicios',
                'editar actividades_servicios',
                'eliminar actividades_servicios',
                //------------------------
                'crear roles',
                'ver roles',
                'cambiar estado roles',
                'editar roles',
                'eliminar roles',
                //------------------------
                'editar configuracion',
                'ver configuracion',
            ],
            'Líder' => [
                'ver horarios',
                'ver actividades_servicios',
                'ver ministerios',
            ],
            'Miembro' => [
                'ver horarios',
                'ver actividades_servicios',
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
