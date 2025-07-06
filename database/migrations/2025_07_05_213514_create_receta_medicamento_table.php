<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receta_medicamento', function (Blueprint $table) {
            $table->unsignedBigInteger('id_medicamento');
            $table->unsignedBigInteger('id_receta');
            $table->unsignedBigInteger('id_frecuencia');
            $table->string('dosis');
            $table->string('duración');



            // Definir clave primaria compuesta
            $table->primary(['id_medicamento', 'id_receta']);

            // Definir claves foráneas
            $table->foreign('id_medicamento')->references('id_medicamento')->on('medicamento')->onDelete('no action');
            $table->foreign('id_receta')->references('id_receta')->on('receta')->onDelete('cascade');
            $table->foreign('id_frecuencia')->references('id_frecuencia')->on('frecuencia')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receta_medicamento');
    }
};
