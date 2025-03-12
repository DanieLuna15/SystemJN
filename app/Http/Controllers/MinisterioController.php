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
        // Obtener usuarios que no están asociados a ningún ministerio
        $lideres = User::whereDoesntHave('ministeriosLiderados')->get();
        return view('admin.ministerios.create', compact('pageTitle', 'lideres'));
    }

    public function edit(Ministerio $ministerio)
    {
        $pageTitle = 'Edición de Ministerio: ' . $ministerio->nombre;
        // Recuperar usuarios ó (Líderes) que no están asociados a ningún ministerio
        // o que están asociados al ministerio actual
        $lideres = User::whereDoesntHave('ministeriosLiderados')
            ->orWhereHas('ministeriosLiderados', function ($query) use ($ministerio) {
                $query->where('ministerios.id', $ministerio->id);
            })->get();
        return view('admin.ministerios.edit', compact('ministerio', 'pageTitle', 'lideres'));
    }

    public function store(Request $request, $id = null)
    {
        // Validación de datos
        $request->validate([
            'user_id' => 'required|array',
            'user_id.*' => 'exists:users,id',
            'nombre' => 'required|string|min:3|max:255|unique:ministerios,nombre,' . ($id ? $id : 'NULL') . '|regex:/^[\p{L}\s]+$/u',
            'multa_incremento' => 'required|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tipo' => 'required|integer|min:0|max:1',
        ]);

        // Recoger los datos
        $data = $request->except('_token', 'remove_logo');
        try {
            if ($id) {
                // 📌 Si se trata de una edición, buscar el ministerio y actualizarlo
                $ministerio = Ministerio::findOrFail($id);

                // Sincronizar los líderes seleccionados
                $ministerio->lideres()->sync($request->user_id);

                // Eliminar logo si el usuario lo desea
                if ($request->input('remove_logo') == '1') {
                    deleteFile($ministerio->logo);  // Eliminar el logo anterior
                    $data['logo'] = null;
                }

                // Subir el nuevo logo si se ha proporcionado uno
                if ($request->hasFile('logo')) {
                    deleteFile($ministerio->logo);  // Eliminar el logo anterior
                    $data['logo'] = uploadFile($request->file('logo'), 'uploads/ministerios');
                }

                // Actualizar los datos del ministerio
                $ministerio->update($data);

                $message = 'Ministerio actualizado correctamente.';
            } else {
                // Crear el ministerio
                $ministerio = Ministerio::create($data);

                // Sincronizar los líderes seleccionados
                $ministerio->lideres()->attach($request->user_id);

                // Subir el nuevo logo si se ha proporcionado uno
                if ($request->hasFile('logo')) {
                    $data['logo'] = uploadFile($request->file('logo'), 'uploads/ministerios');
                    $ministerio->update(['logo' => $data['logo']]);
                }

                $message = 'Ministerio creado correctamente.';
            }
            return redirect()->route('admin.ministerios.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.ministerios.index')->with('error', 'Hubo un error en la operación.');
        }
    }

    public function status($id)
    {
        return Ministerio::changeStatus($id, 'estado');
    }

    public function horarios(Ministerio $ministerio)
    {
        $pageTitle = 'Todos los horarios del ministerio: ' . $ministerio->nombre;

        $horarios = $ministerio->horarios()
            ->orderByDesc('id')
            ->get();

        return view('admin.ministerios.horarios', compact('horarios', 'pageTitle'));
    }
}
