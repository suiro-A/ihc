<?php

namespace Database\Seeders;

use App\Models\Alergia;
use App\Models\EnfermedadCronica;
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
            AlergiaSeeder::class,
            EnfermedadCronicaSeeder::class,
            MedicamentoSeeder::class
        ]);
    }
}
