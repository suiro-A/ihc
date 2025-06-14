<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DoctorController extends Controller
{
    private function getCurrentUser()
    {
        return Session::get('user_data');
    }

    public function dashboard()
    {
        // $doctor = $this->getCurrentUser();
        
        // $citasHoy = DataService::getCitasHoy()->where('doctor_id', $doctor['id']);
        // $todasCitas = DataService::getCitasByDoctor($doctor['id']);
        // $proximasCitas = $todasCitas->where('estado', 'agendada')
        //                            ->where('fecha', '>=', Carbon::today()->format('Y-m-d'))
        //                            ->sortBy(['fecha', 'hora'])
        //                            ->take(3);
        
        // // Enriquecer citas con datos de pacientes
        // $proximasCitas = $proximasCitas->map(function ($cita) {
        //     $paciente = DataService::findPaciente($cita['paciente_id']);
        //     $cita['paciente'] = $paciente;
        //     return $cita;
        // });

        // $stats = [
        //     'citas_hoy' => $citasHoy->count(),
        //     'proxima_cita' => $proximasCitas->first(),
        //     'pacientes_atendidos' => $citasHoy->where('estado', 'completada')->count(),
        //     'recetas_emitidas' => DataService::getHistorialClinico()
        //                                     ->where('doctor_id', $doctor['id'])
        //                                     ->where('fecha_consulta', '>=', Carbon::today()->format('Y-m-d'))
        //                                     ->whereNotNull('receta_medica')
        //                                     ->count(),
        // ];

        $stats = [
            'citas_hoy' => 11,
            'pacientes_atendidos' => 5,
            'proxima_cita' => [
                'hora' => '10:00 AM',
                'paciente' => ['nombre' => 'Juan Pérez']
            ],
            'recetas_emitidas' => 3,
        ];

        $proximasCitas = [
            ['id' => 1, 'paciente' => ['nombre' => 'Ana López', 'apellidos' => 'García'], 'hora' => '11:00 AM', 'motivo' => 'Consulta general'],
            // Más citas...
        ];

        return view('doctor.dashboard', compact('stats', 'proximasCitas'));
    }

    public function agenda(Request $request)
    {
        $doctor = $this->getCurrentUser();
        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        
        $citas = DataService::getCitasByDoctor($doctor['id'])
                           ->where('fecha', $fecha)
                           ->sortBy('hora');
        
        // Enriquecer con datos de pacientes
        $citas = $citas->map(function ($cita) {
            $paciente = DataService::findPaciente($cita['paciente_id']);
            $cita['paciente'] = $paciente;
            return $cita;
        });
        
        return view('doctor.agenda', compact('citas', 'fecha'));
    }

    public function historial()
    {
        $doctor = $this->getCurrentUser();
        
        // Obtener pacientes que han tenido citas con este doctor
        $citasDoctor = DataService::getCitasByDoctor($doctor['id']);
        $pacienteIds = $citasDoctor->pluck('paciente_id')->unique();
        
        $pacientes = collect(DataService::getPacientes())
                    ->whereIn('id', $pacienteIds)
                    ->map(function ($paciente) use ($doctor) {
                        $ultimaCita = DataService::getCitasByPaciente($paciente['id'])
                                                ->where('doctor_id', $doctor['id'])
                                                ->sortByDesc('fecha')
                                                ->first();
                        $paciente['ultima_cita'] = $ultimaCita;
                        return $paciente;
                    });
        
        return view('doctor.historial.index', compact('pacientes'));
    }

    public function historialPaciente($id)
    {
        $doctor = $this->getCurrentUser();
        $paciente = DataService::findPaciente($id);
        
        if (!$paciente) {
            abort(404, 'Paciente no encontrado');
        }
        
        $historial = DataService::getHistorialByPaciente($id)
                               ->where('doctor_id', $doctor['id'])
                               ->sortByDesc('fecha_consulta');
        
        // Enriquecer historial con datos de citas
        $historial = $historial->map(function ($registro) {
            if ($registro['cita_id']) {
                $cita = DataService::findCita($registro['cita_id']);
                $registro['cita'] = $cita;
            }
            return $registro;
        });
        
        return view('doctor.historial.paciente', compact('paciente', 'historial'));
    }

    public function detalleCita($id)
    {
        $doctor = $this->getCurrentUser();
        $cita = DataService::findCita($id);
        
        if (!$cita || $cita['doctor_id'] != $doctor['id']) {
            abort(404, 'Cita no encontrada');
        }
        
        $paciente = DataService::findPaciente($cita['paciente_id']);
        $cita['paciente'] = $paciente;
        
        $historial = DataService::getHistorialByPaciente($cita['paciente_id'])
                               ->sortByDesc('fecha_consulta')
                               ->take(5);
        
        return view('doctor.citas.detalle', compact('cita', 'historial'));
    }

    public function guardarDiagnostico(Request $request, $citaId)
    {
        $request->validate([
            'diagnostico' => 'required|string',
            'indicaciones' => 'nullable|string',
            'medicamentos' => 'nullable|array',
        ]);

        $doctor = $this->getCurrentUser();
        $cita = DataService::findCita($citaId);
        
        if (!$cita || $cita['doctor_id'] != $doctor['id']) {
            abort(404, 'Cita no encontrada');
        }

        // En un sistema real, aquí guardaríamos en la base de datos
        // Por ahora solo simulamos el éxito
        
        return redirect()->route('doctor.citas.detalle', $citaId)
                        ->with('success', 'Diagnóstico guardado exitosamente.');
    }
}
