<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pasedelista extends Model
{
    use HasFactory;

    public $timestamps = false;


    protected $table = 'registro_asistencia'; // Nombre de la tabla
    protected $fillable = [
        'alumno_id',
        'fecha',
        'asistencia',
        'user_id',
    ];

    // Relación con el modelo Alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}
