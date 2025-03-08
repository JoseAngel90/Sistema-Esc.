<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\DatosGenerales;
use App\Models\Grupo;
use Illuminate\Support\Facades\Log;

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
        // Obtener los datos generales del usuario autenticado
        $datosGenerales = auth()->user()->datosGeneralesEscuela;

        // Obtener los grupos asociados al usuario autenticado
        $grupos = Auth::user()->grupos()->get();
        dd($grupos);
        $grupos = Grupo::select('*')->get();

        // Pasar las variables a la vista
        return view('home', compact('datosGenerales'));
    }

    use Exception;



}