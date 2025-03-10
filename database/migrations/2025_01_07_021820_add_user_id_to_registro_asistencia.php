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
        Schema::table('registro_asistencia', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('user_id')->nullable(); // Agregar el campo user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registro_asistencia', function (Blueprint $table) {
            //
            $table->dropColumn('user_id');

        });
    }
};
