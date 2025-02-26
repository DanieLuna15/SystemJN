<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $pageTitle = 'Toda la Configuración del sistema';
    //     return view('admin.configuraciones.index', compact('pageTitle'));
    // }

    public function index()
    {
        $pageTitle = 'Toda la Configuración del sistema';
        $configuracion = Configuracion::first(); // Obtiene la primera configuración de la base de datos
        return view('admin.configuraciones.index', compact('pageTitle', 'configuracion'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Configuracion $configuracion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Configuracion $configuracion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'descripcion' => 'nullable|string',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|min:3|max:10',
            'url' => 'nullable|url|max:255',
        ]);

        // Encontrar y actualizar la configuración existente en una sola línea
        Configuracion::where('id', $id)->update($validatedData);

        // Redireccionar a la página de configuraciones con un mensaje de éxito
        return redirect()->route('admin.configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }

    public function update_logo(Request $request, $id)
    {
        //dd($request->all());
        // 🔹 Validar los datos del formulario
        $validatedData = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'loader' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 🔹 Obtener la configuración existente
        $configuracion = Configuracion::findOrFail($id);

        // 🔹 Eliminar imágenes si el usuario las quitó manualmente
        if ($request->input('remove_logo') == '1') {
            deleteFile($configuracion->logo);
            $configuracion->logo = null;
        }

        if ($request->input('remove_favicon') == '1') {
            deleteFile($configuracion->favicon);
            $configuracion->favicon = null;
        }

        if ($request->input('remove_loader') == '1') {
            deleteFile($configuracion->loader);
            $configuracion->loader = null;
        }

        // 🔹 Subir nuevas imágenes si se adjuntaron
        if (isset($validatedData['logo'])) {
            deleteFile($configuracion->logo);
            $configuracion->logo = uploadFile($validatedData['logo'], 'uploads/configuraciones/logo');
        }

        if (isset($validatedData['favicon'])) {
            deleteFile($configuracion->favicon);
            $configuracion->favicon = uploadFile($validatedData['favicon'], 'uploads/configuraciones/favicon');
        }

        if (isset($validatedData['loader'])) {
            deleteFile($configuracion->loader);
            $configuracion->loader = uploadFile($validatedData['loader'], 'uploads/configuraciones/loader');
        }

        // 🔹 Guardar los cambios en la base de datos
        $configuracion->save();

        // 🔹 Redireccionar con mensaje de éxito
        return redirect()->route('admin.configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Configuracion $configuracion)
    {
        //
    }
}
