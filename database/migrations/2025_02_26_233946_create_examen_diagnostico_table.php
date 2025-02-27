<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('examen_diagnostico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->decimal('calificacion', 5, 2);
            $table->timestamp('fecha')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('examen_diagnostico');
    }
};
