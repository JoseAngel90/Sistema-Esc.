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
    Schema::table('alumnos', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable(); // Columna para almacenar el ID del usuario
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Relación con la tabla de usuarios
    });
}

public function down()
{
    Schema::table('alumnos', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}

};
