<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosGenerales extends Model
{
    use HasFactory;

    // Especifica la tabla si no sigue la convención de nombres de Laravel
    protected $table = 'datos_generales_escuela';

    // Definir los campos que se pueden llenar
    protected $fillable = [
        'user_id', 'nombre_escuela', 'ciclo_escolar', 'turno', 'asignatura',
        'grado_grupo', 'nombre_profesor', 'periodo'
    ];

    // Relación con el modelo de User
    public function user()
    {
        return $this->belongsTo(User::class); // Esto establece que un dato general pertenece a un usuario
    }
}
