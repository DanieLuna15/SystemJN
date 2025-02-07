<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Ministerio;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{
    public function index()
    {
        $pageTitle = 'Todos los Ministerios';
        $ministerios = $this->commonQuery()->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Ministerios Activos';
        $ministerios = $this->commonQuery()->where('estado', Status::ACTIVE)->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Ministerios Inactivos';
        $ministerios = $this->commonQuery()->where('estado', Status::INACTIVE)->get();
        return view('admin.ministerios.index', compact('ministerios', 'pageTitle'));
    }


    protected function commonQuery()
    {
        return Ministerio::query()->orderBy('id', 'DESC');
    }


    public function create()
    {
        return view('admin.ministerios.create');
    }

    public function edit(Ministerio $ministerio)
    {
        return view('admin.ministerios.edit', compact('ministerio'));
    }


    public function store(Request $request, $id = null)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'multa_incremento' => 'required|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->except(['_token']);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/ministerios/'), $filename);
                $data['logo'] = 'uploads/ministerios/' . $filename;
            }

            if ($id) {
                $ministerio = Ministerio::findOrFail($id);
                $ministerio->update($data);
                $message = 'Ministerio actualizado correctamente.';
            } else {
                Ministerio::create($data);
                $message = 'Ministerio creado correctamente.';
            }

            return redirect()->route('admin.ministerios.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.ministerios.index')->with('error', 'Hubo un error en la operación.');
        }
    }

    public function status($id)
    {
        return Ministerio::changeStatus($id, 'estado');
    }
}
