<?php

namespace App\Services;

use Carbon\Carbon;

class DataService
{
    public static function getUsers()
    {
        return [
            [
                'id' => 1,
                'name' => 'Dr. Juan Pérez',
                'email' => 'doctor@clinica.com',
                'telefono' => '612345678',
                'especialidad' => 'Medicina General',
                'is_active' => true,
                'role' => 'doctor',
                'created_at' => '2024-01-10',
            ],
            [
                'id' => 2,
                'name' => 'María González',
                'email' => 'recepcionista@clinica.com',
                'telefono' => '698765432',
                'especialidad' => null,
                'is_active' => true,
                'role' => 'recepcionista',
                'created_at' => '2024-02-15',
            ],
            [
                'id' => 3,
                'name' => 'Carlos Rodríguez',
                'email' => 'admin@clinica.com',
                'telefono' => '634567890',
                'especialidad' => null,
                'is_active' => true,
                'role' => 'administrativo',
                'created_at' => '2024-01-05',
            ],
            [
                'id' => 4,
                'name' => 'Dra. Laura Gómez',
                'email' => 'laura.gomez@clinica.com',
                'telefono' => '645123789',
                'especialidad' => 'Cardiología',
                'is_active' => true,
                'role' => 'doctor',
                'created_at' => '2024-03-20',
            ],
            [
                'id' => 5,
                'name' => 'Pedro Sánchez',
                'email' => 'pedro.sanchez@clinica.com',
                'telefono' => '656789123',
                'especialidad' => null,
                'is_active' => false,
                'role' => 'recepcionista',
                'created_at' => '2024-02-10',
            ],
        ];
    }

    public static function getPacientes()
    {
        return [
            [
                'id' => 1,
                'nombre' => 'Ana',
                'apellidos' => 'Martínez',
                'dni' => '12345678A',
                'fecha_nacimiento' => '1988-05-15',
                'genero' => 'femenino',
                'telefono' => '612345678',
                'email' => 'ana.martinez@email.com',
                'direccion' => 'Calle Principal 123',
                'alergias' => 'Penicilina',
                'enfermedades_cronicas' => 'Hipertensión',
                'medicacion_actual' => 'Enalapril 10mg',
                'observaciones' => 'Paciente con seguimiento regular',
            ],
            [
                'id' => 2,
                'nombre' => 'Pedro',
                'apellidos' => 'Sánchez',
                'dni' => '87654321B',
                'fecha_nacimiento' => '1981-03-22',
                'genero' => 'masculino',
                'telefono' => '698765432',
                'email' => 'pedro.sanchez@email.com',
                'direccion' => 'Avenida Central 456',
                'alergias' => null,
                'enfermedades_cronicas' => null,
                'medicacion_actual' => null,
                'observaciones' => null,
            ],
            [
                'id' => 3,
                'nombre' => 'Laura',
                'apellidos' => 'Gómez',
                'dni' => '56781234C',
                'fecha_nacimiento' => '1995-08-10',
                'genero' => 'femenino',
                'telefono' => '634567812',
                'email' => 'laura.gomez@email.com',
                'direccion' => 'Plaza Mayor 789',
                'alergias' => 'Aspirina',
                'enfermedades_cronicas' => null,
                'medicacion_actual' => null,
                'observaciones' => 'Paciente joven, sin antecedentes',
            ],
            [
                'id' => 4,
                'nombre' => 'Carlos',
                'apellidos' => 'Ruiz',
                'dni' => '43218765D',
                'fecha_nacimiento' => '1972-11-30',
                'genero' => 'masculino',
                'telefono' => '678123456',
                'email' => 'carlos.ruiz@email.com',
                'direccion' => 'Calle Secundaria 321',
                'alergias' => null,
                'enfermedades_cronicas' => 'Diabetes tipo 2',
                'medicacion_actual' => 'Metformina 850mg',
                'observaciones' => 'Control diabético mensual',
            ],
            [
                'id' => 5,
                'nombre' => 'Sofía',
                'apellidos' => 'López',
                'dni' => '21876543E',
                'fecha_nacimiento' => '1990-07-25',
                'genero' => 'femenino',
                'telefono' => '645678123',
                'email' => 'sofia.lopez@email.com',
                'direccion' => 'Barrio Norte 654',
                'alergias' => 'Mariscos',
                'enfermedades_cronicas' => null,
                'medicacion_actual' => null,
                'observaciones' => null,
            ],
        ];
    }

    public static function getCitas()
    {
        return [
            [
                'id' => 1,
                'paciente_id' => 1,
                'doctor_id' => 1,
                'fecha' => Carbon::today()->format('Y-m-d'),
                'hora' => '09:00',
                'motivo' => 'Control mensual de tratamiento para hipertensión',
                'estado' => 'agendada',
                'observaciones' => null,
            ],
            [
                'id' => 2,
                'paciente_id' => 2,
                'doctor_id' => 1,
                'fecha' => Carbon::today()->format('Y-m-d'),
                'hora' => '10:30',
                'motivo' => 'Dolor de cabeza persistente',
                'estado' => 'agendada',
                'observaciones' => null,
            ],
            [
                'id' => 3,
                'paciente_id' => 3,
                'doctor_id' => 4,
                'fecha' => Carbon::today()->format('Y-m-d'),
                'hora' => '12:00',
                'motivo' => 'Evaluación cardiológica',
                'estado' => 'agendada',
                'observaciones' => null,
            ],
            [
                'id' => 4,
                'paciente_id' => 4,
                'doctor_id' => 1,
                'fecha' => Carbon::tomorrow()->format('Y-m-d'),
                'hora' => '09:30',
                'motivo' => 'Control diabético',
                'estado' => 'agendada',
                'observaciones' => null,
            ],
            [
                'id' => 5,
                'paciente_id' => 5,
                'doctor_id' => 4,
                'fecha' => Carbon::tomorrow()->format('Y-m-d'),
                'hora' => '11:00',
                'motivo' => 'Consulta general',
                'estado' => 'completada',
                'observaciones' => 'Paciente en buen estado',
            ],
            [
                'id' => 6,
                'paciente_id' => 1,
                'doctor_id' => 1,
                'fecha' => Carbon::yesterday()->format('Y-m-d'),
                'hora' => '15:00',
                'motivo' => 'Seguimiento hipertensión',
                'estado' => 'completada',
                'observaciones' => 'Presión controlada',
            ],
            [
                'id' => 7,
                'paciente_id' => 3,
                'doctor_id' => 1,
                'fecha' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'hora' => '16:30',
                'motivo' => 'Revisión general',
                'estado' => 'cancelada',
                'observaciones' => 'Cancelada por el paciente',
            ],
        ];
    }

    public static function getHistorialClinico()
    {
        return [
            [
                'id' => 1,
                'paciente_id' => 1,
                'cita_id' => 1,
                'doctor_id' => 1,
                'diagnostico' => 'Hipertensión arterial controlada',
                'indicaciones' => 'Continuar con medicación actual. Dieta baja en sodio y ejercicio regular.',
                'receta_medica' => [
                    [
                        'nombre' => 'Enalapril',
                        'dosis' => '10mg',
                        'frecuencia' => '1 vez al día',
                        'duracion' => '30 días'
                    ]
                ],
                'fecha_consulta' => Carbon::yesterday()->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'paciente_id' => 1,
                'cita_id' => null,
                'doctor_id' => 1,
                'diagnostico' => 'Cefalea tensional',
                'indicaciones' => 'Reducir estrés, descanso adecuado y relajación.',
                'receta_medica' => [
                    [
                        'nombre' => 'Paracetamol',
                        'dosis' => '500mg',
                        'frecuencia' => 'Cada 8 horas',
                        'duracion' => '5 días'
                    ]
                ],
                'fecha_consulta' => Carbon::today()->subDays(30)->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'paciente_id' => 5,
                'cita_id' => 5,
                'doctor_id' => 4,
                'diagnostico' => 'Estado general satisfactorio',
                'indicaciones' => 'Mantener hábitos saludables. Control anual.',
                'receta_medica' => null,
                'fecha_consulta' => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    public static function getDisponibilidad()
    {
        return [
            [
                'id' => 1,
                'doctor_id' => 1,
                'fecha' => Carbon::today()->format('Y-m-d'),
                'hora_inicio' => '09:00',
                'hora_fin' => '17:00',
                'disponible' => true,
            ],
            [
                'id' => 2,
                'doctor_id' => 1,
                'fecha' => Carbon::tomorrow()->format('Y-m-d'),
                'hora_inicio' => '09:00',
                'hora_fin' => '13:00',
                'disponible' => true,
            ],
            [
                'id' => 3,
                'doctor_id' => 4,
                'fecha' => Carbon::today()->format('Y-m-d'),
                'hora_inicio' => '10:00',
                'hora_fin' => '18:00',
                'disponible' => true,
            ],
        ];
    }

    public static function getRoles()
    {
        return [
            [
                'id' => 1,
                'name' => 'doctor',
                'display_name' => 'Doctor',
            ],
            [
                'id' => 2,
                'name' => 'recepcionista',
                'display_name' => 'Recepcionista',
            ],
            [
                'id' => 3,
                'name' => 'administrativo',
                'display_name' => 'Administrativo',
            ],
        ];
    }

    public static function getPermisos()
    {
        return [
            'dashboard.view' => ['doctor', 'recepcionista', 'administrativo'],
            'pacientes.view' => ['doctor', 'recepcionista'],
            'pacientes.create' => ['recepcionista'],
            'pacientes.edit' => ['recepcionista'],
            'pacientes.delete' => [],
            'citas.view' => ['doctor', 'recepcionista'],
            'citas.create' => ['recepcionista'],
            'citas.edit' => ['doctor', 'recepcionista'],
            'citas.delete' => ['recepcionista'],
            'historial.view' => ['doctor', 'recepcionista'],
            'historial.create' => ['doctor'],
            'historial.edit' => ['doctor'],
            'recetas.view' => ['doctor', 'recepcionista'],
            'recetas.create' => ['doctor'],
            'recetas.edit' => ['doctor'],
            'recetas.delete' => ['doctor'],
            'usuarios.view' => ['administrativo'],
            'usuarios.create' => ['administrativo'],
            'usuarios.edit' => ['administrativo'],
            'usuarios.delete' => ['administrativo'],
            'roles.view' => ['administrativo'],
            'roles.create' => ['administrativo'],
            'roles.edit' => ['administrativo'],
            'roles.delete' => ['administrativo'],
            'disponibilidad.view' => ['doctor', 'recepcionista', 'administrativo'],
            'disponibilidad.create' => ['administrativo'],
            'disponibilidad.edit' => ['administrativo'],
            'disponibilidad.delete' => ['administrativo'],
        ];
    }

    // Métodos de utilidad para buscar datos
    public static function findUser($id)
    {
        $users = collect(self::getUsers());
        return $users->firstWhere('id', $id);
    }

    public static function findUserByEmail($email)
    {
        $users = collect(self::getUsers());
        return $users->firstWhere('email', $email);
    }

    public static function findPaciente($id)
    {
        $pacientes = collect(self::getPacientes());
        return $pacientes->firstWhere('id', $id);
    }

    public static function findCita($id)
    {
        $citas = collect(self::getCitas());
        return $citas->firstWhere('id', $id);
    }

    public static function getCitasByDoctor($doctorId)
    {
        $citas = collect(self::getCitas());
        return $citas->where('doctor_id', $doctorId);
    }

    public static function getCitasByPaciente($pacienteId)
    {
        $citas = collect(self::getCitas());
        return $citas->where('paciente_id', $pacienteId);
    }

    public static function getCitasHoy()
    {
        $citas = collect(self::getCitas());
        return $citas->where('fecha', Carbon::today()->format('Y-m-d'));
    }

    public static function getHistorialByPaciente($pacienteId)
    {
        $historial = collect(self::getHistorialClinico());
        return $historial->where('paciente_id', $pacienteId);
    }

    public static function getUsersByRole($role)
    {
        $users = collect(self::getUsers());
        return $users->where('role', $role);
    }

    public static function hasRole($userId, $role)
    {
        $user = self::findUser($userId);
        return $user && $user['role'] === $role;
    }

    public static function hasPermission($userId, $permission)
    {
        $user = self::findUser($userId);
        if (!$user) return false;

        $permisos = self::getPermisos();
        return isset($permisos[$permission]) && in_array($user['role'], $permisos[$permission]);
    }

    public static function getEdadPaciente($fechaNacimiento)
    {
        return Carbon::parse($fechaNacimiento)->age;
    }

    public static function getNombreCompleto($paciente)
    {
        return $paciente['nombre'] . ' ' . $paciente['apellidos'];
    }
}
