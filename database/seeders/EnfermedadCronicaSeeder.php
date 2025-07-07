<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnfermedadCronicaSeeder extends Seeder
{
    public function run(): void
    {
        $enfermedadesCronicas = [
            ['descripcion' => 'Diabetes tipo 2'],
            ['descripcion' => 'Hipertensión arterial'],
            ['descripcion' => 'Artritis reumatoide'],
            ['descripcion' => 'Asma bronquial'],
            ['descripcion' => 'Enfermedad renal crónica'],
            ['descripcion' => 'Enfermedad pulmonar obstructiva crónica (EPOC)'],
            ['descripcion' => 'Fibromialgia'],
            ['descripcion' => 'Hipotiroidismo'],
            ['descripcion' => 'Osteoporosis'],
            ['descripcion' => 'Migraña crónica'],
        ];

        foreach ($enfermedadesCronicas as $enfermedad) {
            DB::table('enfermedad_cronica')->insert([
                'descripcion' => $enfermedad['descripcion'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
