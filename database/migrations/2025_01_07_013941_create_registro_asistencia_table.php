<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_asistencia', function (Blueprint $table) {
            $table->id();  // id de registro_asistencia
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade'); // referencia a la tabla alumnos
            $table->date('fecha');
            $table->string('asistencia');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_asistencia');
    }
};
