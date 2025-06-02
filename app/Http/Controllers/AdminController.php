<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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
        $usuarios = collect(DataService::getUsers())->map(function ($usuario) {
            $roles = DataService::getRoles();
            $rol = collect($roles)->firstWhere('name', $usuario['role']);
            $usuario['rol_display'] = $rol ? $rol['display_name'] : $usuario['role'];
            return $usuario;
        });
        
        // Filtro por nombre o email
        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);
            $usuarios = $usuarios->filter(function ($usuario) use ($buscar) {
                return str_contains(strtolower($usuario['name']), $buscar)
                    || str_contains(strtolower($usuario['email']), $buscar);
            });
        }

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function crearUsuario()
    {
        $roles = DataService::getRoles();
        return view('admin.usuarios.crear', compact('roles'));
    }

    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string',
            'especialidad' => 'nullable|string',
            'role' => 'required',
        ]);

        // Verificar email único
        $usuarios = collect(DataService::getUsers());
        if ($usuarios->where('email', $request->email)->isNotEmpty()) {
            return back()->withErrors(['email' => 'El email ya está registrado.']);
        }

        // En un sistema real, aquí guardaríamos en la base de datos
        
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
