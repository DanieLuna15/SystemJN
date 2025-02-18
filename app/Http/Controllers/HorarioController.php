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
        $horarios = $this->commonQuery()->where('estado', Status::ACTIVE)->get();
        return view('admin.horarios.index', compact('horarios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Horarios Inactivos';
        $horarios = $this->commonQuery()->where('estado', Status::INACTIVE)->get();
        return view('admin.horarios.index', compact('horarios', 'pageTitle'));
    }

    protected function commonQuery()
    {
        return Horario::query()->orderBy('id', 'DESC');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Nuevo Horario';
        $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();
        $actividadServicios = ActividadServicio::where('estado', Status::ACTIVE)->get();
        return view('admin.horarios.create', compact('actividadServicios','ministerios', 'pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, $id = null)
    {

        // 📌 Log para ver los datos originales recibidos
        Log::debug('🔹 Datos recibidos en la solicitud:', $request->all());

        // 📌 Asegurar que el formato sea correcto antes de validar
        $request->merge([
            'hora_registro' => !empty($request->hora_registro) ? date('H:i', strtotime($request->hora_registro)) : null,
            'hora_multa' => !empty($request->hora_multa) ? date('H:i', strtotime($request->hora_multa)) : null
        ]);

        // 📌 Log después del formateo
        Log::debug('🔹 Datos después del formateo:', [
            'hora_registro' => $request->hora_registro,
            'hora_multa' => $request->hora_multa
        ]);

        // 📌 Validación asegurando que los valores sean correctos
        $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'actividad_servicio_id' => 'required|exists:actividad_servicios,id',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_registro' => ['required', 'date_format:H:i'],
            'hora_multa' => 'required|date_format:H:i|after:hora_registro',
            'tipo' => 'required|integer|min:0|max:1',
        ]);

        Log::debug('✅ Validación pasada con éxito.');

        try {
            $data = $request->except(['_token']);

            if ($id) {
                $horario = Horario::findOrFail($id);
                Log::debug("🔄 Actualizando horario con ID: $id", $data);
                $horario->update($data);
                $message = 'Horario actualizado correctamente.';
            } else {
                Log::debug('🆕 Creando nuevo horario', $data);
                Horario::create($data);
                $message = 'Horario creado correctamente.';
            }

            return redirect()->route('admin.horarios.index')->with('success', $message);
        } catch (\Exception $e) {
            // 📌 Log para capturar errores y la traza completa
            Log::error('❌ Error en store(): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

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
        return view('admin.horarios.edit', compact('horario', 'ministerios','actividadServicios','pageTitle'));
    }

    public function status($id)
    {
        return Horario::changeStatus($id, 'estado');
    }
}
