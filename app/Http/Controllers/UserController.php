<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver usuarios')->only(['index', 'active', 'inactive']);
        $this->middleware('can:crear usuarios')->only(['create', 'store']);
        $this->middleware('can:editar usuarios')->only(['edit', 'update']);
        $this->middleware('can:ver usuario')->only(['show']);
        $this->middleware('can:eliminar usuarios')->only(['destroy']);
        $this->middleware('can:cambiar estado usuarios')->only(['status']);
    }

    public function index()
    {
        $pageTitle = 'Todos los Usuarios';
        $usuarios = $this->commonQuery()->get();
        return view('admin.usuarios.index', compact('usuarios', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Usuarios Activos';
        $usuarios =  $this->commonQuery()->active()->get();
        return view('admin.usuarios.index', compact('usuarios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Usuarios Inactivos';
        $usuarios =  $this->commonQuery()->inactive()->get();
        return view('admin.usuarios.index', compact('usuarios', 'pageTitle'));
    }

    protected function commonQuery()
    {
        return User::orderBy('id');
    }

    public function create()
    {
        $pageTitle = 'Nuevo Usuario';
        return view('admin.usuarios.create', compact('pageTitle'));
    }

    public function edit(User $user)
    {
        $pageTitle = 'Editar Usuario: ' . $user->name;
        return view('admin.usuarios.edit', compact('user', 'pageTitle'));
    }

    public function store(Request $request, $id = null)
    {
        // Validación de los datos (sin cambios)
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:users,name,' . ($id ? $id : 'NULL'),
            'email' => 'required|email|unique:users,email,' . ($id ? $id : 'NULL'),
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(Role::pluck('name'))],
            'last_name' => 'required|string|min:3|max:255',
            'address' => 'nullable|string|max:255',
            'ci' => 'required|numeric|min:1000000|max:99999999|unique:users,ci,' . ($id ? $id : 'NULL'),
            'phone' => 'nullable|numeric|min:10000000|max:99999999',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Recoger los datos, excepto el token
        $data = $request->except('_token', 'remove_logo');

        // 🔹 Eliminar la profile_image solo si el usuario la quitó manualmente
        if ($request->input('remove_logo') == '1') {
            $user = User::findOrFail($id);
            deleteFile($user->profile_image);
            $data['profile_image'] = null;
        }

        // 🔹 Si se sube una nueva profile_image, procesarla
        if ($request->hasFile('profile_image')) {
            deleteFile($user->profile_image); // Eliminar el anterior antes de guardar el nuevo
            $data['profile_image'] = uploadFile($request->file('profile_image'), 'uploads/actividad_servicios');
        }

        try {
            if ($id) {
                // Si se trata de una edición, buscar al usuario y actualizarlo
                $user = User::findOrFail($id);
                $user->update($data);

                $message = 'Usuario actualizado correctamente.';
            } else {
                // Si es un nuevo usuario, encriptar la contraseña y crear el usuario
                $password = $this->generatePassword($request->name, $request->last_name, $request->ci, $request->phone);
                $data['password'] = bcrypt($password);
                $user = User::create($data);

                $message = 'Usuario creado correctamente.';
            }

            return redirect()->route('admin.users.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error al guardar el usuario: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Hubo un error en la operación.');
        }
    }


    public function destroy(User $user)
    {
        // try {
        //     // Eliminar la profile_image del perfil si existe
        //     if ($user->profile_image && Storage::exists('public/' . $user->profile_image)) {
        //         Storage::delete('public/' . $user->profile_image);
        //     }
        //     $user->delete();
        //     return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
        // } catch (\Exception $e) {
        //     Log::error('Error al eliminar el usuario: ' . $e->getMessage());
        //     return redirect()->route('admin.users.index')->with('error', 'Hubo un error al eliminar el usuario.');
        // }
    }

    public function status($id)
    {
        return User::changeStatus($id, 'estado');
    }

    private function generatePassword($name, $last_name, $ci, $phone)
    {
        // Primeras letras del nombre y apellido
        $initials = strtolower(substr($name, 0, 1) . substr($last_name, 0, 1));

        // Primeros 3 dígitos del número de carnet
        $ciPart = substr($ci, 0, 3);

        // Últimos 4 dígitos del número de teléfono
        $phonePart = substr($phone, -4); // Tomar los últimos 4 dígitos

        // Generar la contraseña: iniciales + 3 primeros dígitos del CI + últimos 4 dígitos del teléfono
        return $initials . $ciPart . $phonePart;
    }
}
