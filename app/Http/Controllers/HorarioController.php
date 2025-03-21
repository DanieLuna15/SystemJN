<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Constants\Status;
use App\Models\Ministerio;
use App\Models\ActividadServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver horarios')->only(['index', 'active', 'inactive']);
        $this->middleware('can:crear horarios')->only(['create', 'store']);
        $this->middleware('can:editar horarios')->only(['edit', 'store']);
        $this->middleware('can:ver horario')->only(['show']);
        $this->middleware('can:eliminar horarios')->only(['destroy']);
        $this->middleware('can:cambiar estado horarios')->only(['status']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Todos los Horarios';
        $horarios = $this->commonQuery()->get();
        return view('admin.horarios.index', compact('horarios', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Horarios Activos';
        $horarios = $this->commonQuery()->active()->get();
        return view('admin.horarios.index', compact('horarios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Horarios Inactivos';
        $horarios = $this->commonQuery()->inactive()->get();
        return view('admin.horarios.index', compact('horarios', 'pageTitle'));
    }

    protected function commonQuery()
    {
        return Horario::orderByDesc('id');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Nuevo Horario';
        $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();
        $actividadServicios = ActividadServicio::where('estado', Status::ACTIVE)->get();
        return view('admin.horarios.create', compact('actividadServicios', 'ministerios', 'pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, $id = null)
    {

        // 📌 Asegurar que el formato sea correcto antes de validar
        $request->merge([
            'hora_registro' => !empty($request->hora_registro) ? date('H:i:s', strtotime($request->hora_registro)) : null,
            'hora_multa' => !empty($request->hora_multa) ? date('H:i:s', strtotime($request->hora_multa)) : null
        ]);

        if ($request->tipo == 1) { // Si es tipo fijo
            $request->merge(['fecha' => null]);
        } elseif ($request->tipo == 0) { // Si es tipo eventual
            $request->merge(['dia_semana' => null]);
        }

        $rules = [
            'ministerio_id' => 'required|array',
            'ministerio_id.*' => 'exists:ministerios,id', 
            'actividad_servicio_id' => 'required|exists:actividad_servicios,id',
            'hora_registro' => ['required', 'date_format:H:i:s'],
            'hora_multa' => 'required|date_format:H:i:s|after:hora_registro',
            'tipo' => 'required|integer|min:0|max:1',
            'dia_semana' => $request->tipo == 1 ? 'required|integer|min:0|max:6' : 'nullable',
            'fecha' => $request->tipo == 0 ? 'required|date|after_or_equal:today' : 'nullable',
        ];

        $request->validate($rules);

        try {
            // 📌 Extraer datos sin el _token y guardar en la base de datos
            $data = $request->except(['_token', 'ministerio_id']);

            if ($id) {
                $horario = Horario::findOrFail($id);
            
                $horario->update($data);
                $horario->ministerios()->sync($request->ministerio_id); // Sincroniza ministerios seleccionados
                $message = 'Horario actualizado correctamente.';
            } else {
            
                $horario = Horario::create($data);
                $horario->ministerios()->attach($request->ministerio_id); // Guarda la relación muchos a muchos
                $message = 'Horario creado correctamente.';
            }

            return redirect()->route('admin.horarios.index')->with('success', $message);
        } catch (\Exception $e) {

            return redirect()->route('admin.horarios.index')->with('error', 'Hubo un error en la operación.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Horario $horario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Horario $horario)
    {
        $pageTitle = 'Edición de Horario';
        $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();
        $actividadServicios = ActividadServicio::where('estado', Status::ACTIVE)->get();
        return view('admin.horarios.edit', compact('horario', 'ministerios', 'actividadServicios', 'pageTitle'));
    }

    public function status($id)
    {
        return Horario::changeStatus($id, 'estado');
    }
}
