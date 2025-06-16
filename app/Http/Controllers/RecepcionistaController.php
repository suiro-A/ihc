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

class RecepcionistaController extends Controller
{
    private function getCurrentUser()
    {
        return Session::get('user_data');
    }

    public function dashboard()
    {
        $citasHoy = collect(DataService::getCitasHoy());
        
        // Enriquecer citas con datos de pacientes y doctores
        $citasHoy = $citasHoy->map(function ($cita) {
            $paciente = DataService::findPaciente($cita['paciente_id']);
            $doctor = DataService::findUser($cita['doctor_id']);
            $cita['paciente'] = $paciente;
            $cita['doctor'] = $doctor;
            return $cita;
        });
        
        $stats = [
            'citas_hoy' => $citasHoy->count(),
            'proxima_cita' => $citasHoy->where('estado', 'agendada')->first(),
            'pacientes_registrados' => count(DataService::getPacientes()),
            'citas_confirmadas' => $citasHoy->where('estado', 'agendada')->count(),
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

    $paciente = Paciente::create([
        'nombres' => $request->nombres,
        'apellidos' => $request->apellidos,
        'dni' => $request->dni,
        'fecha_nac' => $request->fecha_nacimiento,
        'sexo' => $request->genero === 'masculino' ? 1 : 0,
        'telefono' => $request->telefono,
        'correo' => $request->email,
    ]);

    session()->flash('pacienteCreate', [
        'title' => "¡Bien hecho!",
        'text' => "Paciente creado correctamente",
        'icon' => "success"
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

    return redirect()->route('recepcionista.citas.agendar')
        ->with('success', 'Paciente registrado exitosamente.');
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
            'title' => "¡Bien hecho!",
            'text' => "Paciente actualizado correctamente",
            'icon' => "success"
        ]);
        return redirect()->route('recepcionista.pacientes.buscar')
                        ->with('success', 'Paciente actualizado exitosamente.');
    }

    public function gestionarCitas(Request $request)
    {
        $estado = $request->input('estado', 'todas');
        $buscar = $request->input('buscar');
        $citas = collect(\App\Services\DataService::getCitas());

        // Filtrar por estado
        if ($estado !== 'todas') {
            $citas = $citas->where('estado', $estado);
        }

        // Enriquecer con datos de pacientes y doctores
        $citas = $citas->map(function ($cita) {
            $cita['paciente'] = \App\Services\DataService::findPaciente($cita['paciente_id']);
            $cita['doctor'] = \App\Services\DataService::findUser($cita['doctor_id']);
            return $cita;
        });

        // Filtrar por búsqueda de paciente
        if ($buscar) {
            $citas = $citas->filter(function ($cita) use ($buscar) {
                $nombreCompleto = strtolower($cita['paciente']['nombre'] . ' ' . $cita['paciente']['apellidos']);
                return str_contains(strtolower($cita['paciente']['nombre']), strtolower($buscar))
                    || str_contains(strtolower($cita['paciente']['apellidos']), strtolower($buscar))
                    || str_contains($nombreCompleto, strtolower($buscar));
            });
        }

        
        return view('recepcionista.citas.index', compact('citas'));
    }

    public function agendarCita(Request $request)
    {
        $paciente = null;
        $pacientes = \App\Models\Paciente::orderBy('nombres')->get();

        if ($request->has('paciente_id')) {
            $paciente = \App\Models\Paciente::find($request->paciente_id);
        }

        // Obtener médicos con usuario y especialidad
        $doctores = \App\Models\Medico::with(['usuario', 'especialidadNombre'])->get();

        return view('recepcionista.citas.agendar', compact('pacientes', 'paciente', 'doctores'));
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

        // Aquí puedes crear la cita
        \App\Models\Cita::create([
            'id_paciente' => $request->paciente_id,
            'id_doctor' => $request->doctor_id,
            'motivo' => $request->motivo,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
        ]);

        return redirect()->route('recepcionista.citas.index')->with('success', 'Cita agendada correctamente');
    }

    public function editarCita($id)
    {
        $cita = DataService::findCita($id);
        
        if (!$cita) {
            abort(404, 'Cita no encontrada');
        }
        
        $paciente = DataService::findPaciente($cita['paciente_id']);
        $doctor = DataService::findUser($cita['doctor_id']);
        $cita['paciente'] = $paciente;
        $cita['doctor'] = $doctor;
        
        $doctores = DataService::getUsersByRole('doctor')->where('is_active', true);
        
        return view('recepcionista.citas.editar', compact('cita', 'doctores'));
    }

    public function actualizarCita(Request $request, $id)
    {
        $cita = DataService::findCita($id);
        
        if (!$cita) {
            abort(404, 'Cita no encontrada');
        }
        
        $request->validate([
            'doctor_id' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'motivo' => 'required|string',
            'estado' => 'required|in:agendada,completada,cancelada',
        ]);

        // En un sistema real, aquí actualizaríamos en la base de datos
        
        return redirect()->route('recepcionista.citas.index')
                        ->with('success', 'Cita actualizada exitosamente.');
    }

    public function cancelarCita($id)
    {
        $cita = DataService::findCita($id);
        
        if (!$cita) {
            abort(404, 'Cita no encontrada');
        }

        // En un sistema real, aquí actualizaríamos el estado en la base de datos
        
        return back()->with('success', 'Cita cancelada exitosamente.');
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
