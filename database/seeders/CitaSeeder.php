<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $doctor = User::role('doctor')->first();
        $pacientes = Paciente::all();

        foreach ($pacientes as $index => $paciente) {
            Cita::create([
                'paciente_id' => $paciente->id,
                'doctor_id' => $doctor->id,
                'fecha' => now()->addDays($index),
                'hora' => '09:' . str_pad($index * 30, 2, '0', STR_PAD_LEFT),
                'motivo' => 'Consulta general',
                'estado' => 'agendada',
            ]);
        }
    }
}
