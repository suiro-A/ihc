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
            'alergia' => 'required|exists:alergia,id_alergia',
            'cronica' => 'required|exists:enfermedad_cronica,id_enfermedad',
            'medicamento' => 'required|exists:medicamento,id_medicamento',
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

        // Crear historial clínico
        $historial = HistorialClinico::create([
            'id_paciente' => $paciente->id_paciente,
        ]);

        // Guardar alergia seleccionada
        \DB::table('historial_alergia')->insert([
            'id_historial' => $historial->id_historial,
            'id_alergia' => $request->alergia,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guardar enfermedad crónica seleccionada
        \DB::table('historial_enfermedad')->insert([
            'id_historial' => $historial->id_historial,
            'id_enfermedad' => $request->cronica,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guardar medicamento seleccionado
        \DB::table('medicacion_actual')->insert([
            'id_historial' => $historial->id_historial,
            'id_medicamento' => $request->medicamento,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('recepcionista.citas.agendar')
            ->with('success', 'Paciente registrado exitosamente.');
    }

    public function buscarPacientes(Request $request)
    {
        $query = $request->input('buscar');
        $pacientes = collect(DataService::getPacientes());

        if ($query) {
            $pacientes = $pacientes->filter(function ($paciente) use ($query) {
                $nombreCompleto = strtolower($paciente['nombres'] . ' ' . $paciente['apellidos']);
                return str_contains(strtolower($paciente['nombres']), strtolower($query))
                    || str_contains(strtolower($paciente['apellidos']), strtolower($query))
                    || str_contains(strtolower($nombreCompleto), strtolower($query))
                    || str_contains(strtolower($paciente['dni']), strtolower($query));
            });
        }

        // Enriquecer con última cita y edad
        $pacientes = $pacientes->map(function ($paciente) {
            $ultimaCita = DataService::getCitasByPaciente($paciente['id'])
                                    ->sortByDesc('fecha')
                                    ->first();
            $paciente['ultima_cita'] = $ultimaCita ? $ultimaCita['fecha'] : 'Sin citas';
            $paciente['edad'] = DataService::getEdadPaciente($paciente['fecha_nacimiento']);
            return $paciente;
        });
        
        return view('recepcionista.pacientes.buscar', compact('pacientes'));
    }

    public function editarPaciente($id)
    {
        $paciente = DataService::findPaciente($id);
        
        if (!$paciente) {
            abort(404, 'Paciente no encontrado');
        }
        
        return view('recepcionista.pacientes.editar', compact('paciente'));
    }

    public function actualizarPaciente(Request $request, $id)
    {
        $paciente = DataService::findPaciente($id);
        
        if (!$paciente) {
            abort(404, 'Paciente no encontrado');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dni' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:masculino,femenino,otro',
            'telefono' => 'required|string',
            'email' => 'nullable|email',
        ]);

        // En un sistema real, aquí actualizaríamos en la base de datos
        
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
        $pacienteId = $request->get('paciente');
        $paciente = $pacienteId ? DataService::findPaciente($pacienteId) : null;
        
        $pacientes = collect(DataService::getPacientes())->sortBy('nombre');
        $doctores = DataService::getUsersByRole('doctor')->where('is_active', true);
        
        return view('recepcionista.citas.agendar', compact('pacientes', 'doctores', 'paciente'));
    }

    public function guardarCita(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'doctor_id' => 'required',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
            'motivo' => 'required|string',
        ]);

        // Verificar disponibilidad
        $citaExistente = collect(DataService::getCitas())
                        ->where('doctor_id', $request->doctor_id)
                        ->where('fecha', $request->fecha)
                        ->where('hora', $request->hora)
                        ->where('estado', '!=', 'cancelada')
                        ->isNotEmpty();

        if ($citaExistente) {
            return back()->withErrors(['hora' => 'El horario seleccionado no está disponible.']);
        }

        // En un sistema real, aquí guardaríamos en la base de datos
        
        return redirect()->route('recepcionista.citas.index')
                        ->with('success', 'Cita agendada exitosamente.');
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

    public function buscarPacientesAjax(Request $request)
    {
        $q = $request->input('q');
        $pacientes = collect(\App\Services\DataService::getPacientes())
            ->filter(function ($paciente) use ($q) {
                return str_contains(strtolower($paciente['nombre']), strtolower($q))
                    || str_contains(strtolower($paciente['apellidos']), strtolower($q))
                    || str_contains(strtolower($paciente['dni']), strtolower($q));
            })
            ->values()
            ->all();

        return response()->json($pacientes);
    }
}
