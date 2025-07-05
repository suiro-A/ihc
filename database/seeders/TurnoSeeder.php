<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('turno')->insert([
            [
                'descripcion' => 'Mañana',
                'hora_inicio' => '07:00',
                'hora_fin' => '12:00',
            ],
            [
                'descripcion' => 'Tarde',
                'hora_inicio' => '14:00',
                'hora_fin' => '19:00',
            ],
        ]);
    }
}
