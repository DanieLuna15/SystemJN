<?php

namespace App\Http\Controllers;

use App\Models\Excepcion;
use App\Constants\Status;
use App\Models\Ministerio;
use Illuminate\Http\Request;

class ExcepcionController extends Controller
{

    public function __construct()
{
    $this->middleware('auth');
    $this->middleware('can:ver excepciones')->only(['index']);
    $this->middleware('can:crear excepciones')->only(['create', 'store']);
    $this->middleware('can:editar excepciones')->only(['edit', 'store']);
    $this->middleware('can:ver excepcion')->only(['show']);
    $this->middleware('can:eliminar excepciones')->only(['destroy']);
    $this->middleware('can:cambiar estado excepciones')->only(['status']);
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Todas las Excepciones';
        $excepciones = $this->commonQuery()->get();
        return view('admin.excepciones.index', compact('excepciones', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Nueva excepcion';
        $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();
        return view('admin.excepciones.create', compact('ministerios', 'pageTitle'));
    }
    /**
     * Returns a query builder instance for the Excepcion model, ordered by descending ID.
     */

    protected function commonQuery()
    {
        return Excepcion::with('usuario') // Cargar la relación con el usuario
            ->orderByDesc('id');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id = null)
    {
        // Asegurar el formato correcto antes de validar
        $request->merge([
            'hora_inicio' => !empty($request->hora_inicio) ? date('H:i', strtotime($request->hora_inicio)) : null,
            'hora_fin' => !empty($request->hora_fin) ? date('H:i', strtotime($request->hora_fin)) : null,
            'dia_entero' => $request->tipo, // Mapear el valor de 'tipo' al campo 'dia_entero'
        ]);

        // Ajustar lógica para el tipo de excepción
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
            'ministerio_id' => 'required|array',
            'ministerio_id.*' => 'exists:ministerios,id',
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
            $data = $request->except(['_token', 'ministerio_id', 'tipo']);
            $data['usuario_id'] = auth()->id(); // Asignar el usuario autenticado

            if ($id) {
                $excepcion = Excepcion::findOrFail($id);
                $excepcion->update($data);
                $excepcion->ministerios()->sync($request->ministerio_id); // Sincroniza ministerios
                $message = 'Excepción actualizada correctamente.';
            } else {
                $excepcion = Excepcion::create($data);
                $excepcion->ministerios()->attach($request->ministerio_id); // Relación muchos a muchos
                $message = 'Excepción creada correctamente.';
            }

            return redirect()->route('admin.excepciones.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.excepciones.index')->with('error', 'Hubo un error en la operación.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Excepcion $excepcion)
    {
        $pageTitle = 'Edición de Excepcion';
        $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();
        return view('admin.excepciones.edit', compact('excepcion', 'ministerios', 'pageTitle'));
    }

    public function status($id)
    {
        return Excepcion::changeStatus($id, 'estado');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
