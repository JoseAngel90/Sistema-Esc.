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
        $grupos = Auth::user()->grupos()->get();
        
        return view('home', compact('datosGenerales', 'grupos'));
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
            'nombre_profesor' => 'required|string|max:255',
            'periodo' => 'required|string|max:255',
            'nombre_y_clave' => 'required|string|max:255',
            'localidad' => 'required|string|max:255',
            'clave_ct' => 'required|string|max:255',
        ]);
    
        // Buscar si ya existe un registro para el usuario
        $datosGenerales = DatosGenerales::where('user_id', Auth::id())->first();
    
        // Si no existe, lo creamos
        if (!$datosGenerales) {
        DatosGenerales::create([
            'nombre_escuela' => $request->nombre_escuela,
            'ciclo_escolar' => $request->ciclo_escolar,
            'turno' => $request->turno,
            'asignatura' => $request->asignatura,
            'nombre_profesor' => $request->nombre_profesor,
            'periodo' => $request->periodo,
            'nombre_y_clave' => $request->nombre_y_clave,
            'localidad' => $request->localidad,
            'clave_ct' => $request->clave_ct,
            'user_id' => Auth::id(),
        ]);
        return redirect()->route('home')->with('success', 'Datos Generales guardados correctamente.');
    } 
    
        // Si el registro ya existe, actualizar los datos
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
        'nombre_profesor' => 'required|string|max:255',
        'periodo' => 'required|string|max:255',
        'nombre_y_clave' => 'required|string|max:255',
        'localidad' => 'required|string|max:255',
        'clave_ct' => 'required|string|max:255',
    ]);

    
    $datosGenerales = DatosGenerales::where('user_id', Auth::id())->first();

    if ($datosGenerales) {
        $datosGenerales->update($request->all());
        return redirect()->route('home')->with('success', 'Datos Generales actualizados correctamente.');
    }

    return redirect()->route('home')->with('error', 'No se encontraron datos para actualizar.');
}

}