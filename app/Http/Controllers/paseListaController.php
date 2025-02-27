<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno; // Modelo de la tabla alumnos
use App\Models\pasedelista; // Modelo de la tabla registro_asistencia

class paseListaController extends Controller
{
    // Método para mostrar el pase de lista
    public function index()
{
    $alumnos = Alumno::where('user_id', auth()->id())->get();

    // Obtener solo las asistencias de hoy
    $asistencias = pasedelista::whereIn('alumno_id', $alumnos->pluck('id'))
                               ->whereDate('fecha', now()->toDateString()) 
                               ->where('user_id', auth()->id())
                               ->get();

    return view('pasedelista', compact('alumnos', 'asistencias'));
}


    // Método para guardar o actualizar el pase de lista
    public function store(Request $request)
{
    $request->validate([
        'fecha.*' => 'required|date',
        'asistencia.*' => 'required|in:presente,ausente',
    ]);

    $userId = auth()->id();

    foreach ($request->asistencia as $alumnoId => $asistencia) {
        pasedelista::updateOrCreate(
            [
                'alumno_id' => $alumnoId,
                'fecha' => $request->fecha[$alumnoId],
                'user_id' => $userId
            ],
            [
                'asistencia' => $asistencia
            ]
        );
    }

    return redirect()->route('pase.lista')->with('success', 'Asistencia registrada correctamente.');
}


    // Método para mostrar los grados y grupos, y filtrar por ellos
    public function showPaseLista()
    {
        // Obtener todos los grados y grupos únicos de los alumnos
        $grados = Alumno::where('user_id', auth()->id())->pluck('grado')->unique(); // Obtener grados únicos del usuario autenticado
        $grupos = Alumno::where('user_id', auth()->id())->pluck('grupo')->unique(); // Obtener grupos únicos del usuario autenticado

        // Obtener los alumnos del usuario autenticado
        $alumnos = Alumno::where('user_id', auth()->id())->get();

        // Obtener las asistencias de los alumnos del usuario autenticado
        $asistencias = pasedelista::whereIn('alumno_id', $alumnos->pluck('id'))
                                   ->where('user_id', auth()->id())
                                   ->get();

        return view('pase_lista', compact('alumnos', 'asistencias', 'grados', 'grupos'));
    }

    
}
