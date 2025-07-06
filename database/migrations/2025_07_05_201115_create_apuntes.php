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
        Schema::create('apuntes', function (Blueprint $table) {
            $table->id('id_apunte');
            $table->unsignedBigInteger('id_cita');
            $table->text('sintomas_reportados');
            $table->text('exploracion_fisica');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apuntes');
    }
};
