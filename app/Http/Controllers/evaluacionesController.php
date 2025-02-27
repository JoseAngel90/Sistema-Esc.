<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\Evaluaciones; // IMPORTA el modelo correctamente

class EvaluacionesController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
    
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }
    
        $alumnos = Alumno::where('user_id', $usuario->id)
            ->leftJoin('examen_diagnostico', 'alumnos.id', '=', 'examen_diagnostico.alumno_id')
            ->select('alumnos.*', 'examen_diagnostico.calificacion')
            ->get();
    
        if ($alumnos->isEmpty()) {
            return redirect()->route('evaluacion')->with('error', 'No tienes alumnos registrados.');
        }
    
        return view('Evaluaciones', compact('alumnos'));
    }
    
    public function guardarCalificaciones(Request $request)
    {
        $usuario = Auth::user();
        $alumnosUsuario = Alumno::where('user_id', $usuario->id)->pluck('id')->toArray();
    
        foreach ($request->calificaciones as $calificacionData) {
            if (!in_array($calificacionData['alumno_id'], $alumnosUsuario)) {
                continue;
            }
    
            Evaluaciones::updateOrCreate(
                ['alumno_id' => $calificacionData['alumno_id']],
                ['calificacion' => $calificacionData['calificacion'], 'fecha' => now()]
            );
        }
    
        return redirect()->back()->with('success', 'Calificaciones guardadas correctamente.');
    }
    
}
