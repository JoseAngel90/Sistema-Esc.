<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalificarCotejo extends Model
{
    use HasFactory;

    // Especificamos el nombre de la tabla
    protected $table = 'calificaciones';

    // Definimos los campos que son asignables de forma masiva
    protected $fillable = [
        'user_id',
        'alumno_id',
        'periodo',
        'apoyo_p_id',
        'proyectos_id',
        'trabajos_clase_id',
        'tareas_id',
        'examen_id',
        'evaluacion_1',
        'evaluacion_2',
        'evaluacion_3',
        'evaluacion_4',
        'evaluacion_5',
        'rubro1',
        'rubro2',
        'rubro3',
        'rubro4',
        'rubro5',
        'Total',
        'valor_maximo1',
        'valor_maximo2',
        'valor_maximo3',
    ];
}








