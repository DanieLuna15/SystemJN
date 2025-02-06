<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{
    public function index()
    {
        $ministerios = Ministerio::all();
        return view('admin.ministerios.index', compact('ministerios'));
    }

    public function create()
    {
        return view('admin.ministerios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'multa_incremento' => 'required|numeric|min:0',
            'hora_tolerancia' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar imagen
        ]);
    
        $data = $request->all();
    
        // Guardar imagen si se sube una
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/ministerios/'), $filename);
            $data['logo'] = 'uploads/ministerios/' . $filename;
        }
    
        Ministerio::create($data);
    
        return redirect()->route('admin.ministerios.index')->with('success', 'Ministerio creado correctamente.');
    }
    

    public function edit(Ministerio $ministerio)
    {
        return view('ministerios.edit', compact('ministerio'));
    }

    public function update(Request $request, Ministerio $ministerio)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'multa_incremento' => 'required|numeric|min:0',
            'minutos_tolerancia' => 'required|integer|min:0',
        ]);

        $ministerio->update($request->all());

        return redirect()->route('admin.ministerios.index')->with('success', 'Ministerio actualizado correctamente.');
    }

    public function destroy(Ministerio $ministerio)
    {
        $ministerio->delete();
        return redirect()->route('admin.ministerios.index')->with('success', 'Ministerio eliminado correctamente.');
    }
}

