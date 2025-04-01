<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Constants\Status;
use App\Models\User;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    /**
     * Lista todos los permisos.
     */
    public function index()
    {
        $pageTitle = 'Todos los Permisos';
        $permisos = Permiso::with('usuario')->orderByDesc('id')->get();
        return view('admin.permisos.index', compact('permisos', 'pageTitle'));
    }

    /**
     * Muestra el formulario para crear un permiso.
     */
    public function create()
    {
        $pageTitle = 'Nuevo Permiso';
        $usuarios = User::has('ministerios')->get();
        return view('admin.permisos.create', compact('usuarios', 'pageTitle'));
    }

    /**
     * Guarda un nuevo permiso o actualiza uno existente.
     */
    public function store(Request $request, $id = null)
    {
        // Asegurar el formato correcto antes de validar
        $request->merge([
            'hora_inicio' => !empty($request->hora_inicio) ? date('H:i', strtotime($request->hora_inicio)) : null,
            'hora_fin' => !empty($request->hora_fin) ? date('H:i', strtotime($request->hora_fin)) : null,
            'dia_entero' => $request->tipo, // Mapear el valor de 'tipo' al campo 'dia_entero'
        ]);

        // Ajustar lógica para el tipo de permiso
        switch ($request->tipo) {
            case 1: // Todo el día
                $request->merge([
                    'hora_inicio' => null,
                    'hora_fin' => null,
                    'hasta' => null,
                ]);
                break;
            case 0: // Rango de horas
                $request->merge(['hasta' => null]);
                break;
            case 2: // Varios días
                $request->merge(['hora_inicio' => null, 'hora_fin' => null]);
                break;
        }

        // Reglas de validación
        $rules = [
            'usuario_id' => 'required|array',
            'usuario_id.*' => 'exists:users,id',
            'fecha' => 'required|date',
            'hasta' => $request->tipo == 2 ? 'required|date|after_or_equal:fecha' : 'nullable',
            'hora_inicio' => $request->tipo == 0 ? 'required|date_format:H:i' : 'nullable',
            'hora_fin' => $request->tipo == 0 ? 'required|date_format:H:i|after:hora_inicio' : 'nullable',
            'tipo' => 'required|integer|min:0|max:2',
            'motivo' => 'required|string|max:255',
        ];

        $request->validate($rules);

        try {
            // Extraer datos y guardar en la base de datos
            $data = $request->except(['_token', 'usuario_id', 'tipo']);
            $data['user_id'] = auth()->id(); // Asignar el usuario autenticado

            if ($id) {
                $permiso = Permiso::findOrFail($id);
                $permiso->update($data);
                $permiso->usuarios()->sync($request->usuario_id); // Sincroniza usuarios
                $message = 'Permiso actualizado correctamente.';
            } else {
                $permiso = Permiso::create($data);
                $permiso->usuarios()->attach($request->usuario_id); // Relación muchos a muchos
                $message = 'Permiso creado correctamente.';
            }

            return redirect()->route('admin.permisos.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.permisos.index')->with('error', 'Hubo un error en la operación.');
        }
    }


    /**
     * Edita un permiso.
     */
    public function edit(Permiso $permiso)
    {
        $pageTitle = 'Edición de Permiso';
        $usuarios = User::has('ministerios')->get();
        return view('admin.permisos.edit', compact('permiso', 'usuarios', 'pageTitle'));
    }

    /**
     * Cambia el estado de un permiso.
     */
    public function status($id)
    {
        $permiso = Permiso::findOrFail($id);

        // Alternar entre los estados: Pendiente -> Autorizado -> Rechazado -> Pendiente
        switch ($permiso->estado) {
            case 0:
                $permiso->estado = 1; // Cambiar a Autorizado
                break;
            case 1:
                $permiso->estado = 2; // Cambiar a Rechazado
                break;
            case 2:
                $permiso->estado = 0; // Volver a Pendiente
                break;
        }

        $permiso->save();

        return redirect()->back()->with('success', 'Estado del permiso actualizado correctamente.');
    }

}
