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
                    'calificaciones.Total'
                )
                ->get()
                ->keyBy('alumno_id');
        }

        $registro = CalificarCotejo::first();

        if ($alumnos->isEmpty()) {
            return redirect()->route('evaluacion')->with('error', 'No tienes alumnos registrados en este grado o grupo.');
        }

        return view('CalificarCotejo', compact(
            'alumnos', 'grados', 'grupos', 'gradoFiltro', 'grupoFiltro', 'registro', 'calificacionesPorPestania'
        ));
        
    }
    


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Guardar Rubro en la base de datos
    public function guardarRubro(Request $request)
{
    $request->validate([
        'Rubro1' => 'nullable|numeric|min:0|max:100',
        'Rubro2' => 'nullable|numeric|min:0|max:100',
        'Rubro3' => 'nullable|numeric|min:0|max:100',
        'Rubro4' => 'nullable|numeric|min:0|max:100',
        'Rubro5' => 'nullable|numeric|min:0|max:100',
        'nombreRubro1' => 'nullable|string|max:255',
        'nombreRubro2' => 'nullable|string|max:255',
        'nombreRubro3' => 'nullable|string|max:255',
        'nombreRubro4' => 'nullable|string|max:255',
        'nombreRubro5' => 'nullable|string|max:255',
        'grado' => 'required',
        'grupo' => 'required',
    ]);

    $usuario_id = Auth::id();

    $data = [];

    if (!is_null($request->input('Rubro1'))) $data['rubro1'] = $request->input('Rubro1');
    if (!is_null($request->input('Rubro2'))) $data['rubro2'] = $request->input('Rubro2');
    if (!is_null($request->input('Rubro3'))) $data['rubro3'] = $request->input('Rubro3');
    if (!is_null($request->input('Rubro4'))) $data['rubro4'] = $request->input('Rubro4');
    if (!is_null($request->input('Rubro5'))) $data['rubro5'] = $request->input('Rubro5');

    // Guardar los nombres de los criterios
    if ($request->filled('nombreRubro1')) $data['nombre_rubro1'] = $request->input('nombreRubro1');
    if ($request->filled('nombreRubro2')) $data['nombre_rubro2'] = $request->input('nombreRubro2');
    if ($request->filled('nombreRubro3')) $data['nombre_rubro3'] = $request->input('nombreRubro3');
    if ($request->filled('nombreRubro4')) $data['nombre_rubro4'] = $request->input('nombreRubro4');
    if ($request->filled('nombreRubro5')) $data['nombre_rubro5'] = $request->input('nombreRubro5');

    if (empty($data)) {
        return redirect()->back()->with('error', 'No se ingresó ningún valor para guardar.');
    }

    // Actualiza o crea el registro para el usuario, grado y grupo
    CalificarCotejo::updateOrCreate(
        [
            'user_id' => $usuario_id,
            'grado' => $request->input('grado'),
            'grupo' => $request->input('grupo'),
        ],
        $data
        
    );

    return redirect()->back()->with('success', 'Rubro(s) guardado(s) correctamente.');
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
    
        // Obtener las calificaciones de los alumnos
        $evaluaciones = $request->input('evaluaciones');
        $elementosCorrectos = $request->input('elementos_correctos');
    
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

                // Solo actualiza el valor máximo si viene en el request
                $campoElementoCorrecto = 'valor_maximo' . $i;
                if (
                    isset($elementosCorrectos[$alumno_id]) &&
                    isset($elementosCorrectos[$alumno_id][$i]) &&
                    isset($elementosCorrectos[$alumno_id][$i][$tipoPestania])
                ) {
                    $datosEvaluacion[$campoElementoCorrecto] = $elementosCorrectos[$alumno_id][$i][$tipoPestania];
                }
            }

            // Calcular el promedio si es necesario
            $valoresValidos = array_filter($datosEvaluacion, fn($valor) => $valor !== null);
            $promedio = count($valoresValidos) > 0 ? array_sum($valoresValidos) / count($valoresValidos) : null;
            $datosEvaluacion['Total'] = $promedio;

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
    
        // Redirigir con éxito
        return redirect()->back()->with('success', 'Calificaciones guardadas correctamente.');
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function guardarElementosCorrectos(Request $request)
{
    $request->validate([
        'alumnos' => 'required|array',
        'alumnos.*.id' => 'required|integer',
        'tipo_pestania' => 'required|string',
        'pestania_index' => 'required|integer',
        'alumnos.*.evaluaciones' => 'required|array',
        'alumnos.*.evaluaciones.*.evaluacion' => 'required|integer',
        'alumnos.*.evaluaciones.*.elementos_correctos' => 'required|numeric',
    ]);

    $userId = auth()->id();
    $tipoPestania = $request->tipo_pestania;
    $pestaniaIndex = $request->pestania_index;

    foreach ($request->alumnos as $alumno) {
        $alumnoId = $alumno['id'];
        foreach ($alumno['evaluaciones'] as $evaluacion) {
            $campo = "valor_maximo" . $evaluacion['evaluacion'];

            // Buscar el registro correcto por pestaña y periodo
            $existingRecord = \App\Models\CalificarCotejo::where([
                'alumno_id' => $alumnoId,
                'user_id' => $userId,
                $tipoPestania . '_id' => $pestaniaIndex,
                'periodo' => now()->year,
            ])->first();

            if ($existingRecord) {
                // Actualiza el valor máximo
                $existingRecord->update([
                    $campo => $evaluacion['elementos_correctos'],
                ]);
            } else {
                // Si no existe, crea el registro con el valor máximo
                \App\Models\CalificarCotejo::create([
                    'alumno_id' => $alumnoId,
                    'user_id' => $userId,
                    $tipoPestania . '_id' => $pestaniaIndex,
                    'periodo' => now()->year,
                    $campo => $evaluacion['elementos_correctos'],
                ]);
            }
        }
    }

    return response()->json(['success' => true]);
}


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
}

