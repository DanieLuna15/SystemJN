<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use App\Models\Horario;
use App\Models\ActividadServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Constants\Status;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $ministerios= Ministerio::where('estado', Status::ACTIVE)->count();
        $horarios = Horario::where('estado', Status::ACTIVE)->count();
        $actividadServicios = ActividadServicio::where('estado', Status::ACTIVE)->count();
        return view('home', compact('ministerios', 'horarios', 'actividadServicios'));
    }
}
