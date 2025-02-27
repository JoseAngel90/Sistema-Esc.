<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluaciones extends Model
{
    use HasFactory;

    protected $table = 'examen_diagnostico'; // Asegurar que usa la tabla correcta
    public $timestamps = false;

    protected $fillable = ['alumno_id', 'calificacion', 'fecha']; // Permitir estos campos
}

