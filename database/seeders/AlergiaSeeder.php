<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlergiaSeeder extends Seeder
{
    public function run(): void
    {
        $alergias = [
            ['descripcion' => 'Polen'],
            ['descripcion' => 'Ácaros del polvo'],
            ['descripcion' => 'Frutos secos'],
            ['descripcion' => 'Mariscos'],
            ['descripcion' => 'Penicilina'],
            ['descripcion' => 'Látex'],
            ['descripcion' => 'Huevos'],
            ['descripcion' => 'Leche'],
            ['descripcion' => 'Chocolate'],
            ['descripcion' => 'Gatos'],
        ];

        foreach ($alergias as $alergia) {
            DB::table('alergia')->insert([
                'descripcion' => $alergia['descripcion'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
