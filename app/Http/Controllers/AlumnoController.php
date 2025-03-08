<?php

namespace App\Http\Controllers;

use App\Models\Alumno; // Importa el modelo de Alumno
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    // Mostrar formulario de registro o edición de alumno
    public function create(Request $request, $alumnoEdit = null)
    {
        // Si se pasa el ID de un alumno para editar, obtiene ese alumno
        $alumnoEdit = $alumnoEdit ? Alumno::find($alumnoEdit) : null;

        // Obtener todos los alumnos registrados para el usuario autenticado
        $alumnos = auth()->user()->alumnos;

        // Obtener los parámetros de grado y grupo
        $grado = $request->input('grado');
        $grupo = $request->input('grupo');

        // Pasar los datos de los alumnos, el alumno a editar (si existe), grado y grupo a la vista
        return view('Registrar_alumno', compact('alumnos', 'alumnoEdit', 'grado', 'grupo'));
    }

    // Guardar los datos del alumno
    public function store(Request $request)
    {
        // Validación para los campos
        $request->validate([
            'nombre_alumno' => 'required|string|max:255',
            'grado' => 'required|string|max:2',
            'grupo' => 'required|string|max:1',
            'hombre' => 'nullable|boolean',  // Cambiado a nullable para permitir que no se marque
            'mujer' => 'nullable|boolean',   // Cambiado a nullable para permitir que no se marque
        ]);

        // Crear un nuevo alumno
        $alumno = new Alumno();
        $alumno->nombre_alumno = $request->nombre_alumno;
        $alumno->grado = $request->grado;
        $alumno->grupo = $request->grupo;
        $alumno->hombre = $request->hombre ? 1 : 0;
        $alumno->mujer = $request->mujer ? 1 : 0;
        $alumno->user_id = auth()->id(); // Asignar el user_id del usuario autenticado
        $alumno->save();

        $message = 'Alumno registrado correctamente';

        // Redirigir con mensaje de éxito y pasar los parámetros de grado y grupo
        return redirect()->route('alumnos.index', ['grado' => $request->grado, 'grupo' => $request->grupo])->with('success', $message);
    }

    // Actualizar los datos del alumno
    public function update(Request $request, $id)
    {
        // Validación para los campos
        $request->validate([
            'nombre_alumno' => 'required|string|max:255',
            'grado' => 'required|string|max:2',
            'grupo' => 'required|string|max:1',
            'hombre' => 'nullable|boolean',
            'mujer' => 'nullable|boolean',
        ]);

        // Actualizar el alumno existente
        $alumno = Alumno::findOrFail($id);
        $alumno->update([
            'nombre_alumno' => $request->nombre_alumno,
            'grado' => $request->grado,
            'grupo' => $request->grupo,
            'hombre' => $request->hombre ? 1 : 0,
            'mujer' => $request->mujer ? 1 : 0,
        ]);

        return redirect()->route('alumnos.index', ['grado' => $request->grado, 'grupo' => $request->grupo])->with('success', 'Alumno actualizado correctamente');
    }

    // Eliminar un alumno
    public function destroy(Alumno $alumno)
    {
        // Asegurarse de que el alumno pertenece al usuario autenticado antes de eliminar
        if ($alumno->user_id == auth()->id()) {
            $alumno->delete();
            return redirect()->route('alumnos.index', ['grado' => $alumno->grado, 'grupo' => $alumno->grupo])->with('success', 'Alumno eliminado correctamente');
        } else {
            return redirect()->route('alumnos.index', ['grado' => $alumno->grado, 'grupo' => $alumno->grupo])->with('error', 'No tienes permiso para eliminar este alumno');
        }
    }

    // Mostrar todos los alumnos del usuario autenticado
    public function index(Request $request)
    {
        $grado = $request->input('grado');
        $grupo = $request->input('grupo');

        $alumnos = auth()->user()->alumnos()
                        ->where('grado', $grado)
                        ->where('grupo', $grupo)
                        ->get();

        return view('Registrar_alumno', compact('alumnos', 'grado', 'grupo'));
    }
}