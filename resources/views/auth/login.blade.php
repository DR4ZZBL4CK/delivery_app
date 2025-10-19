@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card" style="max-width: 400px; width: 100%;">
        <div class="card-header text-center">
            <h2 class="card-title">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </h2>
            <p style="color: #666; margin: 0;">Accede a tu cuenta para gestionar tus contactos</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       placeholder="tu@email.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required
                       placeholder="Tu contraseña">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Recordarme
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </div>
        </form>

        <div style="text-align: center; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
            <p style="color: #666;">
                ¿No tienes una cuenta? 
                <a href="{{ route('register') }}" style="color: #667eea; text-decoration: none; font-weight: 500;">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-center {
        text-align: center;
    }
</style>
@endpush
