<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;

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
        $grupos = Auth::user()->grupos; // Devolviendo la relación directamente

        // Pasar las variables a la vista
        return view('home', compact('datosGenerales', 'grupos'));
    }

    /**
     * Store a newly created group in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'grado' => 'required|integer|min:1',  // Aseguramos que el grado sea un número entero positivo
            'grupo' => 'required|string|max:255|alpha', // Validamos que solo se permitan letras (grupo) 
        ]);

        // Verifica si ya existe ese grado y grupo con el mismo nombre en la base de datos
        $existe = Grupo::where('grado', $request->grado)
                       ->where('grupo', strtoupper($request->grupo))
                       ->exists();

        if ($existe) {
            // Si ya existe el grupo, devolver error
            return back()->with('error', 'Ya existe un grupo con ese grado y nombre.')->withInput();
        }

        // Guardar el nuevo grupo
        Grupo::create([
            'grado' => $request->grado,
            'grupo' => strtoupper($request->grupo),
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Grupo guardado exitosamente.');
    }

   
}
