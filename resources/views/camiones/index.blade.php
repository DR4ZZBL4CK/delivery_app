@extends('layouts.app')

@section('title', 'Camiones')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">
            <i class="fas fa-truck-loading"></i>
            Camiones
        </h1>
        <p style="color: #B0D4F0; margin: 0;">Listado de camiones desde la API (con token).</p>
    </div>

    <div class="actions-bar" style="padding: 1rem; border-bottom: 1px solid #eee;">
        <button class="btn btn-success" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> Crear Camión
        </button>
    </div>

    <div class="search-box">
        <input id="searchInput" type="text" class="form-control search-input" placeholder="Buscar por placa o modelo...">
        <button id="searchBtn" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
        <button id="reloadBtn" class="btn btn-secondary"><i class="fas fa-refresh"></i> Recargar</button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Modelo</th>
                    <th>Camioneros</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="camionesTableBody">
                <tr>
                    <td colspan="4">
                        <div style="text-align: center; color: #B0D4F0;">
                            <span class="spinner"></span>
                            Cargando camiones...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
        <button id="prevPage" class="btn btn-secondary" disabled><i class="fas fa-chevron-left"></i> Anterior</button>
        <div id="pageInfo" style="color: #B0D4F0;">Página - de -</div>
        <button id="nextPage" class="btn btn-secondary" disabled>Siguiente <i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; width: 500px; margin: 100px auto; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Nuevo Camión</h2>
            <button onclick="closeCreateModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form id="createForm">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="placa">Placa</label>
                <input type="text" id="placa" name="placa" class="form-control" required minlength="5" maxlength="10">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" class="form-control" required minlength="2" maxlength="10">
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="closeCreateModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let lastPage = 1;

    document.addEventListener('DOMContentLoaded', function() {
        loadCamiones();
        document.getElementById('reloadBtn').addEventListener('click', () => loadCamiones());
        document.getElementById('searchBtn').addEventListener('click', () => loadCamiones(1));
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) loadCamiones(currentPage - 1); });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < lastPage) loadCamiones(currentPage + 1); });
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) loadCamiones(currentPage - 1); });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < lastPage) loadCamiones(currentPage + 1); });
        
        document.getElementById('createForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await createCamion();
        });
    });

    function openCreateModal() {
        document.getElementById('createModal').style.display = 'block';
    }

    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
        document.getElementById('createForm').reset();
    }

    async function createCamion() {
        const formData = new FormData(document.getElementById('createForm'));
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/api/camiones', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                closeCreateModal();
                loadCamiones();
                alert('Camión creado exitosamente');
            } else {
                const error = await response.json();
                alert('Error al crear camión: ' + (error.message || 'Error desconocido'));
            }
        } catch (error) {
            alert('Error de red: ' + error.message);
        }
    }

    async function deleteCamion(id) {
        if (!confirm('¿Está seguro de eliminar este camión?')) return;

        try {
            const response = await fetch(`/api/camiones/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                loadCamiones(currentPage);
                alert('Camión eliminado exitosamente');
            } else {
                const error = await response.json();
                alert('Error al eliminar camión: ' + (error.message || 'Error desconocido'));
            }
        } catch (error) {
            alert('Error de red: ' + error.message);
        }
    }

    async function loadCamiones(page = 1) {
        currentPage = page;
        const search = (document.getElementById('searchInput').value || '').trim();
        const params = new URLSearchParams();
        params.set('page', page);
        if (search) params.set('search', search);

        const tbody = document.getElementById('camionesTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="4">
                    <div style="text-align: center; color: #B0D4F0;">
                        <span class="spinner"></span>
                        Cargando camiones...
                    </div>
                </td>
            </tr>
        `;

        try {
            const response = await fetch(`/api/camiones?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!response.ok) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="color: #dc3545; text-align: center;">
                            <i class="fas fa-exclamation-triangle"></i> ${data.message || 'Error al obtener camiones'}
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
                    <td colspan="4" style="color: #dc3545; text-align: center;">
                        <i class="fas fa-exclamation-circle"></i> ${error.message}
                    </td>
                </tr>
            `;
            updatePagination(1, 1);
        }
    }

    function renderTable(items) {
        const tbody = document.getElementById('camionesTableBody');
        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; color: #B0D4F0;">
                        No hay camiones disponibles.
                    </td>
                </tr>
            `;
            return;
        }

        const rows = items.map(c => {
            const camioneros = (c.camioneros || []).length;
            return `
                <tr>
                    <td>${c.placa || '-'}</td>
                    <td>${c.modelo || '-'}</td>
                    <td><span class="badge badge-info">${camioneros}</span></td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="deleteCamion(${c.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
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

