@extends('layouts.app')

@section('title', 'Paquetes')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">
            <i class="fas fa-box"></i>
            Paquetes
        </h1>
        <p style="color: #666; margin: 0;">Listado de paquetes desde la API (con token).</p>
    </div>

    <div class="search-box">
        <input id="searchInput" type="text" class="form-control search-input" placeholder="Buscar por dirección...">
        <button id="searchBtn" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
        <button id="reloadBtn" class="btn btn-secondary"><i class="fas fa-refresh"></i> Recargar</button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Dirección</th>
                    <th>Camionero</th>
                    <th>Estado</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody id="paquetesTableBody">
                <tr>
                    <td colspan="4">
                        <div style="text-align: center; color: #666;">
                            <span class="spinner"></span>
                            Cargando paquetes...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
        <button id="prevPage" class="btn btn-secondary" disabled><i class="fas fa-chevron-left"></i> Anterior</button>
        <div id="pageInfo" style="color: #666;">Página - de -</div>
        <button id="nextPage" class="btn btn-secondary" disabled>Siguiente <i class="fas fa-chevron-right"></i></button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let lastPage = 1;

    document.addEventListener('DOMContentLoaded', function() {
        loadPaquetes();
        document.getElementById('reloadBtn').addEventListener('click', () => loadPaquetes());
        document.getElementById('searchBtn').addEventListener('click', () => loadPaquetes(1));
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) loadPaquetes(currentPage - 1); });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < lastPage) loadPaquetes(currentPage + 1); });
    });

    async function loadPaquetes(page = 1) {
        currentPage = page;
        const search = (document.getElementById('searchInput').value || '').trim();
        const params = new URLSearchParams();
        params.set('page', page);
        if (search) params.set('search', search); // si el backend soporta filtro

        const tbody = document.getElementById('paquetesTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="4">
                    <div style="text-align: center; color: #666;">
                        <span class="spinner"></span>
                        Cargando paquetes...
                    </div>
                </td>
            </tr>
        `;

        try {
            const response = await fetch(`/api/paquetes?${params.toString()}`);
            const data = await response.json();

            if (!response.ok) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="color: #dc3545; text-align: center;">
                            <i class="fas fa-exclamation-triangle"></i> ${data.message || 'Error al obtener paquetes'}
                        </td>
                    </tr>
                `;
                updatePagination(1, 1);
                return;
            }

            renderTable(data.data);
            updatePagination(data.meta.current_page, data.meta.last_page);
        } catch (error) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="color: #dc3545; text-align: center;">
                        <i class="fas fa-exclamation-circle"></i> ${error.message}
                    </td>
                </tr>
            `;
            updatePagination(1, 1);
        }
    }

    function renderTable(items) {
        const tbody = document.getElementById('paquetesTableBody');
        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">
                        No hay paquetes disponibles.
                    </td>
                </tr>
            `;
            return;
        }

        const rows = items.map(p => `
            <tr>
                <td>${p.direccion}</td>
                <td>${p.camionero?.nombre || '-'} ${p.camionero?.apellido || ''}</td>
                <td>${p.estado?.estado || '-'}</td>
                <td><span class="badge badge-info">${(p.detalles || []).length}</span></td>
            </tr>
        `).join('');
        tbody.innerHTML = rows;
    }

    function updatePagination(curr, last) {
        currentPage = curr;
        lastPage = last;
        document.getElementById('pageInfo').textContent = `Página ${curr} de ${last}`;
        document.getElementById('prevPage').disabled = curr <= 1;
        document.getElementById('nextPage').disabled = curr >= last;
    }
</script>
@endpush