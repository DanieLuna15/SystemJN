<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Ministerio;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{
    public function index()
    {
        $pageTitle = 'Todos los Ministerios';
        $ministerios = $this->commonQuery()->get();
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
        return Ministerio::query()->orderBy('id', 'DESC');
    }


    public function create()
    {
        $pageTitle = 'Nuevo Ministerio';
        return view('admin.ministerios.create', compact('pageTitle'));
    }

    public function edit(Ministerio $ministerio)
    {
        $pageTitle = 'Edición de Ministerio: ' . $ministerio->nombre;
        return view('admin.ministerios.edit', compact('ministerio', 'pageTitle'));
    }

    public function store(Request $request, $id = null)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'multa_incremento' => 'required|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tipo' => 'required|integer|min:0|max:1',
        ]);

        try {
            $data = $request->except('_token');
            $ministerio = $id ? Ministerio::findOrFail($id) : new Ministerio();

            // 🔹 Si NO se sube un nuevo logo y ya existía, eliminar el anterior
            if ($id && !$request->hasFile('logo')) {
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
}
