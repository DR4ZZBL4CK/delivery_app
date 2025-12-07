@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">
            <i class="fas fa-user-shield"></i>
            Panel de Administración
        </h1>
        <p style="color: #B0D4F0; margin: 0;">Bienvenido/a, {{ auth()->user()->full_name }} ({{ auth()->user()->role }})</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-number" id="paquetes-count">-</div>
            <div class="stat-label">Paquetes Total</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stat-number" id="camioneros-count">-</div>
            <div class="stat-label">Camioneros</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <div class="stat-number" id="camiones-count">-</div>
            <div class="stat-label">Camiones</div>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <h3 style="margin-bottom: 1rem; color: #12C6EB;">
            <i class="fas fa-cog"></i>
            Acciones de Administración
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <a href="{{ route('paquetes.index') }}" class="btn btn-primary" style="text-decoration: none;">
                <i class="fas fa-box"></i>
                Gestionar Paquetes
            </a>
            
            <a href="{{ route('camioneros.index') }}" class="btn btn-success" style="text-decoration: none;">
                <i class="fas fa-truck"></i>
                Gestionar Camioneros
            </a>
            
            <a href="{{ route('camiones.index') }}" class="btn btn-warning" style="text-decoration: none;">
                <i class="fas fa-shipping-fast"></i>
                Gestionar Camiones
            </a>
            
            <a href="{{ route('api.documentation') }}" class="btn btn-info" style="text-decoration: none;">
                <i class="fas fa-book"></i>
                Ver Documentación API
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadStats();
    });

    async function loadStats() {
        try {
            // Cargar estadísticas de paquetes
            const paquetesResponse = await fetch('/api/paquetes', { headers: { 'Accept': 'application/json' } });
            if (paquetesResponse.ok) {
                const paquetesData = await paquetesResponse.json();
                document.getElementById('paquetes-count').textContent = paquetesData.meta?.total || 0;
            }

            // Cargar estadísticas de camioneros
            const camionerosResponse = await fetch('/api/camioneros', { headers: { 'Accept': 'application/json' } });
            if (camionerosResponse.ok) {
                const camionerosData = await camionerosResponse.json();
                document.getElementById('camioneros-count').textContent = camionerosData.meta?.total || 0;
            }

            // Cargar estadísticas de camiones
            const camionesResponse = await fetch('/api/camiones', { headers: { 'Accept': 'application/json' } });
            if (camionesResponse.ok) {
                const camionesData = await camionesResponse.json();
                document.getElementById('camiones-count').textContent = camionesData.meta?.total || 0;
            }
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
        }
    }
</script>
@endpush
