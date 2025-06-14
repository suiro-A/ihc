<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_clinico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('cita_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->text('diagnostico');
            $table->text('indicaciones')->nullable();
            $table->json('receta_medica')->nullable();
            $table->datetime('fecha_consulta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_clinico');
    }
};
