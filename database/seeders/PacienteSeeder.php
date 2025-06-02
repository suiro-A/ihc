<?php

namespace Database\Seeders;

use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        Paciente::create([
            'nombre' => 'Ana',
            'apellidos' => 'Martínez',
            'dni' => '12345678A',
            'fecha_nacimiento' => '1988-05-15',
            'genero' => 'femenino',
            'telefono' => '612345678',
            'email' => 'ana.martinez@email.com',
            'alergias' => 'Penicilina',
            'enfermedades_cronicas' => 'Hipertensión',
            'medicacion_actual' => 'Enalapril 10mg',
        ]);

        Paciente::create([
            'nombre' => 'Pedro',
            'apellidos' => 'Sánchez',
            'dni' => '87654321B',
            'fecha_nacimiento' => '1981-03-22',
            'genero' => 'masculino',
            'telefono' => '698765432',
            'email' => 'pedro.sanchez@email.com',
        ]);

        Paciente::create([
            'nombre' => 'Laura',
            'apellidos' => 'Gómez',
            'dni' => '56781234C',
            'fecha_nacimiento' => '1995-08-10',
            'genero' => 'femenino',
            'telefono' => '634567812',
            'email' => 'laura.gomez@email.com',
        ]);
    }
}
