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

    <div class="actions-bar" style="padding: 1rem; border-bottom: 1px solid #eee;">
        <button class="btn btn-success" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> Crear Camionero
        </button>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="camionerosTableBody">
                <tr>
                    <td colspan="8">
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

<!-- Create Modal -->
<div id="createModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; width: 500px; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Nuevo Camionero</h2>
            <button onclick="closeCreateModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form id="createForm">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="documento">Documento</label>
                <input type="text" id="documento" name="documento" class="form-control" required minlength="5" maxlength="10">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required minlength="2" maxlength="45">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" class="form-control" required minlength="2" maxlength="45">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="fecha_nacimiento">Fecha Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="licencia">Licencia</label>
                <input type="text" id="licencia" name="licencia" class="form-control" required minlength="5" maxlength="10">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control" required minlength="7" maxlength="15">
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
        loadCamioneros();
        document.getElementById('reloadBtn').addEventListener('click', () => loadCamioneros());
        document.getElementById('searchBtn').addEventListener('click', () => loadCamioneros(1));
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) loadCamioneros(currentPage - 1); });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < lastPage) loadCamioneros(currentPage + 1); });
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) loadCamioneros(currentPage - 1); });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < lastPage) loadCamioneros(currentPage + 1); });
        
        document.getElementById('createForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await createCamionero();
        });
    });

    function openCreateModal() {
        document.getElementById('createModal').style.display = 'block';
    }

    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
        document.getElementById('createForm').reset();
    }

    async function createCamionero() {
        const formData = new FormData(document.getElementById('createForm'));
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/api/camioneros', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                closeCreateModal();
                loadCamioneros();
                alert('Camionero creado exitosamente');
            } else {
                const error = await response.json();
                alert('Error al crear camionero: ' + (error.message || 'Error desconocido'));
            }
        } catch (error) {
            alert('Error de red: ' + error.message);
        }
    }

    async function deleteCamionero(id) {
        if (!confirm('¿Está seguro de eliminar este camionero?')) return;

        try {
            const response = await fetch(`/api/camioneros/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                loadCamioneros(currentPage);
                alert('Camionero eliminado exitosamente');
            } else {
                const error = await response.json();
                alert('Error al eliminar camionero: ' + (error.message || 'Error desconocido'));
            }
        } catch (error) {
            alert('Error de red: ' + error.message);
        }
    }

    async function loadCamioneros(page = 1) {
        currentPage = page;
        const search = (document.getElementById('searchInput').value || '').trim();
        const params = new URLSearchParams();
        params.set('page', page);
        if (search) params.set('search', search);

        const tbody = document.getElementById('camionerosTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8">
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
                        <td colspan="8" style="color: #dc3545; text-align: center;">
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
                    <td colspan="8" style="color: #dc3545; text-align: center;">
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
                    <td colspan="8" style="text-align: center; color: #666;">
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
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="deleteCamionero(${c.id})">
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

