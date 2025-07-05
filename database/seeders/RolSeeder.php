<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rol')->insert([
            [
                'rol' => 'Doctor',
            ],
            [
                'rol' => 'Recepcionista',
            ],
            [
                'rol' => 'Administrador',
            ],
        ]);
    }
}
