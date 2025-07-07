<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            EspecialidadSeeder::class,
            TurnoSeeder::class,
            UsuarioSeeder::class,
            FrecuenciaSeeder::class,
            MedicamentoSeeder::class,
            AlergiaSeeder::class,
            EnfermedadCronicaSeeder::class,

        ]);
    }
}
