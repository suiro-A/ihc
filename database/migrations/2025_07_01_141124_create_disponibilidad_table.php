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
        Schema::create('disponibilidad', function (Blueprint $table) {
            $table->id('id_disponibilidad');
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            
            $table->unsignedBigInteger('id_turno');
            $table->unsignedBigInteger('id_usuario');

            // Definir claves foráneas
            $table->foreign('id_usuario')->references('id_usuario')->on('medico')->onDelete('cascade');
            $table->foreign('id_turno')->references('id_turno')->on('turno')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilidad');
    }
};
