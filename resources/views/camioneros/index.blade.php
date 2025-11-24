@extends('layouts.app')

@section('title', 'Camioneros')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">
            <i class="fas fa-truck"></i>
            Camioneros
        </h1>
        <p style="color: #666; margin: 0;">Listado de camioneros desde la API (con token).</p>
    </div>

    <div class="search-box">
        <input id="searchInput" type="text" class="form-control search-input" placeholder="Buscar por nombre, documento, licencia...">
        <button id="searchBtn" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
        <button id="reloadBtn" class="btn btn-secondary"><i class="fas fa-refresh"></i> Recargar</button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Fecha Nacimiento</th>
                    <th>Licencia</th>
                    <th>Teléfono</th>
                    <th>Camiones</th>
                </tr>
            </thead>
            <tbody id="camionerosTableBody">
                <tr>
                    <td colspan="7">
                        <div style="text-align: center; color: #666;">
                            <span class="spinner"></span>
                            Cargando camioneros...
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
        loadCamioneros();
        document.getElementById('reloadBtn').addEventListener('click', () => loadCamioneros());
        document.getElementById('searchBtn').addEventListener('click', () => loadCamioneros(1));
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) loadCamioneros(currentPage - 1); });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < lastPage) loadCamioneros(currentPage + 1); });
    });

    async function loadCamioneros(page = 1) {
        currentPage = page;
        const search = (document.getElementById('searchInput').value || '').trim();
        const params = new URLSearchParams();
        params.set('page', page);
        if (search) params.set('search', search);

        const tbody = document.getElementById('camionerosTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div style="text-align: center; color: #666;">
                        <span class="spinner"></span>
                        Cargando camioneros...
                    </div>
                </td>
            </tr>
        `;

        try {
            const response = await fetch(`/api/camioneros?${params.toString()}`);
            const data = await response.json();

            if (!response.ok) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="color: #dc3545; text-align: center;">
                            <i class="fas fa-exclamation-triangle"></i> ${data.message || 'Error al obtener camioneros'}
                        </td>
                    </tr>
                `;
                updatePagination(1, 1);
                return;
            }

            renderTable(data.data);
            updatePagination(data.meta.current_page, data.meta.last_page || 1);
        } catch (error) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="color: #dc3545; text-align: center;">
                        <i class="fas fa-exclamation-circle"></i> ${error.message}
                    </td>
                </tr>
            `;
            updatePagination(1, 1);
        }
    }

    function renderTable(items) {
        const tbody = document.getElementById('camionerosTableBody');
        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; color: #666;">
                        No hay camioneros disponibles.
                    </td>
                </tr>
            `;
            return;
        }

        const rows = items.map(c => {
            const fechaNac = c.fecha_nacimiento ? new Date(c.fecha_nacimiento).toLocaleDateString('es-ES') : '-';
            const camiones = (c.camiones || []).length;
            return `
                <tr>
                    <td>${c.documento || '-'}</td>
                    <td>${c.nombre || '-'}</td>
                    <td>${c.apellido || '-'}</td>
                    <td>${fechaNac}</td>
                    <td>${c.licencia || '-'}</td>
                    <td>${c.telefono || '-'}</td>
                    <td><span class="badge badge-info">${camiones}</span></td>
                </tr>
            `;
        }).join('');
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

