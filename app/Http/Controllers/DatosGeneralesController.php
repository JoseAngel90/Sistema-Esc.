<?php

namespace App\Http\Controllers;

use App\Models\DatosGenerales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DatosGeneralesController extends Controller
{
    // Mostrar formulario de registro o edición de datos generales
    public function create()
    {
        // Si el usuario ya tiene datos generales, los muestra
        $datosGenerales = DatosGenerales::where('user_id', Auth::id())->first();
        return view('home', compact('datosGenerales'));
    }

    // Guardar o actualizar los datos generales
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'nombre_escuela' => 'required|string|max:255',
            'ciclo_escolar' => 'required|string|max:255',
            'turno' => 'required|string|max:255',
            'asignatura' => 'required|string|max:255',
            'grado_grupo' => 'required|string|max:255',
            'nombre_profesor' => 'required|string|max:255',
            'periodo' => 'required|string|max:255',
        ]);

        // Buscar el registro de datos generales del usuario autenticado
        $datosGenerales = DatosGenerales::where('user_id', Auth::id())->first();

        // Si no existe el registro, lo creamos
        if (!$datosGenerales) {
            DatosGenerales::create([
                'nombre_escuela' => $request->nombre_escuela,
                'ciclo_escolar' => $request->ciclo_escolar,
                'turno' => $request->turno,
                'asignatura' => $request->asignatura,
                'grado_grupo' => $request->grado_grupo,
                'nombre_profesor' => $request->nombre_profesor,
                'periodo' => $request->periodo,
                'user_id' => Auth::id(), // Asignar el ID del usuario autenticado
            ]);
            return redirect()->route('home')->with('success', 'Datos Generales guardados correctamente.');
        }        

        //Si el registro ya existe, actualizamos los datos
        $datosGenerales->update($request->all());
        return redirect()->route('home')->with('success', 'Datos Generales actualizados correctamente.');
    }

    public function update(Request $request)
{
    $request->validate([
        'nombre_escuela' => 'required|string|max:255',
        'ciclo_escolar' => 'required|string|max:255',
        'turno' => 'required|string|max:255',
        'asignatura' => 'required|string|max:255',
        'grado_grupo' => 'required|string|max:255',
        'nombre_profesor' => 'required|string|max:255',
        'periodo' => 'required|string|max:255',
    ]);

    $datosGenerales = DatosGenerales::where('user_id', Auth::id())->first();

    if ($datosGenerales) {
        $datosGenerales->update($request->all());
        return redirect()->route('home')->with('success', 'Datos Generales actualizados correctamente.');
    }

    return redirect()->route('home')->with('error', 'No se encontraron datos para actualizar.');
}

}
