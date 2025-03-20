<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Constants\Status;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | Este controlador maneja la autenticación de usuarios y redirige a la
    | pantalla principal después del inicio de sesión exitoso.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Método para manejar la autenticación personalizada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->estado != Status::ACTIVE) {
            Auth::logout(); // Cierra la sesión si el usuario está inactivo

            return redirect()->route('login')->withErrors([
                'estado' => 'Tu cuenta está inhabilitada. Por favor, contacta al Administrador.',
            ]);
        }

        // Si el usuario está activo, continúa con el flujo normal
        return redirect()->intended($this->redirectTo);
    }
}
