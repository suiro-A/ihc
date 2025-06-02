<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('dni')->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['masculino', 'femenino', 'otro']);
            $table->string('telefono');
            $table->string('email')->nullable();
            $table->text('direccion')->nullable();
            $table->text('alergias')->nullable();
            $table->text('enfermedades_cronicas')->nullable();
            $table->text('medicacion_actual')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
