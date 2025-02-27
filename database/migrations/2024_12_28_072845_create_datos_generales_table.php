<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatosGeneralesTable extends Migration
{
    public function up()
{
    Schema::create('datos_generales_escuela', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('nombre_escuela');
        $table->string('ciclo_escolar');
        $table->string('turno');
        $table->string('asignatura');
        $table->string('grado_grupo');
        $table->string('nombre_profesor');
        $table->string('periodo');
        $table->timestamps();
    });

    Schema::table('datos_generales_escuela', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->change(); // Hacer que user_id sea nullable
    });
    
    
}
}