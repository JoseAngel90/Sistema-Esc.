<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DatosGenerales;

class Alumno extends Model
{
    use HasFactory;

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con DatosGeneralesEscuela (opcional si la necesitas)
    public function datosGeneralesEscuela()
    {
        return $this->belongsTo(DatosGeneralesEscuela::class, 'user_id', 'user_id');
    }

    // Relación con el modelo RegistroAsistencia
    public function pasedelista()
    {
        return $this->hasMany(RegistroAsistencia::class, 'alumno_id');
    }

    public function examenDiagnostico()
{
    return $this->hasOne(Evaluaciones::class, 'alumno_id');
}


    protected $table = 'alumnos';  // Asegúrate de que el nombre de la tabla sea correcto

    protected $fillable = [
        'nombre_alumno',
        'grado',
        'grupo',
        'hombre',
        'mujer',
    ];

    
}
