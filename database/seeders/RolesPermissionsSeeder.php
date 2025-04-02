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
                // 📌 Reportes y Dashboard
                'ver reportes',
                'ver dashboard',

                // 📌 Gestión de Usuarios
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

                // 📌 Gestión de Ministerios
                'crear ministerios',
                'ver ministerios',
                'cambiar estado ministerios',
                'editar ministerios',
                'eliminar ministerios',
                'ver horarios_ministerio',

                // 📌 Gestión de Horarios
                'crear horarios',
                'ver horarios',
                'ver horario',
                'cambiar estado horarios',
                'editar horarios',
                'eliminar horarios',

                // 📌 Gestión de Actividades y Servicios
                'crear actividades_servicios',
                'ver actividades_servicios',
                'ver actividad_servicio',
                'cambiar estado actividades_servicios',
                'editar actividades_servicios',
                'eliminar actividades_servicios',

                // 📌 Gestión de Roles
                'crear roles',
                'ver roles',
                'ver rol',
                'cambiar estado roles',
                'editar roles',
                'eliminar roles',

                // 📌 Configuración del Sistema
                'editar configuracion informacion',
                'editar configuracion imagenes',
                'ver configuracion',

                // 📌 Gestión de Permisos (Nuevo)
                'crear permisos',
                'ver permisos',
                'ver permiso',
                'editar permisos',
                'cambiar estado permisos',
                'eliminar permisos',
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
