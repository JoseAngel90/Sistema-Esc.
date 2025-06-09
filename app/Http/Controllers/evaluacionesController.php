<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;

class EvaluacionesController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }

        $gradoFiltro = $request->grado;
        $grupoFiltro = $request->grupo;

        $alumnos = Alumno::where('user_id', $usuario->id);

        if ($gradoFiltro) {
            $alumnos->where('alumnos.grado', $gradoFiltro);
        }

        if ($grupoFiltro) {
            $alumnos->where('alumnos.grupo', $grupoFiltro);
        }

        $alumnos = $alumnos->get();

        $grados = Alumno::distinct()->pluck('grado');
        $grupos = Alumno::distinct()->pluck('grupo');

        if ($alumnos->isEmpty()) {
            return redirect()->route('erro.404')->with('error', 'No tienes alumnos registrados.');
        }

        $actaCerrada = $request->session()->get('acta_cerrada', false);

        return view('404', compact('alumnos', 'grados', 'grupos', 'gradoFiltro', 'grupoFiltro'));
    }
}
