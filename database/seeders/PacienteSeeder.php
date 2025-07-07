<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $nombres = ['Juan', 'Ana', 'Pedro', 'Luisa', 'Carlos', 'Elena', 'Mario', 'Sofía', 'José', 'María'];
        $apellidos = ['García', 'Pérez', 'López', 'Sánchez', 'Ramírez', 'Torres', 'Vargas', 'Ruiz', 'Castillo', 'Flores'];
        $correosBase = ['gmail.com', 'yahoo.com', 'hotmail.com'];

        for ($i = 1; $i <= 400; $i++) {
            $nombre = $nombres[array_rand($nombres)];
            $apellido = $apellidos[array_rand($apellidos)];
            $segundoApellido = $apellidos[array_rand($apellidos)];
            $dni = str_pad($i, 8, '0', STR_PAD_LEFT);
            $telefono = '9' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $correo = strtolower($nombre . '.' . $apellido . $i . '@' . $correosBase[array_rand($correosBase)]);
            
            DB::table('paciente')->insert([
                'nombres'     => $nombre,
                'apellidos'   => $apellido . ' ' . $segundoApellido,
                'dni'         => $dni,
                'fecha_nac'   => now()->subYears(rand(18, 60))->format('Y-m-d'),
                'sexo'        => rand(0, 1),
                'telefono'    => $telefono,
                'correo'      => $correo,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
