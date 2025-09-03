<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // guardar en sesión
            session(['user_id' => $user->id, 'username' => $user->username]);
            return redirect()->route('index');
        }

        return back()
            ->withErrors(['credentials' => 'Usuario o contraseña inválidos'])
            ->withInput();
    }

    public function index()
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        return view('index');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
