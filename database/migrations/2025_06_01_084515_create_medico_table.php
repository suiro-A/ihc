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
        Schema::create('medico', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->primary();
            $table->unsignedBigInteger('especialidad');
            $table->string('num_colegiatura');
            // $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
            $table->foreign('especialidad')->references('id_especialidad')->on('especialidad')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medico');
    }
};
