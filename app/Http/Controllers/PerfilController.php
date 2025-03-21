<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver perfil')->only(['profile']);
        $this->middleware('can:editar perfil_informacion')->only(['update']);
        $this->middleware('can:editar perfil_imagen')->only(['profile']);
        $this->middleware('can:editar perfil_contraseña')->only(['updatePassword']);
    }

    public function index()
    {
        $pageTitle = 'Mi perfil';
        $usuario = auth()->user();
        return view('admin.perfil.index', compact('pageTitle', 'usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        if ($request->input('form_type') === 'secundario') {
            $validatedData = $request->validate([
                'name' => 'required|string|min:3|max:255|unique:users,name,' . $id,
                'last_name' => 'required|string|min:3|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|numeric|digits:8|unique:users,phone,' . ($id ? $id : 'NULL'),
                'address' => 'nullable|string|max:255',
                'ci' => [
                    'required',
                    'numeric',
                    'digits_between:5,10', // Reemplaza el rango manual con esta regla
                    Rule::unique('users', 'ci')->ignore($id),
                ],
            ]);
        } else {
            return redirect()->back()->with('error', 'Formulario no válido.');
        }

        $usuario->fill($validatedData);
        $usuario->save();

        return redirect()->route('admin.perfil.index', ['usuario' => $usuario->id])->with('success', 'Usuario actualizado correctamente.');
    }



    public function updateImage(Request $request, $id)
    {
        $request->validate([
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::findOrFail($id);

        // Eliminar imagen manualmente si se solicita
        if ($request->input('remove_imagen') == '1' && $user->profile_image) {
            deleteFile($user->profile_image);
            $user->profile_image = null;
        }

        // Subir nueva imagen si se adjunta
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                deleteFile($user->profile_image); // Eliminar la anterior si existe
            }
            $user->profile_image = uploadFile($request->file('profile_image'), 'uploads/usuarios');
        }

        $user->save();

        return redirect()->route('admin.perfil.index')
            ->with('success', 'Imagen de perfil actualizada correctamente');
    }

    public function updatePassword(Request $request, $usuario)
    {
        $user = User::findOrFail($usuario);

        $request->validate([
            'password_actual' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->password_actual, $user->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
