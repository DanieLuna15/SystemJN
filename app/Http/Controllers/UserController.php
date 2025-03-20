<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ministerio;
use App\Constants\Status;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver usuarios')->only(['index', 'active', 'inactive']);
        $this->middleware('can:crear usuarios')->only(['create', 'store']);
        $this->middleware('can:editar usuarios')->only(['edit', 'update']);
        $this->middleware('can:ver usuario')->only(['show']);
        $this->middleware('can:ver perfil')->only(['profile']);
        $this->middleware('can:eliminar usuarios')->only(['destroy']);
        $this->middleware('can:cambiar estado usuarios')->only(['status']);
    }

    public function index()
    {
        $pageTitle = 'Todos los Usuarios';
        $usuarios = $this->commonQuery()->with(['ministerios', 'roles'])->get();
        return view('admin.usuarios.index', compact('usuarios', 'pageTitle'));
    }

    public function active()
    {
        $pageTitle = 'Usuarios Activos';
        $usuarios = $this->commonQuery()->active()->get();
        return view('admin.usuarios.index', compact('usuarios', 'pageTitle'));
    }

    public function inactive()
    {
        $pageTitle = 'Usuarios Inactivos';
        $usuarios = $this->commonQuery()->inactive()->get();
        return view('admin.usuarios.index', compact('usuarios', 'pageTitle'));
    }

    protected function commonQuery()
    {
        return User::orderBy('id');
    }

    public function create()
    {
        $pageTitle = 'Nuevo Usuario';
        $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();
        $roles = Role::all(); // Obtén los roles definidos en tu sistema
        return view('admin.usuarios.create', compact('pageTitle', 'ministerios', 'roles'));
    }

    public function edit(User $usuario)
    {
        $pageTitle = 'Editar Usuario: ' . $usuario->name;
        $roles = Role::all(); // Obtén los roles definidos en tu sistema
        $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();
        return view('admin.usuarios.edit', compact('usuario', 'roles', 'ministerios', 'pageTitle'));
    }

    public function store(Request $request, $id = null)
    {
        // Log inicial para identificar el flujo
        Log::info('Iniciando proceso de guardar usuario.', ['id' => $id]);

        // Validación de los datos
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . ($id ? $id : 'NULL'),
            'rol_id' => ['required', Rule::in(Role::pluck('id'))],
            'address' => 'nullable|string|max:255',
            'ci' => [
                'required',
                'numeric',
                'digits_between:5,10', // Reemplaza el rango manual con esta regla
                Rule::unique('users', 'ci')->ignore($id),
            ],
            'phone' => 'nullable|numeric|digits:8|unique:users,phone,' . ($id ? $id : 'NULL'), // Asegura que siempre sean 8 dígitos exactos
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        Log::debug('Datos validados.', $validatedData);

        // Recoger los datos, excepto el token y otras claves innecesarias
        $data = $request->except('_token', 'remove_logo');
        Log::debug('Datos procesados para guardar.', $data);

        // Definir $usuario si $id no es nulo (proceso de edición)
        $usuario = $id ? User::find($id) : null;
        if ($id && !$usuario) {
            Log::warning('Usuario no encontrado.', ['id' => $id]);
            return redirect()->back()->with('error', 'Usuario no encontrado.');
        }
        Log::info('Usuario encontrado o preparado para creación.', ['usuario' => $usuario]);

        // Eliminar la profile_image solo si el usuario la quitó manualmente
        if ($request->input('remove_logo') == '1' && $usuario) {
            deleteFile($usuario->profile_image);
            $data['profile_image'] = null;
            Log::info('Imagen de perfil eliminada.', ['user_id' => $usuario->id]);
        }

        // Procesar nueva profile_image si se subió
        if ($request->hasFile('profile_image')) {
            if ($usuario && $usuario->profile_image) {
                deleteFile($usuario->profile_image); // Eliminar la imagen anterior si existe
            }
            $data['profile_image'] = uploadFile($request->file('profile_image'), 'uploads/usuarios');
            Log::info('Nueva imagen de perfil procesada.', ['file_path' => $data['profile_image']]);
        }

        try {
            if ($id) {
                // Actualizar usuario existente
                $usuario->update($data);
                $message = 'Usuario actualizado correctamente.';
                Log::info('Usuario actualizado.', ['user_id' => $usuario->id]);
            } else {
                // Crear nuevo usuario
                $password = generatePassword($request->name, $request->last_name, $request->ci, $request->phone);
                $data['password'] = bcrypt($password);
                $usuario = User::create($data);
                $message = 'Usuario creado correctamente.';
                Log::info('Usuario creado.', ['user_id' => $usuario->id]);
            }

            // Obtener el nombre del rol y validar
            $roleName = Role::find($request->input('rol_id'))->name ?? null;
            if (!$roleName) {
                Log::warning('Rol inválido seleccionado.', ['rol_id' => $request->input('rol_id')]);
                return redirect()->back()->with('error', 'El rol seleccionado no es válido.');
            }

            // Sincronizar el rol con el usuario
            $usuario->syncRoles([$roleName]);
            Log::info('Roles sincronizados.', ['user_id' => $usuario->id, 'roles' => $roleName]);

            // Sincronizar ministerios
            $usuario->ministerios()->sync($request->input('ministerio_id', []));
            Log::info('Ministerios sincronizados.', ['user_id' => $usuario->id]);

            // Redirigir con éxito
            return redirect()->route('admin.usuarios.index')->with('success', $message);
        } catch (\Exception $e) {
            // Log del error
            Log::error('Error al guardar el usuario.', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.usuarios.index')->with('error', 'Hubo un error en la operación.');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Iniciando actualización del usuario.', ['id' => $id]);

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

        return redirect()->route('admin.usuarios.profile', ['usuario' => $usuario->id])->with('success', 'Usuario actualizado correctamente.');
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


    public function info(User $usuario)
    {
        $pageTitle = 'Informacion del Usuario: ' . $usuario->name;

        return view('admin.usuarios.info', compact('usuario', 'pageTitle'));
    }

    public function profile()
    {
        $pageTitle = 'Mi perfil';
        $usuario = auth()->user();
        return view('admin.usuarios.profile', compact('pageTitle', 'usuario'));
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

        return redirect()->route('admin.usuarios.profile')
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
