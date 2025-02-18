<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use Illuminate\Http\Request;
use App\Models\ActividadServicio;

class ActividadServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $pageTitle = 'Todas las Actividades y Servicios';
        $actividad_servicios = $this->commonQuery()->get();
        return view('admin.actividad_servicios.index', compact('actividad_servicios', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Actividades y Servicios Activos';
        $actividad_servicios = $this->commonQuery()->where('estado', Status::ACTIVE)->get();
        return view('admin.actividad_servicios.index', compact('actividad_servicios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Actividades y Servicios Inactivos';
        $actividad_servicios = $this->commonQuery()->where('estado', Status::INACTIVE)->get();
        return view('admin.actividad_servicios.index', compact('actividad_servicios', 'pageTitle'));
    }


    protected function commonQuery()
    {
        return ActividadServicio::query()->orderBy('id', 'DESC');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Nueva Actividad o Servicio';
        return view('admin.actividad_servicios.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id = null)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->except('_token');
            $ministerio = $id ? ActividadServicio::findOrFail($id) : new ActividadServicio();

            // 🔹 Si NO se sube un nueva imagen y ya existía, eliminar el anterior
            if ($id && !$request->hasFile('imagen')) {
                deleteFile($ministerio->imagen);
                $data['imagen'] = null;
            }

            // 🔹 Si se sube un nueva imagen, procesarlo
            if ($request->hasFile('imagen')) {
                deleteFile($ministerio->imagen); // Eliminar el anterior antes de guardar el nuevo
                $data['imagen'] = uploadFile($request->file('imagen'), 'uploads/actividad_servicios');
            }


            $ministerio->fill($data)->save();

            return redirect()->route('admin.actividad_servicios.index')->with('success', $id ? 'Act. o Servicio actualizado correctamente.' : 'Act. o Servicio creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.actividad_servicios.index')->with('error', 'Hubo un error en la operación.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ActividadServicio $actividadServicio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActividadServicio $actividadServicio)
    {
        $pageTitle = 'Edición de Actividad o Servicio: '. $actividadServicio->nombre;
        return view('admin.actividad_servicios.edit', compact('actividadServicio','pageTitle'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActividadServicio $actividadServicio)
    {
        //
    }

    public function status($id)
    {
        return ActividadServicio::changeStatus($id, 'estado');
    }
}
