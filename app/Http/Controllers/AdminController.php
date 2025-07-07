<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
  private function getCurrentUser()
  {
    return Session::get('user_data');
  }

  public function dashboard()
  {
    $stats = [
      'total_usuarios' => Usuario::count(),
      'doctores' => Usuario::where('rol', Usuario::ROL_DOCTOR)->where('estado', true)->count(),
      'roles' => Rol::count(),
      'horarios_configurados' => collect(DataService::getDisponibilidad())
        ->where('fecha', '>=', Carbon::today()->format('Y-m-d'))
        ->count(),
    ];

    $usuariosRecientes = Usuario::orderByDesc('created_at')
      ->take(5)
      ->get();

    return view('admin.dashboard', compact('stats', 'usuariosRecientes'));
  }

  public function gestionUsuarios(Request $request)
  {
    $query = Usuario::query();

    // Si hay búsqueda, filtramos por nombres o correo
    if ($request->filled('buscar')) {
      $buscar = $request->buscar;
      $query->where(function ($q) use ($buscar) {
        $q->where('nombres', 'like', "%$buscar%")
          ->orWhere('correo', 'like', "%$buscar%");
      });
    }

    $usuarios = $query->orderBy('nombres')->get();

    return view('admin.usuarios.index', compact('usuarios'));
  }

  public function crearUsuario()
  {
    $roles = Rol::all();
    $especialidades = Especialidad::all();
    return view('admin.usuarios.crear', compact('roles', 'especialidades'));
  }

  public function guardarUsuario(Request $request)
  {
    // Validación de campos
    $request->validate([
      'nombres' => 'required|string|max:255',
      'apellidos' => 'required|string|max:255',
      'telefono' => 'nullable|string|max:20',
      'email' => 'required|email|unique:usuario,correo',
      'role' => 'required',
      'password' => 'required|confirmed|min:6',
    ]);

    try {
        // Crear nuevo usuario
        $usuario = new Usuario();
        $usuario->nombres = $request->input('nombres');
        $usuario->apellidos = $request->input('apellidos');
        $usuario->telefono = $request->input('telefono');
        $usuario->correo = $request->input('email');
        $usuario->clave = Hash::make($request->input('password')); // Encriptar clave
        $usuario->rol = $request->input('role');
        $usuario->estado = true;
        
        $usuario->save();

        // Guardar información profesional si es doctor
        if ($usuario->isDoctor()) {
            $request->validate([
                'especialidad' => 'required',
                'colegiatura' => 'required|numeric|max:9999999999',
            ]);

            $medico = new Medico();
            $medico->id_usuario = $usuario->id_usuario;
            $medico->especialidad = $request->input('especialidad');
            $medico->num_colegiatura = $request->input('colegiatura');
            $medico->save();
        }
        
        // Mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'title' => "¡Usuario Creado!",
            'text' => "El usuario ha sido creado exitosamente",
            'icon' => "success"
        ]);

        return redirect()->route('admin.usuarios.index');

    } catch (\Exception $e) {
        \Log::error('Error al crear usuario: ' . $e->getMessage());
        
        session()->flash('swal', [
            'title' => "Error",
            'text' => "No se pudo crear el usuario. Inténtalo nuevamente.",
            'icon' => "error"
        ]);

        return back()->withInput();
    }
  }

  public function editarUsuario($id)
  {
    $usuario = Usuario::with('medico')->findOrFail($id);
    $especialidades = Especialidad::all();

    if (!$usuario) {
      abort(404, 'Usuario no encontrado');
    }

    $roles = Rol::all();
    return view('admin.usuarios.editar', compact('usuario', 'roles', 'especialidades'));
  }

  public function actualizarUsuario(Request $request, $id)
  {
    $usuario = Usuario::findOrFail($id);

    if (!$usuario) {
      abort(404, 'Usuario no encontrado');
    }

    $request->validate([
      'nombres' => 'required|string|max:255',
      'apellidos' => 'required|string|max:255',
      'telefono' => 'nullable|string|max:20',
      'email' => 'required|email|unique:usuario,correo,' . $usuario->id_usuario . ',id_usuario',
      'role' => 'required',
      'especialidad' => 'nullable|string',
      'colegiatura' => 'nullable|string|max:10',
    ]);

    try {
        $usuario->nombres = $request->input('nombres');
        $usuario->apellidos = $request->input('apellidos');
        $usuario->telefono = $request->input('telefono');
        $usuario->correo = $request->input('email');
        $usuario->rol = $request->input('role');

        $usuario->save();

        // Si el usuario es doctor, guardar o actualizar su info profesional
        if ($usuario->isDoctor()) {
            // Crear o actualizar registro de médico
            $medico = Medico::updateOrCreate(
                ['id_usuario' => $usuario->id_usuario],
                [
                    'especialidad' => $request->input('especialidad'),
                    'num_colegiatura' => $request->input('colegiatura'),
                ]
            );
        } else {
            // Eliminar si ya no es doctor
            if ($usuario->medico) {
                $usuario->medico->delete();
            }
        }

        // Mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'title' => "¡Usuario Actualizado!",
            'text' => "El usuario ha sido actualizado exitosamente",
            'icon' => "success"
        ]);

        return redirect()->route('admin.usuarios.index');

    } catch (\Exception $e) {
        \Log::error('Error al actualizar usuario: ' . $e->getMessage());
        
        session()->flash('swal', [
            'title' => "Error",
            'text' => "No se pudo actualizar el usuario. Inténtalo nuevamente.",
            'icon' => "error"
        ]);

        return back()->withInput();
    }
  }

  public function toggleUsuario($id)
  {
    $usuario = Usuario::findOrFail($id);

    if (!$usuario) {
        abort(404, 'Usuario no encontrado');
    }

    try {
        $estadoAnterior = $usuario->estado;
        $usuario->estado = !$usuario->estado;
        $usuario->save();

        $accion = $usuario->estado ? 'activado' : 'desactivado';
        $nombreCompleto = $usuario->nombres . ' ' . $usuario->apellidos;

        // Mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'title' => "¡Usuario " . ucfirst($accion) . "!",
            'text' => "El usuario \"$nombreCompleto\" ha sido $accion exitosamente",
            'icon' => "success",
            'timer' => 3000,
            'showConfirmButton' => false
        ]);

        return back();

    } catch (\Exception $e) {
        \Log::error('Error al cambiar estado de usuario: ' . $e->getMessage());
        
        session()->flash('swal', [
            'title' => "Error",
            'text' => "No se pudo cambiar el estado del usuario. Inténtalo nuevamente.",
            'icon' => "error"
        ]);

        return back();
    }
  }

  public function disponibilidad()
  {
      $doctores = Usuario::whereHas('medico')
          ->where('estado', 1) 
          ->with(['medico.especialidadNombre'])
          ->orderBy('nombres')
          ->get();

      $disponibilidades = DB::table('disponibilidad')
          ->join('usuario', 'disponibilidad.id_usuario', '=', 'usuario.id_usuario')
          ->join('medico', 'usuario.id_usuario', '=', 'medico.id_usuario')
          ->join('especialidad', 'medico.especialidad', '=', 'especialidad.id_especialidad')
          ->join('turno', 'disponibilidad.id_turno', '=', 'turno.id_turno')
          ->select(
              'disponibilidad.*',
              'usuario.nombres',
              'usuario.apellidos',
              'especialidad.nombre as especialidad',
              'turno.descripcion as turno'
          )
          ->orderBy('disponibilidad.anio', 'desc')
          ->orderBy('disponibilidad.mes', 'desc')
          ->get();

      return view('admin.disponibilidad.index', compact('doctores', 'disponibilidades'));
  }

  public function guardarDisponibilidad(Request $request)
  {
      $request->validate([
          'doctor_id' => 'required|exists:medico,id_usuario',
          'mes_anio' => 'required|date_format:Y-m',
          'turnos' => 'required|array|min:1',
          'turnos.*' => 'in:manana,tarde',
      ]);

      try {
          // Separar año y mes
          [$anio, $mes] = explode('-', $request->mes_anio);

          // Obtener los IDs de turno según el nombre
          $turnos = \DB::table('turno')->whereIn('descripcion', $request->turnos)->get();
          
          $horariosProcesados = 0;

          foreach ($turnos as $turno) {
              // Verificar si ya existe para evitar duplicados
              $existe = \DB::table('disponibilidad')
                  ->where('id_usuario', $request->doctor_id)
                  ->where('anio', $anio)
                  ->where('mes', $mes)
                  ->where('id_turno', $turno->id_turno)
                  ->exists();

              if (!$existe) {
                  \DB::table('disponibilidad')->insert([
                      'id_usuario' => $request->doctor_id,
                      'anio' => $anio,
                      'mes' => $mes,
                      'id_turno' => $turno->id_turno,
                      'created_at' => now(),
                      'updated_at' => now(),
                  ]);
                  $horariosProcesados++;
              }
          }

          // Mensaje de éxito con SweetAlert
          if ($horariosProcesados > 0) {
              session()->flash('swal', [
                  'title' => "¡Horario Definido!",
                  'text' => "La disponibilidad ha sido configurada correctamente",
                  'icon' => "success"
              ]);
          } else {
              session()->flash('swal', [
                  'title' => "¡Información!",
                  'text' => "Los horarios seleccionados ya estaban configurados",
                  'icon' => "info"
              ]);
          }

          return back();

      } catch (\Exception $e) {
          \Log::error('Error al guardar disponibilidad: ' . $e->getMessage());
          
          session()->flash('swal', [
              'title' => "Error",
              'text' => "No se pudo guardar la disponibilidad. Inténtalo nuevamente.",
              'icon' => "error"
          ]);

          return back();
      }
  }

  public function verificarEmail(Request $request)
  {
      $email = $request->input('email');
      
      // Verificar si el email ya existe en la base de datos
      $exists = Usuario::where('correo', $email)->exists();
      
      return response()->json(['exists' => $exists]);
  }
}
