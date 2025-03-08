<?php

namespace App\Http\Controllers;

use App\Models\Alumno; // Asegúrate de importar el modelo de Alumno
use Illuminate\Http\Request;
use App\Models\Grupo;

class PanelController extends Controller
{
    /**
     * Mostrar el panel del grupo.
     *
     * @param  string  $grado
     * @param  string  $grupo
     * @return \Illuminate\View\View
     */
    public function showPanel($grado, $grupo)
    {
        // Verificar si existen alumnos en el grupo
        $existenAlumnos = Alumno::where('grado', $grado)
            ->where('grupo', $grupo)
            ->exists();

        // Retornar la vista del panel con la información del grupo y la existencia de alumnos
        return view('panel', compact('grado', 'grupo', 'existenAlumnos'));
    }
}
