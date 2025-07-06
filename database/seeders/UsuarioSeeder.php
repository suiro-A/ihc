<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen usuarios para evitar duplicados
        if (DB::table('usuario')->count() > 0) {
            return; // Ya hay usuarios, no ejecutar el seeder
        }

        DB::table('usuario')->insert([
            [
                'nombres' => 'Dr. Juan',
                'apellidos' => 'Pérez García',
                'telefono' => '987654321',
                'correo' => 'doctor@hospital.com',
                'clave' => Hash::make('password'),
                'rol' => 1, // Doctor
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'María',
                'apellidos' => 'López Rodríguez',
                'telefono' => '987654322',
                'correo' => 'recepcionista@hospital.com',
                'clave' => Hash::make('password'),
                'rol' => 2, // Recepcionista
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Administrador Silva',
                'telefono' => '987654323',
                'correo' => 'admin@hospital.com',
                'clave' => Hash::make('password'),
                'rol' => 3, // Administrador
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Solo crear el registro de médico si no existe
        if (!DB::table('medico')->where('id_usuario', 1)->exists()) {
            DB::table('medico')->insert([
                'id_usuario' => 1, // ID del doctor
                'especialidad' => 1, // Asumiendo que existe especialidad con ID 1
                'num_colegiatura' => 'CMP12345',
            ]);
        }
    }
}
