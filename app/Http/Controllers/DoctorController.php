<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\HistorialClinico;
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
        $vista = $request->get('vista', 'dia');
        
        // Obtener citas según la vista seleccionada usando Eloquent
        $query = Cita::with(['historial.paciente', 'horaConsulta'])
                     ->whereHas('medico.usuario', function ($q) use ($doctor) {
                         $q->where('id_usuario', $doctor['id']);
                     });
        
        if ($vista === 'dia') {
            $query->where('fecha', $fecha);
        } elseif ($vista === 'semana') {
            $fechaActual = Carbon::parse($fecha);
            $inicioSemana = $fechaActual->copy()->startOfWeek()->format('Y-m-d');
            $finSemana = $fechaActual->copy()->endOfWeek()->format('Y-m-d');
            
            $query->whereBetween('fecha', [$inicioSemana, $finSemana]);
        } else { // mes
            $fechaActual = Carbon::parse($fecha);
            $inicioMes = $fechaActual->copy()->startOfMonth()->format('Y-m-d');
            $finMes = $fechaActual->copy()->endOfMonth()->format('Y-m-d');
            
            $query->whereBetween('fecha', [$inicioMes, $finMes]);
        }
        
        $citas = $query->orderBy('fecha')
                      ->orderBy('id_hora')
                      ->get()
                      ->map(function ($cita) {
                          // Adaptar estructura para mantener compatibilidad con la vista
                          return [
                              'id' => $cita->id_cita,
                              'paciente_id' => $cita->historial->paciente->id_paciente ?? null,
                              'doctor_id' => $cita->id_medico,
                              'fecha' => $cita->fecha->format('Y-m-d'),
                              'hora' => $cita->horaConsulta->hora_inicio ?? '00:00',
                              'motivo' => $cita->motivo,
                              'estado' => $cita->estado,
                              'paciente' => [
                                  'nombre' => $cita->historial->paciente->nombres ?? 'Sin nombre',
                                  'apellidos' => $cita->historial->paciente->apellidos ?? 'Sin apellidos',
                              ]
                          ];
                      });
        
        return view('doctor.agenda', compact('citas', 'fecha', 'vista'));
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
        
        // Obtener cita usando Eloquent
        $citaModel = Cita::with(['historial.paciente', 'horaConsulta', 'medico.usuario'])
                         ->where('id_cita', $id)
                         ->whereHas('medico.usuario', function ($q) use ($doctor) {
                             $q->where('id_usuario', $doctor['id']);
                         })
                         ->first();

        if (!$citaModel) {
            abort(404, 'Cita no encontrada');
        }

        // Adaptar estructura para mantener compatibilidad con la vista
        $cita = [
            'id' => $citaModel->id_cita,
            'paciente_id' => $citaModel->historial->paciente->id_paciente ?? null,
            'doctor_id' => $citaModel->id_medico,
            'fecha' => $citaModel->fecha->format('Y-m-d'),
            'hora' => $citaModel->horaConsulta->hora_inicio ?? '00:00',
            'motivo' => $citaModel->motivo,
            'estado' => $citaModel->estado,
            'paciente' => [
                'id' => $citaModel->historial->paciente->id_paciente ?? null,
                'nombre' => $citaModel->historial->paciente->nombres ?? 'Sin nombre',
                'apellidos' => $citaModel->historial->paciente->apellidos ?? 'Sin apellidos',
                'dni' => $citaModel->historial->paciente->dni ?? 'Sin DNI',
                'telefono' => $citaModel->historial->paciente->telefono ?? 'Sin teléfono',
                'email' => $citaModel->historial->paciente->correo ?? null,
                'fecha_nacimiento' => $citaModel->historial->paciente->fecha_nac ?? null,
            ]
        ];

        // Para diagnóstico y receta seguir usando DataService
        $historial = DataService::getHistorialByPaciente($cita['paciente_id'])
            ->sortByDesc('fecha_consulta')
            ->take(5);

        // Buscar diagnóstico asociado a esta cita
        $diagnosticoActual = collect(DataService::getHistorialByPaciente($cita['paciente_id']))
            ->where('cita_id', $cita['id'])
            ->first();

        // Buscar receta médica asociada a esta cita
        $recetaActual = $diagnosticoActual;

        return view('doctor.citas.detalle', compact('cita', 'historial', 'diagnosticoActual', 'recetaActual'));
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

    public function guardarReceta(Request $request, $citaId)
    {
        $request->validate([
            'medicamento' => 'required|string',
            'dosis' => 'required|string',
            'frecuencia' => 'required|string',
            'duracion' => 'required|string',
        ]);

        $doctor = $this->getCurrentUser();
        $cita = DataService::findCita($citaId);

        if (!$cita || $cita['doctor_id'] != $doctor['id']) {
            abort(404, 'Cita no encontrada');
        }

        // Aquí guardarías la receta en la base de datos o servicio correspondiente

        return redirect()->route('doctor.citas.detalle', $citaId)
            ->with('success', 'Receta médica guardada exitosamente.');
    }
}
