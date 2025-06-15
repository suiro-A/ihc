<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MedicamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presentaciones = ['Tableta', 'Jarabe', 'Inyectable', 'Cápsula'];

        for ($i = 1; $i <= 20; $i++) {
            DB::table('medicamento')->insert([
                
                'nombre' => 'Medicamento ' . $i,
                'descripcion' => 'Tratamiento para ' . Str::random(10),
                'presentacion' => $presentaciones[array_rand($presentaciones)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
