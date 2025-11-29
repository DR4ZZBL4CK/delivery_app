@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 2rem 0;">
    <div class="card" style="max-width: 500px; width: 100%;">
        <div class="card-header text-center">
            <h2 class="card-title">
                <i class="fas fa-user-plus"></i>
                Crear Cuenta
            </h2>
            <p style="color: #B0D4F0; margin: 0;">Únete y comienza a gestionar tus contactos</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="nombre" class="form-label">
                        <i class="fas fa-user"></i> Nombre
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           class="form-control @error('nombre') is-invalid @enderror" 
                           value="{{ old('nombre') }}" 
                           required 
                           autofocus
                           placeholder="Tu nombre">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="apellido" class="form-label">
                        <i class="fas fa-user"></i> Apellido
                    </label>
                    <input type="text" 
                           id="apellido" 
                           name="apellido" 
                           class="form-control @error('apellido') is-invalid @enderror" 
                           value="{{ old('apellido') }}" 
                           required
                           placeholder="Tu apellido">
                    @error('apellido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

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
                       placeholder="Mínimo 8 caracteres">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-lock"></i> Confirmar Contraseña
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       class="form-control" 
                       required
                       placeholder="Repite tu contraseña">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-user-plus"></i>
                    Crear Cuenta
                </button>
            </div>
        </form>

        <div style="text-align: center; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
            <p style="color: #B0D4F0;">
                ¿Ya tienes una cuenta? 
                <a href="{{ route('login') }}" style="color: #12C6EB; text-decoration: none; font-weight: 500;">
                    Inicia sesión aquí
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
    
    @media (max-width: 768px) {
        .card div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
