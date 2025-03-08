<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use Illuminate\Http\Request;
use App\Models\ActividadServicio;

class ActividadServicioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver actividades_servicios')->only(['index', 'active', 'inactive']);
        $this->middleware('can:crear actividades_servicios')->only(['create', 'store']);
        $this->middleware('can:editar actividades_servicios')->only(['edit', 'store']);
        $this->middleware('can:ver actividad_servicio')->only(['show']);
        $this->middleware('can:eliminar actividades_servicios')->only(['destroy']);
        $this->middleware('can:cambiar estado actividades_servicios')->only(['status']);
    }
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
        return ActividadServicio::query()->orderBy('id');
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
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'unique:actividad_servicios,nombre',
                'regex:/^[\p{L}\s]+$/u'
            ],
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);        

        try {
            $data = $request->except('_token', 'remove_logo');
            $actividadServicio = $id ? ActividadServicio::findOrFail($id) : new ActividadServicio();

            // 🔹 Eliminar la imagen solo si el usuario la quitó manualmente
            if ($request->input('remove_logo') == '1') {
                deleteFile($actividadServicio->imagen);
                $data['imagen'] = null;
            }
            
            // 🔹 Si se sube un nueva imagen, procesarlo
            if ($request->hasFile('imagen')) {
                deleteFile($actividadServicio->imagen); // Eliminar el anterior antes de guardar el nuevo
                $data['imagen'] = uploadFile($request->file('imagen'), 'uploads/actividad_servicios');
            }


            $actividadServicio->fill($data)->save();

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
        $pageTitle = 'Edición de Actividad o Servicio: ' . $actividadServicio->nombre;
        return view('admin.actividad_servicios.edit', compact('actividadServicio', 'pageTitle'));
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
