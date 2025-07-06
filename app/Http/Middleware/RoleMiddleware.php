<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener el usuario desde la base de datos
        $usuario = Usuario::find($userId);
        
        if (!$usuario) {
            abort(403, 'Usuario no encontrado.');
        }

        // Verificar el rol basado en el nombre del rol y las constantes
        $hasPermission = false;
        
        switch (strtolower($role)) {
            case 'doctor':
                $hasPermission = $usuario->isDoctor();
                break;
            case 'recepcionista':
                $hasPermission = $usuario->isRecepcionista();
                break;
            case 'administrador':
            case 'admin':
            case 'administrativo':
                $hasPermission = $usuario->isAdministrativo();
                break;
            default:
                // Si se pasa un número directamente, verificar por ID de rol
                if (is_numeric($role)) {
                    $hasPermission = $usuario->rol == (int)$role;
                }
                break;
        }
        
        if (!$hasPermission) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
