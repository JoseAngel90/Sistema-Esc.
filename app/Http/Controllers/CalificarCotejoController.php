<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alumno;
use App\Models\CalificarCotejo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Para agregar logs en el proceso

class CalificarCotejoController extends Controller
{
    // Mostrar la vista de calificaciones
    public function index(Request $request)
    {
        $usuario = Auth::user();
    
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }
    
        $gradoFiltro = $request->input('grado', '');
        $grupoFiltro = $request->input('grupo', '');
    
        // Filtrar alumnos del usuario
        $query = Alumno::where('user_id', $usuario->id);
        if ($gradoFiltro) $query->where('grado', $gradoFiltro);
        if ($grupoFiltro) $query->where('grupo', $grupoFiltro);
        $alumnos = $query->get();
    
        // Obtener listas únicas para filtros
        $grados = Alumno::distinct()->pluck('grado');
        $grupos = Alumno::distinct()->pluck('grupo');
    
        // Pestañas y campos relacionados
        $pestanias = [
            'apoyo_p' => 'apoyo_p_id',
            'proyectos' => 'proyectos_id',
            'trabajos_clase' => 'trabajos_clase_id',
            'tareas' => 'tareas_id',
            'examen' => 'examen_id',
        ];

        // Obtener calificaciones por pestaña
        $calificacionesPorPestania = [];
        foreach ($pestanias as $tipo => $campo) {
            $calificacionesPorPestania[$tipo] = DB::table('calificaciones')
                ->join('alumnos', 'alumnos.id', '=', 'calificaciones.alumno_id')
                ->where('alumnos.user_id', $usuario->id)
                ->whereNotNull("calificaciones.$campo")
                ->select(
                    'calificaciones.alumno_id',
                    'calificaciones.evaluacion_1',
                    'calificaciones.evaluacion_2',
                    'calificaciones.evaluacion_3',
                    'calificaciones.evaluacion_4',
                    'calificaciones.evaluacion_5',
                    'calificaciones.valor_maximo1',
                    'calificaciones.valor_maximo2',
                    'calificaciones.valor_maximo3',
                    'calificaciones.entregables_1',
                    'calificaciones.entregables_2',
                    'calificaciones.entregables_3',
                    'calificaciones.Total'
                )
                ->get()
                ->keyBy('alumno_id');
        }


         // Obtener las etiquetas del usuario autenticado
        $etiquetas = DB::table('etiquetas_rubros')
            ->where('user_id', $usuario->id)
            ->select('etiqueta_rubro', 'etiqueta_pestania', 'etiqueta_nombre')
            ->when($gradoFiltro, function ($query) use ($gradoFiltro) {
                return $query->where('grado', $gradoFiltro);
            }) 
            ->when($grupoFiltro, function ($query) use ($grupoFiltro) {
                return $query->where('grupo', $grupoFiltro);
            })
            ->orderBy('etiqueta_pestania')
            ->orderBy('etiqueta_nombre')
            ->get();

        // Obtener el nombre de los aspectos por pestania
        $aspectos = [];
        $rubrosPorTipo = [
            'apoyo_p' => 'rubro1',
            'proyectos' => 'rubro2',
            'trabajos_clase' => 'rubro3',
            'tareas' => 'rubro4',
            'examen' => 'rubro5',
        ];

        foreach ($rubrosPorTipo as $tipo => $rubro) {
            $registro = \DB::table('etiquetas_rubros')
                ->where('user_id', Auth::id())
                ->where('grado', $gradoFiltro)
                ->where('grupo', $grupoFiltro)
                ->where('etiqueta_nombre', $rubro)
                ->first();

            for ($i = 1; $i <= 5; $i++) {
                $aspectos[$i][$tipo] = $registro ? ($registro->{'etiqueta_aspecto'.$i} ?? null) : null;
            }
        }

        $etiquetas = DB::table('etiquetas_rubros')
            ->where('user_id', $usuario->id)
            ->when($gradoFiltro, function ($query) use ($gradoFiltro) {
                return $query->where('grado', $gradoFiltro);
            })
            ->when($grupoFiltro, function ($query) use ($grupoFiltro) {
                return $query->where('grupo', $grupoFiltro);
            })
            ->orderBy('etiqueta_pestania')
            ->orderBy('etiqueta_nombre')
            ->select('etiqueta_rubro', 'etiqueta_pestania', 'etiqueta_nombre')
            ->get();
            

        $registro = CalificarCotejo::first();

        if ($alumnos->isEmpty()) {
            return redirect()->route('evaluacion')->with('error', 'No tienes alumnos registrados en este grado o grupo.');
        }

        return view('CalificarCotejo', compact(
            'alumnos', 'grados', 'grupos', 'gradoFiltro', 'grupoFiltro', 'registro', 'calificacionesPorPestania','etiquetas', 'aspectos'
        ));
        
    }
    


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Guardar Rubro en la base de datos
    public function guardarRubro(Request $request)
    {
        // Validación dinámica
        $rules = [
            'grado' => 'required|string',
            'grupo' => 'required|string',
        ];

        for ($i = 1; $i <= 5; $i++) {
            $rules["Rubro$i"] = 'nullable|numeric|min:0|max:100';
            $rules["etiqueta-Rubro$i"] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        $usuario_id = Auth::id();
        $grado = $request->input('grado');
        $grupo = $request->input('grupo');

        // Preparar datos de rubros
        $data = [];
        for ($i = 1; $i <= 5; $i++) {
            $rubroInput = $request->input("Rubro$i");
            if (!is_null($rubroInput)) {
                $data["rubro$i"] = $rubroInput;
            }
        }

        if (empty($data)) {
            return redirect()->back()->with('error', 'No se ingresó ningún valor para guardar.');
        }

        // Guardar rubros
        \App\Models\CalificarCotejo::updateOrCreate(
            ['user_id' => $usuario_id],
            $data
        );

        // Guardar solo etiquetas de rubro
        $tipos = ['apoyo_p', 'proyectos', 'trabajos_clase', 'tareas', 'examen'];
        foreach ($tipos as $index => $tipo) {
            $campoNombre = 'rubro' . ($index + 1);
            $nombreEtiqueta = $request->input("etiqueta-Rubro" . ($index + 1));

            if (!is_null($nombreEtiqueta)) {
                \DB::table('etiquetas_rubros')->updateOrInsert(
                    [
                        'user_id' => $usuario_id,
                        'etiqueta_nombre' => $campoNombre,
                        'grado' => $grado,
                        'grupo' => $grupo,
                    ],
                    [
                        'etiqueta_rubro' => $nombreEtiqueta,
                        'updated_at' => now(),
                        'created_at' => now(), // Laravel ignora este campo si ya existe
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Rubro(s) y etiquetas guardados correctamente.');
    }



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Guardar Calificaciones de los Alumnos
    public function guardarCalificacion(Request $request)
{
    // Verificar que el alumno está autenticado
    $alumno = Alumno::where('user_id', Auth::id())->first();
    if (!$alumno) {
        return redirect()->back()->with('error', 'No se encontró el alumno asociado a este usuario.');
    }

    // Obtener el tipo de pestaña (por ejemplo: apoyo_p, proyectos, etc.)
    $tipoPestania = $request->input('tipo_pestania');
    if (!$tipoPestania) {
        return redirect()->back()->with('error', 'Tipo de pestaña no válido.');
    }

    // Verificar que el usuario tiene datos para calificar
    $calificarCotejo = CalificarCotejo::where('user_id', Auth::id())->first();
    if (!$calificarCotejo) {
        return redirect()->back()->with('error', 'No se encontraron datos para el usuario.');
    }

    // Definir los campos para cada tipo de pestaña
    $pestanias = [
        'apoyo_p' => 'apoyo_p_id',
        'proyectos' => 'proyectos_id',
        'trabajos_clase' => 'trabajos_clase_id',
        'tareas' => 'tareas_id',
        'examen' => 'examen_id',
    ];

    if (!array_key_exists($tipoPestania, $pestanias)) {
        return redirect()->back()->with('error', 'Pestaña no válida.');
    }

    // Determinar el campo para almacenar el id de la pestaña en la base de datos
    $campoPestania = $pestanias[$tipoPestania];

    // Asignar un número a cada pestaña (apoyo_p=1, proyectos=2, etc.)
    $pestaniaIndex = array_search($tipoPestania, array_keys($pestanias)) + 1;

    // Obtener el grado y grupo para guardar etiquetas (asumo que vienen en el request)
    $grado = $request->input('grado');
    $grupo = $request->input('grupo');

    // Obtener las calificaciones de los alumnos
    $evaluaciones = $request->input('evaluaciones');
    $elementosCorrectos = $request->input('elementos_correctos');
    $elementosTotales = $request->input('elementos_totales');


    // Validar que las evaluaciones no sean nulas y sean un array
    if (!$evaluaciones || !is_array($evaluaciones)) {
        return redirect()->back()->with('error', 'Las evaluaciones no son válidas o no fueron enviadas.');
    }

    // Lista de alumnos que se evaluarán
    $lista_alumnos_ids = array_keys($evaluaciones);
    if (empty($lista_alumnos_ids)) {
        return redirect()->back()->with('error', 'No se encontraron evaluaciones válidas para los alumnos.');
    }

    // Rubros asociados con cada tipo de pestaña
    $rubros = [
        'apoyo_p' => 'rubro1',
        'proyectos' => 'rubro2',
        'trabajos_clase' => 'rubro3',
        'tareas' => 'rubro4',
        'examen' => 'rubro5',
    ];

    foreach ($lista_alumnos_ids as $alumno_id) {
    $evaluacionAlumno = $evaluaciones[$alumno_id];
    $datosEvaluacion = [];

    $maxEvaluaciones = ($tipoPestania === 'apoyo_p') ? 5 : 3;

    for ($i = 1; $i <= $maxEvaluaciones; $i++) {
        $key = 'eval' . $i;
        $campo = 'evaluacion_' . $i;

        $valor = $evaluacionAlumno[$key] ?? null;
        $datosEvaluacion[$campo] = ($tipoPestania === 'apoyo_p' && $valor !== null) ? $valor * 100 : $valor;

        // Guardar valor máximo si viene
        $campoElementoCorrecto = 'valor_maximo' . $i;
        if (
            isset($elementosCorrectos[$alumno_id]) &&
            isset($elementosCorrectos[$alumno_id][$i]) &&
            isset($elementosCorrectos[$alumno_id][$i][$tipoPestania])
        ) {
            $datosEvaluacion[$campoElementoCorrecto] = $elementosCorrectos[$alumno_id][$i][$tipoPestania];
        }

        // Guardar entregables (elementos totales)
        $campoEntregables = 'entregables_' . $i;
        if (
            isset($elementosTotales[$alumno_id]) &&
            isset($elementosTotales[$alumno_id][$i]) &&
            isset($elementosTotales[$alumno_id][$i][$tipoPestania])
        ) {
            $datosEvaluacion[$campoEntregables] = $elementosTotales[$alumno_id][$i][$tipoPestania];
        }
    }

        // Calcular el promedio solo con los campos evaluacion_X
        $valoresValidos = [];
        for ($i = 1; $i <= $maxEvaluaciones; $i++) {
            $campo = 'evaluacion_' . $i;
            if (isset($datosEvaluacion[$campo]) && is_numeric($datosEvaluacion[$campo])) {
                $valoresValidos[] = $datosEvaluacion[$campo];
            }
        }

        $promedio = count($valoresValidos) > 0 ? array_sum($valoresValidos) / count($valoresValidos) : null;

        // Ajuste: promedio sobre 10 y si es menor a 5.9 y distinto de 0, guardar 5
        if ($promedio !== null) {
            $promedioSobre10 = $promedio / 10;
            $promedioRedondeado = round($promedioSobre10, 2);
            $totalFinal = ($promedioRedondeado < 5.9 && $promedioRedondeado != 0) ? 5 : $promedioRedondeado;
        } else {
            $totalFinal = null;
        }
        $datosEvaluacion['Total'] = $totalFinal;

        // Asignar el rubro correspondiente según el tipo de pestaña
        $nombreRubro = $rubros[$tipoPestania];
        $datosEvaluacion[$nombreRubro] = $calificarCotejo->$nombreRubro;

        // Guardar la evaluación usando updateOrCreate
        CalificarCotejo::updateOrCreate(
            [
                'alumno_id' => $alumno_id,
                'user_id' => auth()->id(),
                'periodo' => now()->year,
                $campoPestania => 1, // Siempre 1 para cada pestaña por alumno, usuario y periodo
            ],
            $datosEvaluacion
        );
    }

    // Supón que recibes los aspectos así: aspectos[1][proyectos], aspectos[2][proyectos], etc.
    $aspectos = $request->input('aspectos', []);
    $tipo = $request->input('tipo_pestania'); // por ejemplo: 'proyectos'
    $grado = $request->input('grado');
    $grupo = $request->input('grupo');
    $user_id = Auth::id();

    // Mapea el tipo de pestaña a su rubro correspondiente
    $rubrosPorTipo = [
        'apoyo_p' => 'rubro1',
        'proyectos' => 'rubro2',
        'trabajos_clase' => 'rubro3',
        'tareas' => 'rubro4',
        'examen' => 'rubro5',
    ];
    $rubroActual = $rubrosPorTipo[$tipo] ?? null;

    if ($rubroActual) {
        // Prepara los datos de los aspectos
        $aspectosData = [];
        for ($i = 1; $i <= 5; $i++) {
            $aspectosData["etiqueta_aspecto$i"] = $aspectos[$i][$tipo] ?? null;
        }

        // Actualiza o inserta en la tabla etiquetas_rubros
        \DB::table('etiquetas_rubros')->updateOrInsert(
            [
                'user_id' => $user_id,
                'grado' => $grado,
                'grupo' => $grupo,
                'etiqueta_nombre' => $rubroActual,
            ],
            $aspectosData + [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
            




        // Redirigir con éxito
        return redirect()->back()->with('success', 'Calificaciones guardadas correctamente.');
    }

    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function guardarElementosCorrectos(Request $request)
{
    $request->validate([
        'grado' => 'required|string',
        'grupo' => 'required|string',
        'tipo_pestania' => 'required|string',
        'pestania_index' => 'required|integer',
        'evaluacion' => 'required|integer',
        'elementos_correctos' => 'required|numeric',
    ]);

    $userId = auth()->id();
    $grado = $request->grado;
    $grupo = $request->grupo;
    $tipoPestania = $request->tipo_pestania;
    $pestaniaIndex = $request->pestania_index;
    $evaluacionIndex = $request->evaluacion;
    $valorElementosCorrectos = $request->elementos_correctos;

    $campo = "valor_maximo" . $evaluacionIndex;

    // Obtener todos los alumnos que coincidan con user_id, grado y grupo
    $alumnos = \App\Models\Alumno::where('user_id', $userId)
        ->where('grado', $grado)
        ->where('grupo', $grupo)
        ->get();

    foreach ($alumnos as $alumno) {
        // Buscar si ya existe el registro en CalificarCotejo para ese alumno
        $existingRecord = \App\Models\CalificarCotejo::where('alumno_id', $alumno->id)
            ->where('user_id', $userId)
            ->where($tipoPestania . '_id', $pestaniaIndex)
            ->where('periodo', now()->year)
            ->first();

        if ($existingRecord) {
            $existingRecord->update([
                $campo => $valorElementosCorrectos,
            ]);
        } else {
            \App\Models\CalificarCotejo::create([
                'alumno_id' => $alumno->id,
                'user_id' => $userId,
                $tipoPestania . '_id' => $pestaniaIndex,
                'periodo' => now()->year,
                $campo => $valorElementosCorrectos,
            ]);
        }
    }

    return response()->json(['success' => true]);
}



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
}

