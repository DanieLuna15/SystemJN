<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ImportacionAsistencia;

class ImportController extends Controller
{
    public function archivoDB(Request $request)
    {
        $pageTitle = 'Importar Información';
        
        try {
            if ($request->isMethod('post')) {
                // Procesar el archivo y obtener la ruta
                $ruta = $this->procesarArchivo($request);
                if (!$ruta) {
                    return response()->json(['error' => 'Error al procesar el archivo.'], 400);
                }
    
                // Obtener los usuarios con CI válido
                $usuarios = User::whereNotNull('ci')->pluck('id', 'ci');
    
                // Obtener registros de asistencia desde SQLite
                $punches = $this->obtenerRegistrosAsistencia($usuarios->keys());
    
                // Insertar asistencias nuevas
                $nuevasAsistencias = $this->insertarAsistencias($punches);
    
                // Guardar el registro de importación
                ImportacionAsistencia::create([
                    'archivo' => basename($ruta),
                    'ruta' => $ruta,
                    'usuario_id' => auth()->id(),
                    'estado' => 'procesado',
                ]);
    
                return response()->json([
                    'success' => "Archivo DB importado correctamente.",
                    'nuevas_asistencias' => $nuevasAsistencias,
                ], 200);
            }
    
            // Obtener la última importación
            $ultimaImportacion = ImportacionAsistencia::latest('created_at')->first();
            return view('admin.imports.archivoDB', compact('pageTitle', 'ultimaImportacion'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación.',
                'details' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    /**
     * Valida, guarda el archivo en la carpeta database y devuelve la ruta.
     */
    private function procesarArchivo(Request $request)
    {
        $validatedData = $request->validate([
            'archivo' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    if ($value->getClientOriginalExtension() !== 'db') {
                        $fail('El archivo debe tener la extensión .db.');
                    }
                },
                'max:10240', // 10 MB
            ],
        ]);
    
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = $archivo->getClientOriginalName();
            $ruta = base_path('database/' . $nombreArchivo);
            $archivo->move(base_path('database/'), $nombreArchivo);
            return $ruta;
        }
    
        return null;
    }
    
    /**
     * Obtiene los registros de asistencia desde la base de datos SQLite.
     */
    private function obtenerRegistrosAsistencia($carnets)
    {
        return DB::connection('sqlite')
            ->table('att_punches as ap')
            ->join('hr_employee as he', 'ap.emp_id', '=', 'he.id')
            ->select('he.emp_pin as ci', 'ap.punch_time')
            ->whereIn('he.emp_pin', $carnets)
            ->get();
    }
    
    /**
     * Inserta las asistencias nuevas en la base de datos.
     */
    private function insertarAsistencias($punches)
    {
        $asistenciasNuevas = [];
    
        foreach ($punches as $registro) {
            $ci = $registro->ci;
            $punchTime = $registro->punch_time;
    
            // Separar fecha y hora
            $fecha = date('Y-m-d', strtotime($punchTime));
            $hora = date('H:i:s', strtotime($punchTime));
    
            // Validar si la asistencia ya existe
            $existe = Asistencia::where('ci', $ci)
                ->where('fecha', $fecha)
                ->where('hora_marcacion', $hora)
                ->exists();
    
            if (!$existe) {
                $asistenciasNuevas[] = [
                    'ci' => $ci,
                    'fecha' => $fecha,
                    'hora_marcacion' => $hora,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
    
        // Insertar en la base de datos si hay registros nuevos
        if (!empty($asistenciasNuevas)) {
            Asistencia::insert($asistenciasNuevas);
        }
    
        return count($asistenciasNuevas);
    }
}
