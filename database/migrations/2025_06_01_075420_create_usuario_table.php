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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombres',40);
            $table->string('apellidos',40);
            // $table->string('DNI',40);
            // $table->date('fecha_nac');
            // $table->boolean('sexo');
            $table->string('telefono',40);
            $table->string('correo',40);
            $table->string('clave');
            $table->unsignedBigInteger('rol');
            $table->timestamps();

            $table->foreign('rol')->references('id_rol')->on('rol')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
