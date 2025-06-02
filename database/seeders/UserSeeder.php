<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Doctor
        $doctor = User::create([
            'name' => 'Dr. Juan Pérez',
            'email' => 'doctor@clinica.com',
            'password' => Hash::make('password'),
            'telefono' => '612345678',
            'especialidad' => 'Medicina General',
            'is_active' => true,
        ]);
        $doctor->assignRole('doctor');

        // Recepcionista
        $recepcionista = User::create([
            'name' => 'María González',
            'email' => 'recepcionista@clinica.com',
            'password' => Hash::make('password'),
            'telefono' => '698765432',
            'is_active' => true,
        ]);
        $recepcionista->assignRole('recepcionista');

        // Administrativo
        $admin = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'admin@clinica.com',
            'password' => Hash::make('password'),
            'telefono' => '634567890',
            'is_active' => true,
        ]);
        $admin->assignRole('administrativo');
    }
}
