<?php

namespace App\Http\Controllers;

use App\Models\Alumno; // Importa el modelo de Alumno
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    // Mostrar formulario de registro o edición de alumno
    public function create($alumnoEdit = null)
    {
        // Si se pasa el ID de un alumno para editar, obtiene ese alumno
        $alumnoEdit = $alumnoEdit ? Alumno::find($alumnoEdit) : null;

        // Obtener todos los alumnos registrados para el usuario autenticado
        $alumnos = auth()->user()->alumnos;

        // Pasar los datos de los alumnos y el alumno a editar (si existe) a la vista
        return view('Registrar_alumno', compact('alumnos', 'alumnoEdit'));
    }

    // Guardar los datos del alumno
    public function store(Request $request)
    {
        // Validación para los campos
        $request->validate([
            'nombre_alumno' => 'required|string|max:255',
            'grado' => 'required|string|max:255',
            'grupo' => 'required|string|max:255',
            'hombre' => 'nullable|boolean',  // Cambiado a nullable para permitir que no se marque
            'mujer' => 'nullable|boolean',   // Cambiado a nullable para permitir que no se marque
        ]);

        // Crear o actualizar el registro del alumno
        if ($request->has('id')) {
            // Actualizar el alumno existente
            $alumno = Alumno::findOrFail($request->id);
            $alumno->update([
                'nombre_alumno' => $request->nombre_alumno,
                'grado' => $request->grado,
                'grupo' => $request->grupo,
                'hombre' => $request->hombre ? 1 : 0,
                'mujer' => $request->mujer ? 1 : 0,
            ]);
            $message = 'Alumno actualizado correctamente';
        } else {
            // Crear un nuevo alumno si no existe el ID
            $alumno = new Alumno();
            $alumno->nombre_alumno = $request->nombre_alumno;
            $alumno->grado = $request->grado;
            $alumno->grupo = $request->grupo;
            $alumno->hombre = $request->hombre ? 1 : 0;
            $alumno->mujer = $request->mujer ? 1 : 0;
            $alumno->user_id = auth()->id(); // Asignar el user_id del usuario autenticado
            $alumno->save();
            $message = 'Alumno registrado correctamente';
        }

        // Redirigir con mensaje de éxito
        return redirect()->route('alumnos.create')->with('success', $message);
    }

    // Eliminar un alumno
    public function destroy(Alumno $alumno)
    {
        // Asegurarse de que el alumno pertenece al usuario autenticado antes de eliminar
        if ($alumno->user_id == auth()->id()) {
            $alumno->delete();
            return redirect()->route('alumnos.create')->with('success', 'Alumno eliminado correctamente');
        } else {
            return redirect()->route('alumnos.create')->with('error', 'No tienes permiso para eliminar este alumno');
        }
    }

    // Mostrar todos los alumnos del usuario autenticado
    public function index()
    {
        $alumnos = auth()->user()->alumnos;
        return view('alumnos.index', compact('alumnos'));
    }
}
