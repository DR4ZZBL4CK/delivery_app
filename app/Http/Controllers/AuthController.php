<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

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

        // Obtener token API al registrar y loguear
        try {
            $apiLogin = Http::post(config('app.url') . '/api/auth/login', [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);
            if ($apiLogin->successful() && isset($apiLogin->json()['token'])) {
                $request->session()->put('api_token', $apiLogin->json()['token']);
            }
        } catch (\Throwable $e) {
            // Podemos registrar en log si es necesario
        }

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

            // Obtener token API tras login web
            try {
                $apiLogin = Http::post(config('app.url') . '/api/auth/login', [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ]);
                if ($apiLogin->successful() && isset($apiLogin->json()['token'])) {
                    $request->session()->put('api_token', $apiLogin->json()['token']);
                }
            } catch (\Throwable $e) {
                // Podemos registrar en log si es necesario
            }

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
