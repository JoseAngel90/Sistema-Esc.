<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionGeneral extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_general';

    protected $fillable = [
        'alumno_id',
        'promedio',
    ];
}