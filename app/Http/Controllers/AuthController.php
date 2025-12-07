<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        // Generar token API directamente sin llamada HTTP
        $token = $user->createToken('api-token')->plainTextToken;
        $request->session()->put('api_token', $token);

        return redirect()->route('dashboard')->with('success', '¡Registro exitoso! Bienvenido/a.');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            // Generar token API directamente sin llamada HTTP
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;
            $request->session()->put('api_token', $token);

            return redirect()->intended('dashboard')->with('success', '¡Inicio de sesión exitoso!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        // Cerrar sesión en API si existe token
        $token = $request->session()->get('api_token');
        if ($token) {
            try {
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])->post(config('app.url') . '/api/auth/logout');
            } catch (\Throwable $e) {
                // Ignorar errores de red al cerrar sesión API
            }
        }

        // Limpiar contexto web
        Auth::logout();
        $request->session()->forget('api_token');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', '¡Sesión cerrada exitosamente!');
    }
}
