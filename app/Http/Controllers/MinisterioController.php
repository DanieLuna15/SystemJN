<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ministerio;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver ministerios')->only(['index', 'active', 'inactive']);
        $this->middleware('can:crear ministerios')->only(['create', 'store']);
        $this->middleware('can:editar ministerios')->only(['edit', 'store']);
        $this->middleware('can:ver ministerio')->only(['show']);
        $this->middleware('can:eliminar ministerios')->only(['destroy']);
        $this->middleware('can:cambiar estado ministerios')->only(['status']);
        $this->middleware('can:ver horarios_ministerio')->only(['horarios']);
    }

    public function index()
    {
        $pageTitle = 'Todos los Ministerios';
        $ministerios = $this->commonQuery()->with('usuarios')->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Ministerios Activos';
        $ministerios = $this->commonQuery()->active()->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Ministerios Inactivos';
        $ministerios = $this->commonQuery()->inactive()->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    protected function commonQuery()
    {
        return Ministerio::orderBy('id');
    }


    public function create()
    {
        $pageTitle = 'Nuevo Ministerio';
        $lideres = User::role('Líder') // Filtra solo usuarios con el rol de "Líder"
            ->whereDoesntHave('ministeriosLiderados') // Aquellos que no tienen ministerios liderados
            ->get();

        return view('admin.ministerios.create', compact('pageTitle', 'lideres'));
    }

    public function edit(Ministerio $ministerio)
    {
        $pageTitle = 'Edición de Ministerio: ' . $ministerio->nombre;
        $lideres = User::role('Líder') // Filtrar usuarios con el rol de "Líder"
            ->whereDoesntHave('ministeriosLiderados') // Que no lideren ningún ministerio
            ->orWhereHas('ministeriosLiderados', function ($query) use ($ministerio) {
                $query->where('ministerios.id', $ministerio->id); // Que lideren el ministerio actual
            })
            ->get();

        return view('admin.ministerios.edit', compact('ministerio', 'pageTitle', 'lideres'));
    }

    public function store(Request $request, $id = null)
    {
        // Validar los datos de entrada
        $rules = [
            'user_id' => 'required|array', // Requerido como un array
            'user_id.*' => [
                'exists:users,id', // Validar que los usuarios existan en la tabla `users`
                function ($attribute, $value, $fail) use ($id) {
                    // Validar que los usuarios tengan el rol "Líder"
                    if ($id) {
                        // Si es edición, validar según los líderes asociados al ministerio actual o libres
                        $ministerio = Ministerio::findOrFail($id);
                        $lideresPermitidos = User::role('Líder')
                            ->whereDoesntHave('ministeriosLiderados')
                            ->orWhereHas('ministeriosLiderados', function ($query) use ($ministerio) {
                                $query->where('ministerios.id', $ministerio->id);
                            })
                            ->pluck('id')
                            ->toArray();

                        if (!in_array($value, $lideresPermitidos)) {
                            $fail("El usuario seleccionado ($value) no está permitido como líder en este ministerio.");
                        }
                    } else {
                        // Validar solo líderes no asociados a ministerios en la creación
                        $liderLibre = User::role('Líder')
                            ->whereDoesntHave('ministeriosLiderados')
                            ->where('id', $value)
                            ->exists();

                        if (!$liderLibre) {
                            $fail("El usuario seleccionado ($value) ya está liderando otro ministerio.");
                        }
                    }
                }
            ],
            'nombre' => 'required|string|min:3|max:255|unique:ministerios,nombre,' . ($id ? $id : 'NULL') . '|regex:/^[\p{L}\s]+$/u',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $messages = [
            'user_id.required' => 'Debe seleccionar al menos un Líder.',
            'user_id.*.exists' => 'Uno o más Líderes seleccionados no son válidos.',
        ];

        // Validar los datos
        $validatedData = $request->validate($rules, $messages);

        // Recoger los datos excluyendo `_token` y `remove_logo`
        $data = $request->except('_token', 'remove_logo');

        try {
            if ($id) {
                // 📌 Si se trata de una edición, buscar el ministerio y actualizarlo
                $ministerio = Ministerio::findOrFail($id);

                // Sincronizar los líderes seleccionados
                $ministerio->lideres()->sync($request->user_id);

                // Eliminar logo si el usuario lo desea
                if ($request->input('remove_logo') == '1') {
                    deleteFile($ministerio->logo); // Eliminar el logo anterior
                    $data['logo'] = null;
                }

                // Subir el nuevo logo si se ha proporcionado uno
                if ($request->hasFile('logo')) {
                    deleteFile($ministerio->logo); // Eliminar el logo anterior
                    $data['logo'] = uploadFile($request->file('logo'), 'uploads/ministerios');
                }

                // Actualizar los datos del ministerio
                $ministerio->update($data);

                $message = 'Ministerio actualizado correctamente.';
            } else {
                // Crear el ministerio
                $ministerio = Ministerio::create($data);

                // Asociar los líderes seleccionados
                $ministerio->lideres()->attach($request->user_id);

                // Subir el nuevo logo si se ha proporcionado uno
                if ($request->hasFile('logo')) {
                    $data['logo'] = uploadFile($request->file('logo'), 'uploads/ministerios');
                    $ministerio->update(['logo' => $data['logo']]);
                }

                $message = 'Ministerio creado correctamente.';
            }

            // Redirigir con mensaje de éxito
            return redirect()->route('admin.ministerios.index')->with('success', $message);
        } catch (\Exception $e) {
            // Capturar errores y redirigir con mensaje de error
            return redirect()->route('admin.ministerios.index')->with('error', 'Hubo un error en la operación: ' . $e->getMessage());
        }
    }


    public function status($id)
    {
        return Ministerio::changeStatus($id, 'estado');
    }

    public function horarios(Ministerio $ministerio)
    {
        $ministerioId = $ministerio->id;

        $pageTitle = 'Todos los horarios del ministerio: ' . $ministerio->nombre;

        $horarios = $ministerio->horarios()
            ->orderByDesc('id')
            ->get();

        $permisos = Permiso::whereHas('usuarios', function ($query) use ($usuarioId) {
            $query->where('usuario_id', $usuarioId);  // Filtra por el ministerio específico
        })
            ->orderByDesc('id')
            ->get();

        return view('admin.ministerios.horarios', compact('horarios', 'pageTitle','excepciones'));
    }
}
