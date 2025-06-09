<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtiquetaRubro;
use Illuminate\Support\Facades\Auth;

class EtiquetaRubroController extends Controller  // <- corregido aquí
{
    public function guardar(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'etiqueta' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $grado = $request->input('grado', null);
        $grupo = $request->input('grupo', null);

        $etiquetaRubro = EtiquetaRubro::updateOrCreate(
            [
                'user_id' => $user->id,
                'tipo' => $request->tipo,
                'grado' => $grado,
                'grupo' => $grupo,
            ],
            [
                'etiqueta' => $request->etiqueta,
            ]
        );

        return response()->json(['success' => true, 'mensaje' => 'Etiqueta guardada correctamente.']);
    }

    // El método mostrarEtiquetas está bien, no es necesario cambiar
}
