<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\DatosGenerales;


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

        // Contar el número de alumnos asociados al usuario
        $numeroAlumnos = auth()->user()->alumnos()->count();
        dd($numeroAlumnos); // Esto debería mostrar el número de alumnos en la pantalla
        

        return view('home', compact('datosGenerales', 'numeroAlumnos'));
    }

    // Método para actualizar el número de alumnos (si lo necesitas)
    public function updateNumeroAlumnos(Request $request)
    {
        // Lógica para actualizar el número de alumnos (si lo necesitas)
        // Esto podría involucrar la creación/eliminación de registros en la base de datos

        return response()->json([
            'numero_alumnos' => Alumno::count(), // Regresa el nuevo número de alumnos
        ]);
    }
    
}
