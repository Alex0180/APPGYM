<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        // Validación de campos
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Buscar usuario por username
        $user = User::where('username', $request->username)->first();

        // Verificar credenciales
        if ($user && Hash::check($request->password, $user->password)) {
            // Guardar datos en sesión
            session([
                'user_id' => $user->id,
                'username' => $user->username
            ]);

            return redirect()->route('index');
        }

        // Si falla, regresar con error
        return back()
            ->withErrors(['credentials' => 'Usuario o contraseña inválidos'])
            ->withInput();
    }

    /**
     * Página principal después de login
     */
    public function index()
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        return view('index');
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
