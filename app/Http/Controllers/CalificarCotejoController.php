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
    // Validar solo si se proporciona el valor
    $request->validate([
        'Rubro1' => 'nullable|numeric|min:0|max:100',
        'Rubro2' => 'nullable|numeric|min:0|max:100',
        'Rubro3' => 'nullable|numeric|min:0|max:100',
        'Rubro4' => 'nullable|numeric|min:0|max:100',
        'Rubro5' => 'nullable|numeric|min:0|max:100',
    ]);

    // Obtener el usuario autenticado
    $usuario_id = Auth::id();

    // Preparar solo los campos que tienen valor
    $data = [];

    if (!is_null($request->input('Rubro1'))) $data['rubro1'] = $request->input('Rubro1');
    if (!is_null($request->input('Rubro2'))) $data['rubro2'] = $request->input('Rubro2');
    if (!is_null($request->input('Rubro3'))) $data['rubro3'] = $request->input('Rubro3');
    if (!is_null($request->input('Rubro4'))) $data['rubro4'] = $request->input('Rubro4');
    if (!is_null($request->input('Rubro5'))) $data['rubro5'] = $request->input('Rubro5');

    // Si no hay datos, no guardar nada
    if (empty($data)) {
        return redirect()->back()->with('error', 'No se ingresó ningún valor para guardar.');
    }

    // Crear o actualizar el registro con solo los campos con valor
    CalificarCotejo::updateOrCreate(
        ['user_id' => $usuario_id],
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
    
            // Determinar el número máximo de evaluaciones (5 para 'apoyo_p' y 3 para el resto)
            $maxEvaluaciones = ($tipoPestania === 'apoyo_p') ? 5 : 3;
    
            // Recorrer las evaluaciones y guardar los datos
            for ($i = 1; $i <= $maxEvaluaciones; $i++) {
                $key = 'eval' . $i;
                $campo = 'evaluacion_' . $i;
    
                $valor = $evaluacionAlumno[$key] ?? null;
                $datosEvaluacion[$campo] = ($tipoPestania === 'apoyo_p' && $valor !== null) ? $valor * 100 : $valor;
            }
    
            // Guardar los elementos correctos en el campo adecuado (valor_maximo1, valor_maximo2, etc.)
            for ($i = 1; $i <= $maxEvaluaciones; $i++) {
                $campoElementoCorrecto = 'valor_maximo' . $i;  // valor_maximo1, valor_maximo2, etc.
                $elementosCorrectosValor = $elementosCorrectos[$alumno_id][$i] ?? null;  // Obtener el valor de elementos_correctos por evaluación
    
                if ($elementosCorrectosValor !== null) {
                    $datosEvaluacion[$campoElementoCorrecto] = $elementosCorrectosValor;
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
                    $campoPestania => $pestaniaIndex, // Aquí se guarda el valor correspondiente a la pestaña
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
    // Validación de los datos recibidos
    $request->validate([
        'alumnos' => 'required|array',
        'alumnos.*.id' => 'required|integer',
        'tipo_pestania' => 'required|string',
        'alumnos.*.evaluaciones' => 'required|array',
        'alumnos.*.evaluaciones.*.evaluacion' => 'required|integer',
        'alumnos.*.evaluaciones.*.elementos_correctos' => 'required|numeric',
    ]);

    $userId = auth()->id();  // Obtener el ID del usuario autenticado

    // Iterar sobre cada alumno
    foreach ($request->alumnos as $alumno) {
        $alumnoId = $alumno['id'];

        // Iterar sobre las evaluaciones de cada alumno
        foreach ($alumno['evaluaciones'] as $evaluacion) {
            $campo = "valor_maximo" . $evaluacion['evaluacion'];  // Campo correspondiente a la evaluación

            // Buscar si existe un registro con alumno_id, user_id y tipo_pestania
            $existingRecord = CalificarCotejo::where([
                'alumno_id' => $alumnoId,
                'user_id' => $userId,
                $request->tipo_pestania . '_id' => 1,  // Ajusta este valor según el tipo de pestaña
            ])->first();

            // Si el registro existe, actualizarlo
            if ($existingRecord) {
                $existingRecord->update([
                    $campo => $evaluacion['elementos_correctos'],  // Actualizar el valor correcto
                ]);
            } else {
                // Si el registro no existe, crearlo
                CalificarCotejo::create([
                    'alumno_id' => $alumnoId,
                    'user_id' => $userId,
                    $request->tipo_pestania . '_id' => 1,  // Ajusta este valor según el tipo de pestaña
                    $campo => $evaluacion['elementos_correctos'],  // Guardar el valor correcto
                ]);
            }
        }
    }

    // Responder con éxito
    return response()->json(['success' => true]);
}


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
}

