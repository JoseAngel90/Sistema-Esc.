<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    // Método en el controlador
public function dashboard()
{
    $user = Auth::user();

    // Obtén los datos generales específicos del usuario autenticado
    $datosGenerales = $user->datosGenerales; // Relación `hasOne`

    // Obtén los alumnos específicos del usuario autenticado (si los necesitas)
    $alumnos = $user->alumnos; // Relación `hasMany`

    return view('dashboard', compact('datosGenerales', 'alumnos'));
}

}
