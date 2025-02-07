<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{
    public function index()
    {
        $pageTitle = 'Todos los Ministerios';
        $ministerios = Ministerio::all();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        try {
            $data = $request->all();
    
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/ministerios/'), $filename);
                $data['logo'] = 'uploads/ministerios/' . $filename;
            }
    
            Ministerio::create($data);
    
            return redirect()->route('admin.ministerios.index')->with('success', 'Ministerio creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ministerios.index')->with('error', 'Hubo un error al crear el ministerio.');
        }
    }
    


    public function edit(Ministerio $ministerio)
    {
        return view('admin.ministerios.edit', compact('ministerio'));
    }



    public function update(Request $request, Ministerio $ministerio)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'multa_incremento' => 'required|numeric|min:0',
            'hora_tolerancia' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        try {
            $data = $request->all();
    
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/ministerios/'), $filename);
                $data['logo'] = 'uploads/ministerios/' . $filename;
            }
    
            $ministerio->update($data);
    
            return redirect()->route('admin.ministerios.index')->with('success', 'Ministerio actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ministerios.index')->with('error', 'Hubo un error al actualizar el ministerio.');
        }
    }
    


    public function destroy(Ministerio $ministerio)
    {
        $ministerio->delete();
        return redirect()->route('admin.ministerios.index')->with('success', 'Ministerio eliminado correctamente.');
    }
}
