<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Horario;
use App\Constants\Status;
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
        // Cargar los ministerios junto con los usuarios relacionados
        $ministerios = $this->commonQuery()->with('usuarios')->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Ministerios Activos';
        $ministerios = $this->commonQuery()->where('estado', Status::ACTIVE)->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Ministerios Inactivos';
        $ministerios = $this->commonQuery()->where('estado', Status::INACTIVE)->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    protected function commonQuery()
    {
        return Ministerio::query()->orderBy('id');
    }

    public function create()
    {
        $pageTitle = 'Nuevo Ministerio';
        // Obtener usuarios que no están asociados a ningún ministerio
        $usuarios = User::whereDoesntHave('ministerios')->get();
        return view('admin.ministerios.create', compact('pageTitle', 'usuarios'));
    }

    public function edit(Ministerio $ministerio)
    {
        $pageTitle = 'Edición de Ministerio: ' . $ministerio->nombre;
        // Recuperar usuarios ó (Líderes) que no están asociados a ningún ministerio
        // o que están asociados al ministerio actual
        $usuarios = User::whereDoesntHave('ministerios')
            ->orWhereHas('ministerios', function ($query) use ($ministerio) {
                $query->where('ministerios.id', $ministerio->id);
            })->get();
        return view('admin.ministerios.edit', compact('ministerio', 'pageTitle', 'usuarios'));
    }

    public function store(Request $request, $id = null)
    {
        $request->validate([
            'user_id' => 'required|array', 
            'user_id.*' => 'exists:users,id', 
            'nombre' => 'required|string|min:3|max:255|unique:ministerios,nombre,' . ($id ? $id : 'NULL') . '|regex:/^[\p{L}\s]+$/u',
            'multa_incremento' => 'required|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tipo' => 'required|integer|min:0|max:1',
        ]);

        try {
            $data = $request->except('_token', 'remove_logo');
            $ministerio = $id ? Ministerio::findOrFail($id) : new Ministerio();
            // 🔹 Sincronizar usuarios seleccionados (líderes)
            $ministerio->usuarios()->sync($request->input('user_id', []));

            // 🔹 Eliminar la imagen solo si el usuario la quitó manualmente
            if ($request->input('remove_logo') == '1') {
                deleteFile($ministerio->logo);
                $data['logo'] = null;
            }

            // 🔹 Si se sube un nuevo logo, procesarlo
            if ($request->hasFile('logo')) {
                deleteFile($ministerio->logo); // Eliminar el anterior antes de guardar el nuevo
                $data['logo'] = uploadFile($request->file('logo'), 'uploads/ministerios');
            }

            $ministerio->fill($data)->save();

            return redirect()->route('admin.ministerios.index')->with('success', $id ? 'Ministerio actualizado correctamente.' : 'Ministerio creado correctamente.');
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
            ->where('estado', Status::ACTIVE)
            ->orderByDesc('id')
            ->get();

        return view('admin.ministerios.horarios', compact('horarios', 'pageTitle'));
    }
}
