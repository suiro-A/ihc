<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Paciente;
use App\Models\HistorialClinico;
use App\Models\Alergia;
use App\Models\EnfermedadCronica;
use App\Models\Medicamento;
use App\Models\HoraConsulta;
use App\Models\Cita;

class RecepcionistaController extends Controller
{
    private function getCurrentUser()
    {
        return Session::get('user_data');
    }

    public function dashboard()
    {
        // Obtener citas de hoy desde la base de datos real, solo las agendadas
        $citasHoy = Cita::with([
            'historial.paciente',
            'medico.usuario', 
            'especialidad',
            'horaConsulta'
        ])
        ->whereDate('fecha', Carbon::today())
        ->where('estado', 'Agendada')
        ->orderBy('id_hora')
        ->take(10)
        ->get();

        // Transformar los datos para la vista
        $citasHoy = $citasHoy->map(function ($cita) {
            return [
                'id' => $cita->id_cita,
                'paciente' => [
                    'nombre' => $cita->historial->paciente->nombres,
                    'apellidos' => $cita->historial->paciente->apellidos,
                    'dni' => $cita->historial->paciente->dni,
                ],
                'doctor' => [
                    'name' => $cita->medico->usuario->nombres . ' ' . $cita->medico->usuario->apellidos,
                    'especialidad' => $cita->especialidad->nombre ?? 'Medicina General'
                ],
                'fecha' => $cita->fecha->format('Y-m-d'),
                'hora' => $cita->horaConsulta->hora_inicio,
                'motivo' => $cita->motivo,
                'estado' => $cita->estado,
            ];
        });

        // Calcular estadísticas reales
        $todasLasCitasHoy = Cita::whereDate('fecha', Carbon::today())->get();
        
        $stats = [
            'citas_hoy' => $todasLasCitasHoy->count(),
            'proxima_cita' => $citasHoy->first(),
            'pacientes_registrados' => \App\Models\Paciente::count(),
            'citas_confirmadas' => $citasHoy->count(),
        ];
        
        return view('recepcionista.dashboard', compact('stats', 'citasHoy'));
    }

    public function registrarPaciente()
    {
        $alergias = \App\Models\Alergia::all();
        $cronicas = \App\Models\EnfermedadCronica::all();
        $medicamentos = \App\Models\Medicamento::all();
        return view('recepcionista.pacientes.registrar', compact('alergias', 'cronicas', 'medicamentos'));
    }

public function guardarPaciente(Request $request)
{
    $request->validate([
        'nombres' => 'required|string|max:40',
        'apellidos' => 'required|string|max:40',
        'dni' => 'required|string|max:40|unique:paciente,dni',
        'fecha_nacimiento' => 'required|date',
        'genero' => 'required|in:masculino,femenino',
        'telefono' => 'required|string|max:40',
        'email' => 'nullable|email|max:40',
        'alergias' => 'array',
        'alergias.*' => 'exists:alergia,id_alergia',
        'cronicas' => 'array',
        'cronicas.*' => 'exists:enfermedad_cronica,id_enfermedad',
        'medicamentos' => 'array',
        'medicamentos.*' => 'exists:medicamento,id_medicamento',
    ]);

    try {
        $paciente = Paciente::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'fecha_nac' => $request->fecha_nacimiento,
            'sexo' => $request->genero === 'masculino' ? 1 : 0,
            'telefono' => $request->telefono,
            'correo' => $request->email,
        ]);

        // Crear historial clínico
        $historial = HistorialClinico::create([
            'id_paciente' => $paciente->id_paciente,
        ]);

        // Guardar todas las alergias seleccionadas
        if ($request->has('alergias')) {
            foreach ($request->alergias as $id_alergia) {
                \DB::table('historial_alergia')->insert([
                    'id_historial' => $historial->id_historial,
                    'id_alergia' => $id_alergia,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Guardar todas las enfermedades crónicas seleccionadas
        if ($request->has('cronicas')) {
            foreach ($request->cronicas as $id_enfermedad) {
                \DB::table('historial_enfermedad')->insert([
                    'id_historial' => $historial->id_historial,
                    'id_enfermedad' => $id_enfermedad,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Guardar todos los medicamentos seleccionados
        if ($request->has('medicamentos')) {
            foreach ($request->medicamentos as $id_medicamento) {
                \DB::table('medicacion_actual')->insert([
                    'id_historial' => $historial->id_historial,
                    'id_medicamento' => $id_medicamento,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        session()->flash('pacienteCreate', [
            'title' => "¡Paciente Creado!",
            'text' => "El usuario ha sido creado exitosamente",
            'icon' => "success"
        ]);

        return redirect()->route('recepcionista.citas.agendar');

    } catch (\Exception $e) {
        \Log::error('Error al crear paciente: ' . $e->getMessage());
        
        session()->flash('swal', [
            'title' => "Error",
            'text' => "No se pudo crear el paciente. Inténtalo nuevamente.",
            'icon' => "error"
        ]);

        return back()->withInput();
    }
}

public function buscarPacientes(Request $request)
{
    // Obtener todos los pacientes sin paginación
    $allPacientes = Paciente::orderBy('apellidos')->get();
    
    // Calcular edad y última cita para cada paciente
    $allPacientes->each(function ($paciente) {
        $paciente->edad = $paciente->fecha_nac ? \Carbon\Carbon::parse($paciente->fecha_nac)->age : null;
        $paciente->ultima_cita = '####'; // Cambia esto si tienes la relación
    });

    // Simular paginación manual (5 por página)
    $perPage = 5;
    $currentPage = LengthAwarePaginator::resolveCurrentPage() ?: 1;
    $pacientes = new LengthAwarePaginator(
        $allPacientes->forPage($currentPage, $perPage),
        $allPacientes->count(),
        $perPage,
        $currentPage,
        ['path' => LengthAwarePaginator::resolveCurrentPath()]
    );

    return view('recepcionista.pacientes.buscar', compact('pacientes', 'allPacientes'));
}

    public function editarPaciente($id)
    {
        $paciente = \App\Models\Paciente::findOrFail($id);

        // Obtener todas las opciones
        $alergias = \App\Models\Alergia::all();
        $cronicas = \App\Models\EnfermedadCronica::all();
        $medicamentos = \App\Models\Medicamento::all();

        // Obtener el historial clínico del paciente
        $historial = \App\Models\HistorialClinico::where('id_paciente', $paciente->id_paciente)->first();

        // Arrays de IDs seleccionados
        $alergiasSeleccionadas = [];
        $cronicasSeleccionadas = [];
        $medicamentosSeleccionados = [];

        if ($historial) {
            $alergiasSeleccionadas = \DB::table('historial_alergia')
                ->where('id_historial', $historial->id_historial)
                ->pluck('id_alergia')
                ->toArray();

            $cronicasSeleccionadas = \DB::table('historial_enfermedad')
                ->where('id_historial', $historial->id_historial)
                ->pluck('id_enfermedad')
                ->toArray();

            $medicamentosSeleccionados = \DB::table('medicacion_actual')
                ->where('id_historial', $historial->id_historial)
                ->pluck('id_medicamento')
                ->toArray();
        }


        return view('recepcionista.pacientes.editar', compact(
            'paciente',
            'alergias',
            'cronicas',
            'medicamentos',
            'alergiasSeleccionadas',
            'cronicasSeleccionadas',
            'medicamentosSeleccionados'
        ));
    }

    public function actualizarPaciente(Request $request, $id)
    {
        $paciente = \App\Models\Paciente::findOrFail($id);
        
        $request->validate([
            'nombres' => 'required|string|max:40',
            'apellidos' => 'required|string|max:40',
            'dni' => 'required|string|max:40|unique:paciente,dni,' . $paciente->id_paciente . ',id_paciente',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:masculino,femenino',
            'telefono' => 'required|string|max:40',
            'email' => 'nullable|email|max:40',
            'alergias' => 'array',
            'alergias.*' => 'exists:alergia,id_alergia',
            'cronicas' => 'array',
            'cronicas.*' => 'exists:enfermedad_cronica,id_enfermedad',
            'medicamentos' => 'array',
            'medicamentos.*' => 'exists:medicamento,id_medicamento',
        ]);

        try {
            $paciente->update([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'dni' => $request->dni,
                'fecha_nac' => $request->fecha_nacimiento,
                'sexo' => $request->genero === 'masculino' ? 1 : 0,
                'telefono' => $request->telefono,
                'correo' => $request->email,
                'observaciones' => $request->observaciones,
            ]);

            // Actualizar historial clínico y relaciones
            $historial = \App\Models\HistorialClinico::where('id_paciente', $paciente->id_paciente)->first();

            if ($historial) {
                // Alergias
                \DB::table('historial_alergia')->where('id_historial', $historial->id_historial)->delete();
                if ($request->has('alergias')) {
                    foreach ($request->alergias as $id_alergia) {
                        \DB::table('historial_alergia')->insert([
                            'id_historial' => $historial->id_historial,
                            'id_alergia' => $id_alergia,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                // Enfermedades crónicas
                \DB::table('historial_enfermedad')->where('id_historial', $historial->id_historial)->delete();
                if ($request->has('cronicas')) {
                    foreach ($request->cronicas as $id_enfermedad) {
                        \DB::table('historial_enfermedad')->insert([
                            'id_historial' => $historial->id_historial,
                            'id_enfermedad' => $id_enfermedad,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                // Medicamentos
                \DB::table('medicacion_actual')->where('id_historial', $historial->id_historial)->delete();
                if ($request->has('medicamentos')) {
                    foreach ($request->medicamentos as $id_medicamento) {
                        \DB::table('medicacion_actual')->insert([
                            'id_historial' => $historial->id_historial,
                            'id_medicamento' => $id_medicamento,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            session()->flash('swal', [
                'title' => "Paciente Actualizado!",
                'text' => "El paciente ha sido actualizado correctamente",
                'icon' => "success"
            ]);

            return redirect()->route('recepcionista.pacientes.buscar');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar paciente: ' . $e->getMessage());
            
            session()->flash('swal', [
                'title' => "Error",
                'text' => "No se pudo actualizar el paciente. Inténtalo nuevamente.",
                'icon' => "error"
            ]);

            return back()->withInput();
        }
    }

    public function gestionarCitas(Request $request)
    {
        $estado = $request->input('estado', 'todas');
        $buscar = $request->input('buscar');
        
        // Obtener citas de la base de datos con relaciones
        $query = Cita::with([
            'historial.paciente',
            'medico.usuario', 
            'especialidad',
            'horaConsulta'
        ]);

        // Filtrar por estado
        if ($estado !== 'todas') {
            $query->where('estado', $estado);
        }

        // Filtrar por búsqueda de paciente
        if ($buscar) {
            $query->whereHas('historial.paciente', function($q) use ($buscar) {
                $q->where('nombres', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellidos', 'LIKE', "%{$buscar}%")
                  ->orWhere('dni', 'LIKE', "%{$buscar}%");
            });
        }

        $citas = $query->orderBy('fecha', 'desc')
                      ->orderBy('id_hora', 'asc')
                      ->get();

        // Transformar los datos para la vista
        $citasTransformadas = $citas->map(function ($cita) {
            return [
                'id' => $cita->id_cita,
                'paciente' => [
                    'nombre' => $cita->historial->paciente->nombres,
                    'apellidos' => $cita->historial->paciente->apellidos,
                    'dni' => $cita->historial->paciente->dni,
                ],
                'doctor' => [
                    'name' => $cita->medico->usuario->nombres . ' ' . $cita->medico->usuario->apellidos,
                    'especialidad' => $cita->especialidad->nombre ?? 'Medicina General',
                ],
                'fecha' => $cita->fecha,
                'hora' => $cita->horaConsulta->hora_inicio,
                'estado' => $cita->estado,
                'motivo' => $cita->motivo,
            ];
        });

        return view('recepcionista.citas.index', compact('citasTransformadas'));
    }

    public function agendarCita(Request $request)
    {
        $paciente = null;
        $pacientes = \App\Models\Paciente::orderBy('nombres')->get();

        if ($request->has('paciente_id')) {
            $paciente = \App\Models\Paciente::find($request->paciente_id);
        }

        // Obtener médicos con usuario y especialidad (solo activos)
        $doctores = \App\Models\Medico::with(['usuario', 'especialidadNombre'])
            ->whereHas('usuario', function($query) {
                $query->where('estado', 1); // Solo usuarios activos
            })
            ->get();

        // Obtener disponibilidades para pasar a la vista (solo de doctores activos)
        $disponibilidades = \DB::table('disponibilidad')
            ->join('turno', 'disponibilidad.id_turno', '=', 'turno.id_turno')
            ->join('medico', 'disponibilidad.id_usuario', '=', 'medico.id_usuario')
            ->join('usuario', 'medico.id_usuario', '=', 'usuario.id_usuario')
            ->where('usuario.estado', 1) // Solo usuarios activos
            ->select('disponibilidad.*', 'turno.descripcion', 'turno.hora_inicio', 'turno.hora_fin')
            ->get()
            ->groupBy('id_usuario');

        // Obtener citas ya agendadas
        $citasExistentes = \DB::table('cita')
            ->join('hora_consulta', 'cita.id_hora', '=', 'hora_consulta.id_hora')
            ->where('cita.estado', '!=', 'Ausente')
            ->select('cita.id_medico', 'cita.fecha', 'hora_consulta.hora_inicio')
            ->get()
            ->groupBy(['id_medico', 'fecha']);

        return view('recepcionista.citas.agendar', compact('pacientes', 'paciente', 'doctores', 'disponibilidades', 'citasExistentes'));
    }

    public function guardarCita(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:paciente,id_paciente',
            'doctor_id' => 'required|exists:medico,id_usuario',
            'motivo' => 'required|string',
            'fecha' => 'required|date',
            'hora' => 'required'
        ]);

        try {
            // Obtener el historial clínico del paciente
            $historial = HistorialClinico::where('id_paciente', $request->paciente_id)->first();
            
            if (!$historial) {
                return back()->withErrors(['error' => 'El paciente no tiene historial clínico']);
            }

            // Obtener el médico y su especialidad
            $medico = \App\Models\Medico::find($request->doctor_id);
            
            if (!$medico) {
                return back()->withErrors(['error' => 'Médico no encontrado']);
            }
            
            // Buscar o crear el horario de consulta
            $horaInicio = $request->hora;
            $horaFin = \Carbon\Carbon::parse($horaInicio)->addMinutes(30)->format('H:i');
            
            $horaConsulta = HoraConsulta::firstOrCreate([
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin
            ]);

            // Verificar que no haya conflicto de horarios
            $citaExistente = \App\Models\Cita::where('id_medico', $request->doctor_id)
                ->where('fecha', $request->fecha)
                ->where('id_hora', $horaConsulta->id_hora)
                ->where('estado', '!=', 'Ausente')
                ->exists();

            if ($citaExistente) {
                return back()->withErrors(['error' => 'Ya existe una cita para ese horario']);
            }

            // Crear la cita
            $cita = \App\Models\Cita::create([
                'id_historial' => $historial->id_historial,
                'id_medico' => $request->doctor_id,
                'id_especialidad' => $medico->especialidad,
                'motivo' => $request->motivo,
                'estado' => 'Agendada',
                'id_hora' => $horaConsulta->id_hora,
                'fecha' => $request->fecha,
            ]);

            // Flash message para cita creada exitosamente
            session()->flash('swal', [
                'title' => "¡Cita Agendada!",
                'text' => "La cita ha sido agendada correctamente",
                'icon' => "success"
            ]);

            return redirect()->route('recepcionista.citas.index');

        } catch (\Exception $e) {
            \Log::error('Error al guardar cita: ' . $e->getMessage());
            
            session()->flash('swal', [
                'title' => "Error",
                'text' => "No se pudo agendar la cita. Inténtalo nuevamente.",
                'icon' => "error"
            ]);

            return back()->withInput();
        }
    }

    public function editarCita($id)
    {
        // Obtener la cita desde la base de datos con todas las relaciones
        $citaDB = Cita::with([
            'historial.paciente',
            'medico.usuario',
            'especialidad',
            'horaConsulta'
        ])->findOrFail($id);

        // Transformar los datos para la vista
        $cita = [
            'id' => $citaDB->id_cita,
            'paciente' => [
                'nombre' => $citaDB->historial->paciente->nombres,
                'apellidos' => $citaDB->historial->paciente->apellidos,
                'dni' => $citaDB->historial->paciente->dni,
            ],
            'doctor_id' => $citaDB->id_medico,
            'fecha' => $citaDB->fecha->format('Y-m-d'),
            'hora' => $citaDB->horaConsulta->hora_inicio,
            'motivo' => $citaDB->motivo,
            'estado' => $citaDB->estado,
        ];

        // Obtener lista de doctores para el select (solo activos)
        $doctores = collect(\App\Models\Medico::with(['usuario', 'especialidadNombre'])
            ->whereHas('usuario', function($query) {
                $query->where('estado', 1); // Solo usuarios activos
            })
            ->get())->map(function($medico) {
            return [
                'id' => $medico->id_usuario,
                'name' => $medico->usuario->nombres . ' ' . $medico->usuario->apellidos,
                'especialidad' => $medico->especialidadNombre->nombre ?? 'Medicina General'
            ];
        });

        // Obtener disponibilidades para pasar a la vista (solo de doctores activos)
        $disponibilidades = \DB::table('disponibilidad')
            ->join('turno', 'disponibilidad.id_turno', '=', 'turno.id_turno')
            ->join('medico', 'disponibilidad.id_usuario', '=', 'medico.id_usuario')
            ->join('usuario', 'medico.id_usuario', '=', 'usuario.id_usuario')
            ->where('usuario.estado', 1) // Solo usuarios activos
            ->select('disponibilidad.*', 'turno.descripcion', 'turno.hora_inicio', 'turno.hora_fin')
            ->get()
            ->groupBy('id_usuario');

        // Obtener citas ya agendadas
        $citasExistentes = \DB::table('cita')
            ->join('hora_consulta', 'cita.id_hora', '=', 'hora_consulta.id_hora')
            ->where('cita.estado', '!=', 'Ausente')
            ->select('cita.id_cita', 'cita.id_medico', 'cita.fecha', 'hora_consulta.hora_inicio')
            ->get()
            ->groupBy(['id_medico', 'fecha']);

        return view('recepcionista.citas.editar', compact('cita', 'doctores', 'disponibilidades', 'citasExistentes'));
    }

        public function actualizarCita(Request $request, $id)
    {
            $request->validate([
                'doctor_id' => 'required|exists:medico,id_usuario',
                'fecha' => 'required|date',
                'hora' => 'required',
                'motivo' => 'required|string',
                'estado' => 'required|in:Agendada,Atendida,Ausente',
            ]);

            try {
                // Encontrar la cita
                $cita = Cita::findOrFail($id);

                // Buscar o crear el horario de consulta para la nueva hora
                $horaInicio = $request->hora;
                $horaFin = \Carbon\Carbon::parse($horaInicio)->addMinutes(30)->format('H:i');
                
                $horaConsulta = HoraConsulta::firstOrCreate([
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin
                ]);

                // Verificar que no haya conflicto de horarios (solo si cambió doctor, fecha u hora)
                if ($cita->id_medico != $request->doctor_id || 
                    $cita->fecha != $request->fecha || 
                    $cita->id_hora != $horaConsulta->id_hora) {
                    
                    $citaExistente = Cita::where('id_medico', $request->doctor_id)
                        ->where('fecha', $request->fecha)
                        ->where('id_hora', $horaConsulta->id_hora)
                        ->where('estado', '!=', 'Ausente')
                        ->where('id_cita', '!=', $id) // Excluir la cita actual
                        ->exists();

                    if ($citaExistente) {
                        return back()->withErrors(['error' => 'Ya existe una cita para ese horario']);
                    }
                }

                // Obtener el médico y su especialidad
                $medico = \App\Models\Medico::find($request->doctor_id);

                // Actualizar la cita
                $cita->update([
                    'id_medico' => $request->doctor_id,
                    'id_especialidad' => $medico->especialidad,
                    'motivo' => $request->motivo,
                    'estado' => $request->estado,
                    'id_hora' => $horaConsulta->id_hora,
                    'fecha' => $request->fecha,
                ]);

                // Flash message para cita actualizada exitosamente
                session()->flash('swal', [
                    'title' => "¡Cita Actualizada!",
                    'text' => "La cita ha sido actualizada correctamente",
                    'icon' => "success"
                ]);

                return redirect()->route('recepcionista.citas.index');

            } catch (\Exception $e) {
                \Log::error('Error al actualizar cita: ' . $e->getMessage());
                
                session()->flash('swal', [
                    'title' => "Error",
                    'text' => "No se pudo actualizar la cita. Inténtalo nuevamente.",
                    'icon' => "error"
                ]);

                return back()->withInput();
            }
    }

    public function cancelarCita($id)
    {
        $cita = DataService::findCita($id);
        
        if (!$cita) {
            abort(404, 'Cita no encontrada');
        }

        // En un sistema real, aquí actualizaríamos el estado en la base de datos
        
        return back()->with('success', 'Cita marcada como ausente exitosamente.');
    }

    public function historialCitasPaciente($id)
    {
        $paciente = DataService::findPaciente($id);
        
        if (!$paciente) {
            abort(404, 'Paciente no encontrado');
        }
        
        $citas = DataService::getCitasByPaciente($id)->sortByDesc('fecha');
        
        // Enriquecer con datos de doctores
        $citas = $citas->map(function ($cita) {
            $doctor = DataService::findUser($cita['doctor_id']);
            $cita['doctor'] = $doctor;
            return $cita;
        });
        
        $perPage = 10;
        $page = request('page', 1);
        $items = collect($citas); // tu colección de citas
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();

        $citas = new LengthAwarePaginator(
            $currentPageItems,
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('recepcionista.citas.paciente', compact('paciente', 'citas'));
    }
}
