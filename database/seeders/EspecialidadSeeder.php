<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen especialidades para evitar duplicados
        if (DB::table('especialidad')->count() > 0) {
            return; // Ya hay especialidades, no ejecutar el seeder
        }

        DB::table('especialidad')->insert([
            [
                'id_especialidad' => 1,
                'nombre' => 'Medicina General',
            ],
            [
                'id_especialidad' => 2,
                'nombre' => 'Cardiología',
            ],
            [
                'id_especialidad' => 3,
                'nombre' => 'Pediatría',
            ],
            [
                'id_especialidad' => 4,
                'nombre' => 'Ginecología',
            ],
            [
                'id_especialidad' => 5,
                'nombre' => 'Dermatología',
            ],
            [
                'id_especialidad' => 6,
                'nombre' => 'Traumatología',
            ],
            [
                'id_especialidad' => 7,
                'nombre' => 'Oftalmología',
            ],
            [
                'id_especialidad' => 8,
                'nombre' => 'Neurología',
            ],
        ]);
    }
}
