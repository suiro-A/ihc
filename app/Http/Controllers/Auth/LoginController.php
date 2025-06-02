<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

        // Buscar usuario en datos simulados
        $user = DataService::findUserByEmail($credentials['email']);
        
        if ($user && $credentials['password'] === 'password') {
            // Simular autenticación exitosa
            Session::put('user_id', $user['id']);
            Session::put('user_data', $user);
            
            // Redireccionar según el rol
            if ($user['role'] === 'doctor') {
                return redirect()->route('doctor.dashboard');
            } elseif ($user['role'] === 'recepcionista') {
                return redirect()->route('recepcionista.dashboard');
            } elseif ($user['role'] === 'administrativo') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect('/');
    }
}
