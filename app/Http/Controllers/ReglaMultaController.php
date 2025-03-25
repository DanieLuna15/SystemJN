<?php

namespace App\Http\Controllers;
use App\Models\ReglaMulta;
use App\Models\Ministerio;
use App\Constants\Status;

use Illuminate\Http\Request;

class ReglaMultaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Todas las Reglas Multas';
        $reglas_multas = $this->commonQuery()->get();
        return view('admin.reglas_multas.index', compact('reglas_multas', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Reglas Multas Activas';
        $reglas_multas = $this->commonQuery()->active()->get();
        return view('admin.reglas_multas.index', compact('reglas_multas', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Reglas Multas Inactivas';
        $reglas_multas = $this->commonQuery()->inactive()->get();
        return view('admin.reglas_multas.index', compact('reglas_multas', 'pageTitle'));
    }

    protected function commonQuery()
    {
        return ReglaMulta::orderByDesc('id');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Nueva Regla Multa';
        $ministerios = Ministerio::whereDoesntHave('reglasMultas')->get();
        return view('admin.reglas_multas.create', compact('ministerios', 'pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id = null)
    {
        // Validar los datos de entrada
        $rules = [
            'descripcion' => 'nullable|string|max:255',
            'multa_por_falta' => 'required|numeric|min:0',
            'minutos_por_incremento' => 'required|integer|min:0',
            'multa_incremental' => 'required|numeric|min:0',
            'minutos_retraso_largo' => 'required|integer|min:0',
            'multa_por_retraso_largo' => 'required|numeric|min:0',
            'ministerio_id' => 'nullable|array', // Los ministerios seleccionados
            'ministerio_id.*' => 'exists:ministerios,id', // Validar que cada ministerio exista
        ];

        // Validar los datos
        $validatedData = $request->validate($rules);

        try {
            // 📌 Extraer datos, excluyendo el token y los IDs de ministerios
            $data = $request->except(['_token', 'ministerio_id']);

            // Recuperar la lógica de validación de ministerios permitidos según creación o edición
            if ($id) {
                // Obtener la regla actual
                $reglaMulta = ReglaMulta::findOrFail($id);

                // Ministerios permitidos: aquellos que no tienen reglas o que están asociados a esta regla
                $ministeriosPermitidos = Ministerio::whereDoesntHave('reglasMultas')
                    ->orWhereHas('reglasMultas', function ($query) use ($reglaMulta) {
                        $query->where('reglas_multas.id', $reglaMulta->id);
                    })->pluck('id')->toArray();
            } else {
                // Ministerios permitidos: aquellos que no tienen ninguna regla de multa
                $ministeriosPermitidos = Ministerio::whereDoesntHave('reglasMultas')->pluck('id')->toArray();
            }

            // Validar que los ministerios seleccionados sean válidos
            if ($request->filled('ministerio_id')) {
                foreach ($request->ministerio_id as $ministerioId) {
                    if (!in_array($ministerioId, $ministeriosPermitidos)) {
                        return redirect()->back()
                            ->withErrors(['ministerio_id' => 'Uno o más ministerios seleccionados no son válidos.'])
                            ->withInput();
                    }
                }
            }

            if ($id) {
                // Actualizar una regla existente
                $reglaMulta->update($data); // Actualizar los datos principales

                // Sincronizar ministerios seleccionados
                if ($request->filled('ministerio_id')) {
                    $reglaMulta->ministerios()->sync($request->ministerio_id); // Mantiene ministerios actualizados
                }

                $message = 'Regla de multa actualizada correctamente.';
            } else {
                // Crear una nueva regla de multa
                $reglaMulta = ReglaMulta::create($data);

                // Asociar ministerios seleccionados si se envían
                if ($request->filled('ministerio_id')) {
                    $reglaMulta->ministerios()->attach($request->ministerio_id); // Guarda la nueva asociación
                }

                $message = 'Regla de multa creada correctamente.';
            }

            // Redirigir con mensaje de éxito
            return redirect()->route('admin.reglas_multas.index')->with('success', $message);
        } catch (\Exception $e) {
            // Capturar errores y redirigir con mensaje de error
            return redirect()->route('admin.reglas_multas.index')->with('error', 'Hubo un error en la operación: ' . $e->getMessage());
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
    public function edit(ReglaMulta $regla_multa)
    {
        $pageTitle = 'Edición de Regla Multa';
        $ministerios = Ministerio::whereDoesntHave('reglasMultas')
            ->orWhereHas('reglasMultas', function ($query) use ($regla_multa) {
                $query->where('reglas_multas.id', $regla_multa->id);
            })->get();
        return view('admin.reglas_multas.edit', compact('regla_multa', 'ministerios', 'pageTitle'));
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

    public function status($id)
    {
        return ReglaMulta::changeStatus($id, 'estado');
    }
}
