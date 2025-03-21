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
                'ver reportes',
                'ver dashboard',
                //------------------------
                'crear usuarios',
                'ver usuarios',
                'ver info_usuario',
                'editar usuarios',
                'cambiar estado usuarios',
                'eliminar usuarios',
                'ver perfil',
                'editar perfil_informacion',
                'editar perfil_imagen',
                'editar perfil_contraseña',
                //------------------------
                'crear ministerios',
                'ver ministerios',
                'cambiar estado ministerios',
                'editar ministerios',
                'eliminar ministerios',
                'ver horarios_ministerio',
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
                'editar configuracion informacion',
                'editar configuracion imagenes',
                'ver configuracion',
            ],
            'Líder' => [
                'ver dashboard',
                'ver horarios',
                'ver actividades_servicios',
                'ver ministerios',
                'ver horarios_ministerio',
                'ver perfil',
            ],
            'Servidor' => [
                'ver dashboard',
                'ver horarios',
                'ver actividades_servicios',
                'ver perfil',
            ],
            'Miembro' => [
                'ver dashboard',
                'ver perfil',
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
