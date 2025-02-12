<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Horario;
use App\Models\Ministerio;
use Illuminate\Http\Request;

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
        $ministerios = Ministerio::active()->get();
        return view('admin.horarios.create', compact('ministerios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id = null)
    {
        $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_multas' => 'required|date_format:H:i|after:hora_inicio',
            'estado' => 'required|boolean',
        ]);

        try {
            $data = $request->except(['_token']);

            if ($id) {
                $horario = Horario::findOrFail($id);
                $horario->update($data);
                $message = 'Horario actualizado correctamente.';
            } else {
                Horario::create($data);
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
        $ministerios = Ministerio::active()->get();
        return view('admin.horarios.edit', compact('horario', 'ministerios'));
    }

    public function status($id)
    {
        return Horario::changeStatus($id, 'estado');
    }
}
