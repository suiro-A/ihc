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

class AdminController extends Controller
{
  private function getCurrentUser()
  {
    return Session::get('user_data');
  }

  public function dashboard()
  {
    $stats = [
      'total_usuarios' => count(DataService::getUsers()),
      'doctores' => DataService::getUsersByRole('doctor')->count(),
      'roles' => count(DataService::getRoles()),
      'horarios_configurados' => collect(DataService::getDisponibilidad())
        ->where('fecha', '>=', Carbon::today()->format('Y-m-d'))
        ->count(),
    ];

    $usuariosRecientes = collect(DataService::getUsers())
      ->sortByDesc('created_at')
      ->take(5);

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
    
    return redirect()->route('admin.usuarios.index')
      ->with('success', 'Usuario creado exitosamente.');
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

    return redirect()->route('admin.usuarios.index')
      ->with('success', 'Usuario actualizado exitosamente.');
  }

  public function toggleUsuario($id)
  {
    $usuario = Usuario::findOrFail($id);

    if (!$usuario) {
      abort(404, 'Usuario no encontrado');
    }

    $usuario->estado = !$usuario->estado;

    $usuario->save();

    $estado = !$usuario->estado ? 'desactivado' : 'activado';

    return back()->with('success', "Usuario {$estado} exitosamente.");
  }

  public function disponibilidad()
  {
    $doctores = Usuario::with(['medico.especialidadNombre'])
      ->where('rol', Usuario::ROL_DOCTOR)
      ->where('estado', true)
      ->orderBy('nombres')
      ->get();

    return view('admin.disponibilidad.index', compact('doctores'));
  }

  public function guardarDisponibilidad(Request $request)
  {
    $request->validate([
      'doctor_id' => 'required',
      'horarios' => 'required|array',
      'horarios.*.fecha' => 'required|date',
      'horarios.*.hora_inicio' => 'required',
      'horarios.*.hora_fin' => 'required',
    ]);

    // En un sistema real, aquí guardaríamos en la base de datos

    return back()->with('success', 'Disponibilidad guardada exitosamente.');
  }
}
