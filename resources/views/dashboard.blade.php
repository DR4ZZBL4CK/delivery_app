@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </h1>
        <p style="color: #666; margin: 0;">¡Bienvenido/a, {{ $user->full_name }}!</p>
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

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        <div>
            <h3 style="margin-bottom: 1rem; color: #333;">
                <i class="fas fa-box"></i>
                Paquetes Recientes
            </h3>
            
            <div id="paquetes-list" style="background: #f8f9fa; border-radius: 8px; padding: 1rem;">
                <div style="text-align: center; padding: 2rem; color: #666;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Cargando paquetes...</p>
                </div>
            </div>
        </div>

        <div>
            <h3 style="margin-bottom: 1rem; color: #333;">
                <i class="fas fa-rocket"></i>
                Acciones Rápidas
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <button onclick="loadPaquetes()" class="btn btn-primary">
                    <i class="fas fa-refresh"></i>
                    Actualizar Paquetes
                </button>
                
                <button onclick="loadCamioneros()" class="btn btn-info">
                    <i class="fas fa-truck"></i>
                    Ver Camioneros
                </button>
                
                <button onclick="loadCamiones()" class="btn btn-warning">
                    <i class="fas fa-shipping-fast"></i>
                    Ver Camiones
                </button>
            </div>

            <div style="margin-top: 2rem; padding: 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white;">
                <h4 style="margin-bottom: 0.5rem;">
                    <i class="fas fa-lightbulb"></i>
                    Sistema de Delivery
                </h4>
                <p style="margin: 0; font-size: 0.9rem;">
                    Gestiona paquetes, camioneros y camiones desde este panel de control integrado.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card" style="text-align: center;">
    <div style="padding: 3rem;">
        <i class="fas fa-truck" style="font-size: 4rem; color: #667eea; margin-bottom: 1rem;"></i>
        <h2 style="color: #333; margin-bottom: 1rem;">¡Sistema de Delivery Activo!</h2>
        <p style="color: #666; margin-bottom: 2rem; font-size: 1.1rem;">
            Usa los botones de arriba para gestionar paquetes, camioneros y camiones.
        </p>
        <button onclick="loadPaquetes()" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
            <i class="fas fa-box"></i>
            Ver Paquetes
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        .card div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Cargar datos al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        loadPaquetes();
        loadCamioneros();
        loadCamiones();
    });

    async function loadPaquetes() {
        try {
            const response = await fetch('/api/paquetes');
            const data = await response.json();
            
            if (response.ok) {
                document.getElementById('paquetes-count').textContent = data.meta.total;
                displayPaquetes(data.data);
            } else {
                showError('Error al cargar paquetes: ' + data.message);
            }
        } catch (error) {
            showError('Error de conexión: ' + error.message);
        }
    }

    async function loadCamioneros() {
        try {
            const response = await fetch('/api/camioneros');
            const data = await response.json();
            
            if (response.ok) {
                document.getElementById('camioneros-count').textContent = data.meta.total;
            } else {
                showError('Error al cargar camioneros: ' + data.message);
            }
        } catch (error) {
            showError('Error de conexión: ' + error.message);
        }
    }

    async function loadCamiones() {
        try {
            const response = await fetch('/api/camiones');
            const data = await response.json();
            
            if (response.ok) {
                document.getElementById('camiones-count').textContent = data.meta.total;
            } else {
                showError('Error al cargar camiones: ' + data.message);
            }
        } catch (error) {
            showError('Error de conexión: ' + error.message);
        }
    }

    function displayPaquetes(paquetes) {
        const container = document.getElementById('paquetes-list');
        
        if (paquetes.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #666;">
                    <i class="fas fa-box" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>No hay paquetes disponibles.</p>
                </div>
            `;
            return;
        }

        let html = '';
        paquetes.slice(0, 5).forEach(paquete => {
            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #e0e0e0;">
                    <div>
                        <strong>${paquete.direccion}</strong><br>
                        <small style="color: #666;">
                            Camionero: ${paquete.camionero.nombre} ${paquete.camionero.apellido} | 
                            Estado: ${paquete.estado.estado}
                        </small>
                    </div>
                    <div>
                        <span class="badge badge-info">${paquete.detalles.length} detalle(s)</span>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    function showError(message) {
        const container = document.getElementById('paquetes-list');
        container.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>${message}</p>
            </div>
        `;
    }
</script>
@endpush
