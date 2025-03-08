<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{

    public function store(Request $request)
{
    // Validación de los datos del formulario
    $request->validate([
        'grado' => 'required|string|max:255',
        'grupo' => 'required|string|max:255',
    ]);

    // Crear el nuevo grupo con los datos proporcionados
    $nuevoGrupo = Grupo::create([
        'grado' => $request->grado,
        'grupo' => $request->grupo,
        'periodo' => '2025',  // Puedes poner el valor por defecto o permitir que el usuario lo envíe
        'user_id' => Auth::id(), // Asignar el ID del usuario autenticado
    ]);

   


    // Agregar el nombre del nuevo grupo a la sesión
    session()->flash('nuevoGrupo', $nuevoGrupo->grado . ' ' . $nuevoGrupo->grupo);

    // Redirigir con un mensaje de éxito
    return redirect()->back();
}

public function destroy($id)
{
    $grupo = Grupo::findOrFail($id);
    $grupo->delete();

    // Guardamos en la sesión el id del grupo eliminado para poder restaurarlo
    session()->flash('grupo_eliminado', $grupo->id);

    return redirect()->route('home')->with('success', 'Grupo eliminado exitosamente.');
}


}
