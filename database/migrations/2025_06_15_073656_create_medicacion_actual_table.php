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
        Schema::create('medicacion_actual', function (Blueprint $table) {
            $table->unsignedBigInteger('id_historial');
            $table->unsignedBigInteger('id_medicamento');


            // Definir clave primaria compuesta
            $table->primary(['id_medicamento', 'id_historial']);

            // Definir claves foráneas
            $table->foreign('id_medicamento')->references('id_medicamento')->on('medicamento')->onDelete('cascade');
            $table->foreign('id_historial')->references('id_historial')->on('historial_clinico')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicacion_actual');
    }
};
