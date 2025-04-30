<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluaciones extends Model
{
    use HasFactory;

   // Especificamos el nombre de la tabla
   protected $table = 'evaluaciones_periodo';

   // Definimos los campos que son asignables de forma masiva
   protected $fillable = [
       'alumno_id',
       'calificacion',
       'fecha',
       'periodo',
       'cerrado',
   ];
    
    
    public function up()
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->integer('grado');
            $table->integer('grupo');
            $table->integer('numero_periodo');
            $table->timestamps();
        });
    }
    
    
}

