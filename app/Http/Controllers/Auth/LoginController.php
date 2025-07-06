<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuario en la base de datos
        $user = Usuario::where('correo', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->clave)) {
            // Autenticación exitosa
            Session::put('user_id', $user->id_usuario);
            Session::put('user_data', [
                'id' => $user->id_usuario,
                'name' => $user->nombres . ' ' . $user->apellidos,
                'email' => $user->correo,
                'role_id' => $user->rol,
                'role' => $this->getRoleName($user->rol)
            ]);
            
            // Redireccionar según el rol
            if ($user->isDoctor()) {
                return redirect()->route('doctor.dashboard');
            } elseif ($user->isRecepcionista()) {
                return redirect()->route('recepcionista.dashboard');
            } elseif ($user->isAdministrativo()) {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    private function getRoleName($roleId)
    {
        switch ($roleId) {
            case Usuario::ROL_DOCTOR:
                return 'doctor';
            case Usuario::ROL_RECEPCIONISTA:
                return 'recepcionista';
            case Usuario::ROL_ADMIN:
                return 'administrativo'; // Cambiado para coincidir con las rutas
            default:
                return 'unknown';
        }
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect('/');
    }
}
