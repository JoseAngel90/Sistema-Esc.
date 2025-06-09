<?php

namespace App\Http\Controllers;

use App\Models\Alumno; // Asegúrate de importar el modelo de Alumno
use Illuminate\Http\Request;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth; // Importa Auth para obtener el usuario autenticado

class PanelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el panel del grupo y los detalles del usuario.
     *
     * @param  string  $grado
     * @param  string  $grupo
     * @return \Illuminate\View\View
     */
    public function showPanel($grado, $grupo)
    {
        // Obtener los detalles del usuario autenticado
        $usuario = Auth::user(); 

        // Verificar si existen alumnos en el grupo
        $existenAlumnos = Alumno::where('grado', $grado)
            ->where('grupo', $grupo)
            ->exists();
    
        // Retornar la vista del panel con la información del grupo, la existencia de alumnos y los detalles del usuario
        return view('Panel', compact('grado', 'grupo', 'existenAlumnos', 'usuario'));
    }
}
