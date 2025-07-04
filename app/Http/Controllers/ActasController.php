<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Alumno;
use App\Models\CalificarCotejo;
use App\Models\DatosGenerales;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActasController extends Controller
{
    public function descargarActa(Request $request)
{
    $usuario = Auth::user();

    $gradoFiltro = $request->input('grado');
    $grupoFiltro = $request->input('grupo');

    $query = Alumno::where('user_id', $usuario->id);
    if ($gradoFiltro) $query->where('grado', $gradoFiltro);
    if ($grupoFiltro) $query->where('grupo', $grupoFiltro);
    $alumnos = $query->get();

    $datosGenerales = DatosGenerales::where('user_id', $usuario->id)->first();
    $asignatura = $datosGenerales->asignatura ?? 'Asignatura no definida';
    $nombre_profesor = $datosGenerales->nombre_profesor ?? 'Nombre no definido';
    $correo = $usuario->email ?? 'Correo no definido';
    $periodoPersonalizado = $request->input('periodo');


    $pestanias = [
        'apoyo_p' => 'apoyo_p_id',
        'proyectos' => 'proyectos_id',
        'trabajos_clase' => 'trabajos_clase_id',
        'tareas' => 'tareas_id',
        'examen' => 'examen_id',
    ];

    $calificacionesPorPestania = [];
    foreach ($pestanias as $tipo => $campo) {
        $calificacionesPorPestania[$tipo] = DB::table('calificaciones')
            ->join('alumnos', 'alumnos.id', '=', 'calificaciones.alumno_id')
            ->where('alumnos.user_id', $usuario->id)
            ->whereNotNull("calificaciones.$campo")
            ->when($gradoFiltro, fn($q) => $q->where('alumnos.grado', $gradoFiltro))
            ->when($grupoFiltro, fn($q) => $q->where('alumnos.grupo', $grupoFiltro))
            ->select(
                'calificaciones.alumno_id',
                'calificaciones.evaluacion_1',
                'calificaciones.evaluacion_2',
                'calificaciones.evaluacion_3',
                'calificaciones.evaluacion_4',
                'calificaciones.evaluacion_5',
                'calificaciones.Total'
            )
            ->get()
            ->keyBy('alumno_id');
    }

    // Obtener el periodo de la tabla grupo
    $periodoInicio = Grupo::where('grado', $gradoFiltro)
        ->where('grupo', $grupoFiltro)
        ->orderBy('created_at', 'asc')
        ->value('created_at') ?? now();

    $periodoFin = now();

    // Cálculo del promedio por alumno
    $promedios = [];
    foreach ($alumnos as $alumno) {
        $calificacionesAlumno = [];

        // Recopilar las calificaciones de cada criterio
        foreach ($pestanias as $tipo => $campo) {
            $calificaciones = $calificacionesPorPestania[$tipo][$alumno->id] ?? null;
            if ($calificaciones) {
                $calificacionesAlumno[] = $calificaciones->Total;
            }
        }

        // Calcular el promedio
        if (count($calificacionesAlumno) > 0) {
            $promedio = array_sum($calificacionesAlumno) / count($calificacionesAlumno);
            $promedioEntero = (int) round($promedio); // Redondear el promedio a entero
        } else {
            $promedio = 0;
            $promedioEntero = 0; // Si no tiene calificaciones, el promedio entero es 0
        }

        // Guardar los promedios con el nombre del alumno
        $promedios[] = [
            'nombre' => $alumno->nombre_alumno,
            'promedio' => round($promedio, 2), // Redondear el promedio a 2 decimales
            'promedio_entero' => $promedioEntero, // Promedio entero
        ];
    }

    // Criterios por tipo
    $criteriosPorTipo = [
        'apoyo_p' => 'Participación y conducta',
        'proyectos' => 'Desarrollo de proyectos',
        'trabajos_clase' => 'Ejercicios en clase',
        'tareas' => 'Responsabilidad en tareas',
        'examen' => 'Conocimientos adquiridos',
    ];

    $tipos = array_keys($pestanias);


    $tipos = array_keys($pestanias);

        $rubrosPorTipo = [
        'apoyo_p' => 'rubro1',
        'proyectos' => 'rubro2',
        'trabajos_clase' => 'rubro3',
        'tareas' => 'rubro4',
        'examen' => 'rubro5',
    ];

    $etiquetasRubros = [];
    foreach ($rubrosPorTipo as $tipo => $rubro) {
        $etiqueta = DB::table('etiquetas_rubros')
            ->where('user_id', $usuario->id)
            ->where('grado', $gradoFiltro)
            ->where('grupo', $grupoFiltro)
            ->where('etiqueta_nombre', $rubro)
            ->value('etiqueta_rubro');
        $etiquetasRubros[$tipo] = $etiqueta;
    }

    $aspectos = [];
    foreach ($rubrosPorTipo as $tipo => $rubro) {
        $registro = DB::table('etiquetas_rubros')
            ->where('user_id', $usuario->id)
            ->where('grado', $gradoFiltro)
            ->where('grupo', $grupoFiltro)
            ->where('etiqueta_nombre', $rubro)
            ->first();

        for ($i = 1; $i <= 5; $i++) {
            $aspectos[$i][$tipo] = $registro ? ($registro->{'etiqueta_aspecto'.$i} ?? null) : null;
        }
    }

    $porcentaje = [];
    foreach ($rubrosPorTipo as $tipo => $rubro) {
        $valor = DB::table('calificaciones')
            ->join('alumnos', 'alumnos.id', '=', 'calificaciones.alumno_id')
            ->where('calificaciones.user_id', $usuario->id)
            ->where('alumnos.grado', $gradoFiltro)
            ->where('alumnos.grupo', $grupoFiltro)
            ->avg("calificaciones.$rubro");
        $porcentaje[$tipo] = $valor !== null ? round($valor, 2) : 0;
    }



    //dd($porcentaje); // Para depurar y ver los porcentajes obtenidos

    // Generar el PDF con la vista 'acta_pdf'
    $pdf = Pdf::loadView('acta_pdf', compact(
        'etiquetasRubros',
        'alumnos',
        'calificacionesPorPestania',
        'tipos',
        'gradoFiltro',
        'grupoFiltro',
        'asignatura',
        'nombre_profesor',
        'correo',
        'criteriosPorTipo',
        'periodoInicio',
        'periodoFin',
        'promedios', 
        'periodoPersonalizado',
        'aspectos',
        'porcentaje',
        'datosGenerales'
    ));

    return $pdf->download('acta_calificaciones_' . $gradoFiltro . '_' . $grupoFiltro . '_' . date('Y-m') .'.pdf');
}



   public function cerrarActa(Request $request)
{
    $usuario = Auth::user();

    if (!$usuario) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
    }

    $grado = $request->input('grado');
    $grupo = $request->input('grupo');

    $query = Alumno::where('user_id', $usuario->id);
    if ($grado) $query->where('grado', $grado);
    if ($grupo) $query->where('grupo', $grupo);

    $alumnos = $query->pluck('id');

    // Eliminar registros de CalificarCotejo
    CalificarCotejo::whereIn('alumno_id', $alumnos)
        ->where('user_id', $usuario->id)
        ->delete();

    // Eliminar registros de etiquetas_rubros
    DB::table('etiquetas_rubros')
        ->where('user_id', $usuario->id)
        ->where('grado', $grado)
        ->where('grupo', $grupo)
        ->delete();

    return redirect()->route('panel', ['grado' => $grado, 'grupo' => $grupo])
        ->with('success', 'El acta fue cerrada y los datos eliminados exitosamente.');
}
}

