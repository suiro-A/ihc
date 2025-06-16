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

    $usuarios = $query->get();

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
    // dd($usuario->id_usuario);


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
    $usuario = DataService::findUser($id);

    if (!$usuario) {
      abort(404, 'Usuario no encontrado');
    }

    $roles = DataService::getRoles();
    return view('admin.usuarios.editar', compact('usuario', 'roles'));
  }

  public function actualizarUsuario(Request $request, $id)
  {
    $usuario = DataService::findUser($id);

    if (!$usuario) {
      abort(404, 'Usuario no encontrado');
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255',
      'telefono' => 'nullable|string',
      'especialidad' => 'nullable|string',
      'role' => 'required',
      'password' => 'nullable|string|min:8|confirmed',
    ]);

    // En un sistema real, aquí actualizaríamos en la base de datos

    return redirect()->route('admin.usuarios.index')
      ->with('success', 'Usuario actualizado exitosamente.');
  }

  public function toggleUsuario($id)
  {
    $usuario = DataService::findUser($id);

    if (!$usuario) {
      abort(404, 'Usuario no encontrado');
    }

    // En un sistema real, aquí cambiaríamos el estado en la base de datos
    $estado = !$usuario['is_active'] ? 'activado' : 'desactivado';

    return back()->with('success', "Usuario {$estado} exitosamente.");
  }

  public function disponibilidad()
  {
    $doctores = DataService::getUsersByRole('doctor')->where('is_active', true);
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
