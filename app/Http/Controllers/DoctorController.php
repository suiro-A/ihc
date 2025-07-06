<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\HistorialClinico;
use App\Models\Diagnostico;
use App\Models\Indicaciones;
use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
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
        
        // Obtener pacientes de forma más directa
        $pacientes = DB::table('paciente as p')
            ->join('historial_clinico as h', 'p.id_paciente', '=', 'h.id_paciente')
            ->join('cita as c', 'h.id_historial', '=', 'c.id_historial')
            ->join('medico as m', 'c.id_medico', '=', 'm.id_usuario')
            ->where('m.id_usuario', $doctor['id'])
            ->select('p.*')
            ->distinct()
            ->get()
            ->map(function ($paciente) use ($doctor) {
                // Obtener última cita que ya ocurrió (antes o igual a hoy) y fue atendida
                $ultimaCita = DB::table('cita as c')
                    ->join('historial_clinico as h', 'c.id_historial', '=', 'h.id_historial')
                    ->where('h.id_paciente', $paciente->id_paciente)
                    ->where('c.id_medico', $doctor['id'])
                    ->where('c.fecha', '<=', now()->format('Y-m-d'))
                    ->where('c.estado', 'Atendida')
                    ->orderByDesc('c.fecha')
                    ->first();
                
                return [
                    'id' => $paciente->id_paciente,
                    'nombre' => $paciente->nombres,
                    'apellidos' => $paciente->apellidos,
                    'dni' => $paciente->dni,
                    'telefono' => $paciente->telefono,
                    'fecha_nacimiento' => $paciente->fecha_nac,
                    'ultima_cita' => $ultimaCita ? ['fecha' => $ultimaCita->fecha] : null
                ];
            });
        
        return view('doctor.historial.index', compact('pacientes'));
    }

    public function historialPaciente($id)
    {
        $doctor = $this->getCurrentUser();
        
        // Obtener paciente desde la base de datos
        $paciente = DB::table('paciente')->where('id_paciente', $id)->first();
        if (!$paciente) {
            abort(404, 'Paciente no encontrado');
        }
        
        // Convertir a array para compatibilidad con la vista
        $paciente = [
            'id' => $paciente->id_paciente,
            'nombre' => $paciente->nombres,
            'apellidos' => $paciente->apellidos,
            'dni' => $paciente->dni,
            'fecha_nacimiento' => $paciente->fecha_nac
        ];
        
        // Obtener historial desde la base de datos
        $historial = DB::table('cita as c')
            ->join('historial_clinico as h', 'c.id_historial', '=', 'h.id_historial')
            ->leftJoin('apuntes as a', 'c.id_cita', '=', 'a.id_cita')
            ->leftJoin('diagnostico as d', 'c.id_cita', '=', 'd.id_cita')
            ->leftJoin('indicaciones as i', 'c.id_cita', '=', 'i.id_cita')
            ->leftJoin('medico as m', 'c.id_medico', '=', 'm.id_usuario')
            ->leftJoin('usuario as u', 'm.id_usuario', '=', 'u.id_usuario')
            ->where('h.id_paciente', $id)
            ->where('c.id_medico', $doctor['id'])
            ->where('c.estado', 'Atendida')
            ->select('c.fecha as fecha_consulta', 'a.sintomas_reportados', 'a.exploracion_fisica', 
                    'd.descripcion as diagnostico', 'i.descripcion as indicaciones',
                    'u.nombres as doctor_nombre', 'u.apellidos as doctor_apellidos')
            ->orderByDesc('c.fecha')
            ->get()
            ->map(function($consulta) {
                return [
                    'fecha_consulta' => $consulta->fecha_consulta,
                    'doctor_nombre' => trim(($consulta->doctor_nombre ?? '') . ' ' . ($consulta->doctor_apellidos ?? '')),
                    'sintomas_reportados' => $consulta->sintomas_reportados,
                    'exploracion_fisica' => $consulta->exploracion_fisica,
                    'diagnostico' => $consulta->diagnostico,
                    'indicaciones' => $consulta->indicaciones,
                    'examenes' => [], // Por implementar según tu estructura
                    'receta_medica' => [] // Por implementar según tu estructura
                ];
            });
        
        return view('doctor.historial.paciente', compact('paciente', 'historial'));
    }

    public function detalleCita($id)
    {
        $doctor = $this->getCurrentUser();
        
        // Obtener cita usando Eloquent
        $citaModel = Cita::with(['historial.paciente', 'horaConsulta', 'medico.usuario', 'diagnostico', 'indicaciones', 'apuntes'])
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
                'nombres' => $citaModel->historial->paciente->nombres ?? 'Sin nombre',
                'apellidos' => $citaModel->historial->paciente->apellidos ?? 'Sin apellidos',
                'dni' => $citaModel->historial->paciente->dni ?? 'Sin DNI',
                'telefono' => $citaModel->historial->paciente->telefono ?? 'Sin teléfono',
                'correo' => $citaModel->historial->paciente->correo ?? null,
                'fecha_nac' => $citaModel->historial->paciente->fecha_nac ?? null,
            ]
        ];

        // Para diagnóstico y receta seguir usando DataService
        $historial = DataService::getHistorialByPaciente($cita['paciente_id'])
            ->sortByDesc('fecha_consulta')
            ->take(5);

        // Buscar diagnóstico asociado a esta cita desde la base de datos
        $diagnosticoActual = $citaModel->diagnostico;

        // Buscar indicaciones asociadas a esta cita desde la base de datos
        $indicacionesActual = $citaModel->indicaciones;

        // Buscar apuntes asociados a esta cita desde la base de datos
        $apuntesActual = $citaModel->apuntes;

        // Buscar receta médica asociada a esta cita
        $recetaActual = $diagnosticoActual;

        // Datos simulados para las nuevas secciones (en un sistema real vendrían de la base de datos)
        $examenesActual = [];

        // Obtener la pestaña activa (por defecto 'detalle')
        $tab = request('tab', 'detalle');

        $viewData = compact('cita', 'historial', 'diagnosticoActual', 'recetaActual', 'apuntesActual', 'examenesActual', 'indicacionesActual', 'tab');

        // Si es una petición AJAX, devolver solo el contenido necesario
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('doctor.citas.detalle', $viewData)->render()
            ]);
        }

        return view('doctor.citas.detalle', $viewData);
    }

    public function guardarDiagnostico(Request $request, $citaId)
    {
        $request->validate([
            'diagnostico' => 'required|string|max:255'
        ], [
            'diagnostico.required' => 'El campo diagnóstico es obligatorio.',
            'diagnostico.string' => 'El diagnóstico debe ser un texto válido.',
            'diagnostico.max' => 'El diagnóstico no puede tener más de 255 caracteres.'
        ]);

        try {
            // Verificar que la cita existe y pertenece al doctor actual
            $doctor = $this->getCurrentUser();
            $cita = Cita::with(['historial.paciente'])
                        ->where('id_cita', $citaId)
                        ->whereHas('medico.usuario', function ($q) use ($doctor) {
                            $q->where('id_usuario', $doctor['id']);
                        })
                        ->first();

            if (!$cita) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Cita no encontrada']);
                }
                
                session()->flash('swal', [
                    'title' => 'Error',
                    'text' => 'Cita no encontrada',
                    'icon' => 'error'
                ]);
                
                return redirect()->back();
            }

            // Buscar si ya existe un diagnóstico para esta cita
            $diagnostico = \App\Models\Diagnostico::where('id_cita', $citaId)->first();
            $esActualizacion = $diagnostico !== null;

            if ($diagnostico) {
                // Actualizar diagnóstico existente
                $diagnostico->update([
                    'descripcion' => $request->diagnostico
                ]);
                $titulo = '¡Actualizado!';
                $mensaje = 'Diagnóstico actualizado correctamente';
            } else {
                // Crear nuevo diagnóstico
                \App\Models\Diagnostico::create([
                    'id_cita' => $citaId,
                    'descripcion' => $request->diagnostico
                ]);
                $titulo = '¡Guardado!';
                $mensaje = 'Diagnóstico guardado correctamente';
            }
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $mensaje]);
            }
            
            // Usar SweetAlert2 para mostrar el mensaje de éxito
            session()->flash('swal', [
                'title' => $titulo,
                'text' => $mensaje,
                'icon' => 'success'
            ]);
            
            return redirect()->route('doctor.citas.detalle', ['id' => $citaId, 'tab' => 'diagnostico']);

        } catch (\Exception $e) {
            \Log::error('Error al guardar diagnóstico: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al guardar el diagnóstico']);
            }
            
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al guardar el diagnóstico',
                'icon' => 'error'
            ]);
            
            return redirect()->back();
        }
    }

    public function guardarReceta(Request $request, $citaId)
    {
        // En un sistema real, aquí guardaríamos en la base de datos
        // Por ahora solo simulamos el éxito sin validaciones

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Receta médica guardada exitosamente']);
        }

        return redirect()->route('doctor.citas.detalle', $citaId)
            ->with('success', 'Receta médica guardada exitosamente.');
    }

    public function guardarApuntes(Request $request, $citaId)
    {
        $request->validate([
            'sintomas_reportados' => 'required|string|max:1000',
            'exploracion_fisica' => 'required|string|max:1000'
        ], [
            'sintomas_reportados.required' => 'El campo síntomas reportados es obligatorio.',
            'sintomas_reportados.string' => 'Los síntomas reportados deben ser un texto válido.',
            'sintomas_reportados.max' => 'Los síntomas reportados no pueden tener más de 1000 caracteres.',
            'exploracion_fisica.required' => 'El campo exploración física es obligatorio.',
            'exploracion_fisica.string' => 'La exploración física debe ser un texto válido.',
            'exploracion_fisica.max' => 'La exploración física no puede tener más de 1000 caracteres.'
        ]);

        try {
            // Verificar que la cita existe y pertenece al doctor actual
            $doctor = $this->getCurrentUser();
            $cita = Cita::with(['historial.paciente'])
                        ->where('id_cita', $citaId)
                        ->whereHas('medico.usuario', function ($q) use ($doctor) {
                            $q->where('id_usuario', $doctor['id']);
                        })
                        ->first();

            if (!$cita) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Cita no encontrada']);
                }
                
                session()->flash('swal', [
                    'title' => 'Error',
                    'text' => 'Cita no encontrada',
                    'icon' => 'error'
                ]);
                
                return redirect()->back();
            }

            // Buscar si ya existen apuntes para esta cita
            $apuntes = \App\Models\Apuntes::where('id_cita', $citaId)->first();
            $esActualizacion = $apuntes !== null;

            if ($apuntes) {
                // Actualizar apuntes existentes
                $apuntes->update([
                    'sintomas_reportados' => $request->sintomas_reportados,
                    'exploracion_fisica' => $request->exploracion_fisica
                ]);
                $titulo = '¡Actualizado!';
                $mensaje = 'Apuntes actualizados correctamente';
            } else {
                // Crear nuevos apuntes
                \App\Models\Apuntes::create([
                    'id_cita' => $citaId,
                    'sintomas_reportados' => $request->sintomas_reportados,
                    'exploracion_fisica' => $request->exploracion_fisica
                ]);
                $titulo = '¡Guardado!';
                $mensaje = 'Apuntes guardados correctamente';
            }
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $mensaje]);
            }
            
            // Usar SweetAlert2 para mostrar el mensaje de éxito
            session()->flash('swal', [
                'title' => $titulo,
                'text' => $mensaje,
                'icon' => 'success'
            ]);
            
            return redirect()->route('doctor.citas.detalle', ['id' => $citaId, 'tab' => 'apuntes']);

        } catch (\Exception $e) {
            \Log::error('Error al guardar apuntes: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al guardar los apuntes']);
            }
            
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al guardar los apuntes',
                'icon' => 'error'
            ]);
            
            return redirect()->back();
        }
    }

    public function guardarExamenes(Request $request, $citaId)
    {
        // En un sistema real, aquí guardaríamos en la base de datos
        // Por ahora solo simulamos el éxito sin validaciones
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Exámenes solicitados exitosamente']);
        }
        
        return redirect()->route('doctor.citas.detalle', ['id' => $citaId, 'tab' => 'examenes'])
                        ->with('success', 'Exámenes solicitados exitosamente.');
    }

    public function guardarIndicaciones(Request $request, $citaId)
    {
        $request->validate([
            'indicaciones' => 'required|string|max:1000'
        ], [
            'indicaciones.required' => 'El campo indicaciones es obligatorio.',
            'indicaciones.string' => 'Las indicaciones deben ser un texto válido.',
            'indicaciones.max' => 'Las indicaciones no pueden tener más de 1000 caracteres.'
        ]);

        try {
            // Verificar que la cita existe y pertenece al doctor actual
            $doctor = $this->getCurrentUser();
            $cita = Cita::with(['historial.paciente'])
                        ->where('id_cita', $citaId)
                        ->whereHas('medico.usuario', function ($q) use ($doctor) {
                            $q->where('id_usuario', $doctor['id']);
                        })
                        ->first();

            if (!$cita) {
                abort(404, 'Cita no encontrada');
            }

            // Crear o actualizar las indicaciones
            $indicaciones = \App\Models\Indicaciones::updateOrCreate(
                ['id_cita' => $citaId],
                ['descripcion' => $request->indicaciones]
            );

            // Determinar si fue creación o actualización
            $titulo = $indicaciones->wasRecentlyCreated ? '¡Guardado!' : '¡Actualizado!';
            $mensaje = $indicaciones->wasRecentlyCreated ? 'Indicaciones guardadas correctamente' : 'Indicaciones actualizadas correctamente';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => $mensaje,
                    'indicaciones' => $indicaciones
                ]);
            }

            // Usar SweetAlert2 para mostrar el mensaje de éxito
            session()->flash('swal', [
                'title' => $titulo,
                'text' => $mensaje,
                'icon' => 'success'
            ]);

            return redirect()->route('doctor.citas.detalle', ['id' => $citaId, 'tab' => 'indicaciones']);

        } catch (\Exception $e) {
            \Log::error('Error al guardar indicaciones: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error al guardar las indicaciones: ' . $e->getMessage()
                ], 500);
            }

            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al guardar las indicaciones',
                'icon' => 'error'
            ]);

            return redirect()->back();
        }
    }

    public function actualizarEstado(Request $request, $citaId)
    {
        $request->validate([
            'estado' => 'required|in:Agendada,Atendida,Ausente'
        ]);

        try {
            // Verificar que la cita existe y pertenece al doctor actual
            $doctor = $this->getCurrentUser();
            $cita = Cita::with(['historial.paciente'])
                        ->where('id_cita', $citaId)
                        ->whereHas('medico.usuario', function ($q) use ($doctor) {
                            $q->where('id_usuario', $doctor['id']);
                        })
                        ->first();

            if (!$cita) {
                session()->flash('swal', [
                    'title' => 'Error',
                    'text' => 'Cita no encontrada',
                    'icon' => 'error'
                ]);
                
                return redirect()->back();
            }

            // Actualizar el estado de la cita
            $cita->update(['estado' => $request->estado]);

            $mensajes = [
                'Agendada' => 'La cita ha sido marcada como agendada',
                'Atendida' => 'La cita ha sido marcada como atendida',
                'Ausente' => 'La cita ha sido marcada como ausente'
            ];

            session()->flash('swal', [
                'title' => '¡Actualizado!',
                'text' => $mensajes[$request->estado],
                'icon' => 'success'
            ]);

            return redirect()->route('doctor.agenda');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado de cita: ' . $e->getMessage());
            
            session()->flash('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar la cita',
                'icon' => 'error'
            ]);
            
            return redirect()->back();
        }
    }
}
