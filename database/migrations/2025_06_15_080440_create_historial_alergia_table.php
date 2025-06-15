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
        Schema::create('historial_alergia', function (Blueprint $table) {

            $table->unsignedBigInteger('id_alergia');
            $table->unsignedBigInteger('id_historial');

            // Definir clave primaria compuesta
            $table->primary(['id_alergia', 'id_historial']);

            // Definir claves foráneas
            $table->foreign('id_alergia')->references('id_alergia')->on('alergia')->onDelete('cascade');
            $table->foreign('id_historial')->references('id_historial')->on('historial_clinico')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_alergia');
    }
};
