<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Pasedelista;

class PaseListaController extends Controller
{
    public function index(Request $request)
    {
        $grado = $request->input('grado');
        $grupo = $request->input('grupo');

        // Filtrar alumnos por grado y grupo
        $alumnos = Alumno::where('user_id', auth()->id())
                         ->when($grado, fn($q) => $q->where('grado', $grado))
                         ->when($grupo, fn($q) => $q->where('grupo', $grupo))
                         ->get();

        // Obtener asistencias
        $asistencias = Pasedelista::whereIn('alumno_id', $alumnos->pluck('id'))
                                   ->whereDate('fecha', now()->toDateString())
                                   ->where('user_id', auth()->id())
                                   ->get();

        // Devolver la vista con los alumnos y asistencias
        return view('pasedelista', compact('alumnos', 'asistencias', 'grado', 'grupo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha.*' => 'required|date',
            'asistencia.*' => 'required|in:presente,ausente',
        ]);

        $userId = auth()->id();

        foreach ($request->asistencia as $alumnoId => $asistencia) {
            Pasedelista::updateOrCreate(
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

        // Mantener los filtros al redirigir
        return redirect()->route('pase.lista', [
            'grado' => $request->input('grado'),
            'grupo' => $request->input('grupo')
        ])->with('success', 'Asistencia registrada correctamente.');
    }

    public function showPaseLista()
    {
        $grados = Alumno::where('user_id', auth()->id())->pluck('grado')->unique();
        $grupos = Alumno::where('user_id', auth()->id())->pluck('grupo')->unique();

        return view('pase_lista', compact('grados', 'grupos'));
    }
}
