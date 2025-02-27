<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_alumnos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosTable extends Migration
{
    public function up()
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('nombre_alumno');
            $table->string('grado');
            $table->string('grupo');
            $table->integer('hombre')->default(0); // Contador de hombres
            $table->integer('mujer')->default(0);  // Contador de mujeres
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('alumnos');
    }
}
