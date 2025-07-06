<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Frecuencia;

class FrecuenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frecuencias = [
            ['descripcion' => 'Cada 4 horas'],
            ['descripcion' => 'Cada 6 horas'],
            ['descripcion' => 'Cada 8 horas'],
            ['descripcion' => 'Cada 12 horas'],
            ['descripcion' => 'Cada 24 horas (1 vez al día)'],
            ['descripcion' => '2 veces al día'],
            ['descripcion' => '3 veces al día'],
            ['descripcion' => 'En ayunas'],
            ['descripcion' => 'Después de comidas'],
            ['descripcion' => 'Antes de dormir'],
            ['descripcion' => 'Según necesidad'],
        ];

        foreach ($frecuencias as $frecuencia) {
            Frecuencia::create($frecuencia);
        }
    }
}
