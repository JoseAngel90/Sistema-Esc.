<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\EvaluacionGeneral;

class DiagnosticoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($grado, $grupo)
    {
        $usuario = Auth::user();

        $alumnos = Alumno::where('grado', $grado)
                         ->where('grupo', $grupo)
                         ->where('user_id', $usuario->id)
                         ->get();

        foreach ($alumnos as $alumno) {
            $evaluacion = EvaluacionGeneral::where('alumno_id', $alumno->id)->first();
            $alumno->promedio = $evaluacion ? $evaluacion->promedio : null;
        }

        return view('diagnostico', compact('alumnos', 'grado', 'grupo'));
    }

    public function mostrarDiagnostico(Request $request)
    {
        $usuario = Auth::user();

        $grado = $request->grado;
        $grupo = $request->grupo;
        $reactivosMax = $request->reactivos_max;

        $alumnos = Alumno::where('grado', $grado)
                         ->where('grupo', $grupo)
                         ->where('user_id', $usuario->id)
                         ->get();

        foreach ($alumnos as $alumno) {
            if ($alumno->contestados != 0) {
                $calculo = ($alumno->contestados / $reactivosMax) * 10;
                $alumno->promedio = min($calculo, 10); // Limitar a 10
            } else {
                $alumno->promedio = 0;
            }
        }

        return view('diagnostico.mostrar', compact('alumnos', 'grado', 'grupo', 'reactivosMax'));
    }

    public function guardarReactivos(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Debes estar autenticado para realizar esta acción.');
        }

        $reactivos = $request->input('reactivos');
        $reactivosMaximos = $request->input('reactivos_max');

        if (!$reactivos || count($reactivos) == 0) {
            return redirect()->back()->with('error', 'No se recibieron datos de reactivos.');
        }

        if ($reactivosMaximos <= 0) {
            return redirect()->back()->with('error', 'El número máximo de reactivos debe ser mayor que 0.');
        }

        try {
            foreach ($reactivos as $reactivo) {
                if (!isset($reactivo['contestados'])) {
                    return redirect()->back()->with('error', 'Faltan reactivos contestados.');
                }

                $reactivosContestados = $reactivo['contestados'];
                $calculo = ($reactivosContestados / $reactivosMaximos) * 10;
                $calificacion = min($calculo, 10); // Limitar a 10

                $evaluacion = EvaluacionGeneral::where('alumno_id', $reactivo['alumno_id'])->first();

                if ($evaluacion) {
                    $evaluacion->promedio = $calificacion;
                    $evaluacion->save();
                } else {
                    EvaluacionGeneral::create([
                        'alumno_id' => $reactivo['alumno_id'],
                        'user_id' => $usuario->id,
                        'promedio' => $calificacion
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Reactivos guardados exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error al guardar los reactivos. ' . $e->getMessage());
        }
    }
}
