@extends('layouts.app')

@section('title', 'Paquetes')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="card-title">
                <i class="fas fa-box"></i>
                Paquetes
            </h1>
            <p style="color: #B0D4F0; margin: 0;">Gestión completa de paquetes desde la API.</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <button id="btnNuevoPaquete" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Paquete
        </button>
        @endif
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
                    <th>ID</th>
                    <th>Dirección</th>
                    <th>Camionero</th>
                    <th>Estado</th>
                    <th>Detalles</th>
                    @if(auth()->user()->role === 'admin')
                    <th style="text-align: center;">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody id="paquetesTableBody">
                <tr>
                    <td colspan="6">
                        <div style="text-align: center; color: #B0D4F0;">
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
        <div id="pageInfo" style="color: #B0D4F0;">Página - de -</div>
        <button id="nextPage" class="btn btn-secondary" disabled>Siguiente <i class="fas fa-chevron-right"></i></button>
    </div>
</div>
<!-- Modal para Crear/Editar Paquete -->
<div id="modalPaquete" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="modal-content" style="background: white; max-width: 600px; margin: 2rem auto; border-radius: 8px; padding: 2rem; max-height: 90vh; overflow-y: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Nuevo Paquete</h2>


        <form id="formPaquete">
            <input type="hidden" id="paqueteId">
            
            <div class="form-group">
                <label class="form-label">Camionero *</label>
                <select id="camioneroId" class="form-control" required>
                    <option value="">Selecciona un camionero...</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Estado *</label>
                <select id="estadoId" class="form-control" required>
                    <option value="">Selecciona un estado...</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Dirección * (máx. 25 caracteres)</label>
                <input type="text" id="direccion" class="form-control" maxlength="25" required>
                <div class="invalid-feedback"></div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <button type="button" onclick="cerrarModal()" class="btn btn-secondary" style="flex: 1;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let lastPage = 1;
    let camioneros = [];
    let estados = [];
    const isAdmin = {{ auth()->user()->role === 'admin' ? 'true' : 'false' }};

    document.addEventListener('DOMContentLoaded', function() {
        loadPaquetes();
        loadCamioneros();
        loadEstados();
        
        document.getElementById('reloadBtn').addEventListener('click', () => loadPaquetes());
        document.getElementById('searchBtn').addEventListener('click', () => loadPaquetes(1));
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) loadPaquetes(currentPage - 1); });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < lastPage) loadPaquetes(currentPage + 1); });
        
        if (isAdmin) {
            document.getElementById('btnNuevoPaquete').addEventListener('click', abrirModalNuevo);
            document.getElementById('formPaquete').addEventListener('submit', guardarPaquete);
        }
    });

    async function loadCamioneros() {
        try {
            const response = await fetch('/internal/camioneros', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            camioneros = data.data || [];
            
            const select = document.getElementById('camioneroId');
            camioneros.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = `${c.nombre} ${c.apellido}`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error cargando camioneros:', error);
        }
    }

    async function loadEstados() {
        try {
            const response = await fetch('/internal/estados-paquetes', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            estados = data.data || data || [];
            
            const select = document.getElementById('estadoId');
            estados.forEach(e => {
                const option = document.createElement('option');
                option.value = e.id;
                option.textContent = e.estado;
                select.appendChild(option);
            });
        } catch (error) {
            // Si el endpoint no existe, crear estados por defecto
            estados = [{id: 1, estado: 'Pendiente'}, {id: 2, estado: 'En camino'}, {id: 3, estado: 'Entregado'}];
            const select = document.getElementById('estadoId');
            estados.forEach(e => {
                const option = document.createElement('option');
                option.value = e.id;
                option.textContent = e.estado;
                select.appendChild(option);
            });
        }
    }

    async function loadPaquetes(page = 1) {
        currentPage = page;
        const search = (document.getElementById('searchInput').value || '').trim();
        const params = new URLSearchParams();
        params.set('page', page);
        if (search) params.set('search', search);

        const tbody = document.getElementById('paquetesTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="${isAdmin ? '6' : '5'}">
                    <div style="text-align: center; color: #B0D4F0;">
                        <span class="spinner"></span>
                        Cargando paquetes...
                    </div>
                </td>
            </tr>
        `;

        try {
            const response = await fetch(`/internal/paquetes?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await response.json();

            if (!response.ok) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${isAdmin ? '6' : '5'}" style="color: #dc3545; text-align: center;">
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
                    <td colspan="${isAdmin ? '6' : '5'}" style="color: #dc3545; text-align: center;">
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
                    <td colspan="${isAdmin ? '6' : '5'}" style="text-align: center; color: #B0D4F0;">
                        No hay paquetes disponibles.
                    </td>
                </tr>
            `;
            return;
        }

        const rows = items.map(p => {
            let acciones = '';
            if (isAdmin) {
                acciones = `
                    <td style="text-align: center;">
                        <button onclick="editarPaquete(${p.id})" class="btn btn-warning" style="padding: 0.5rem 1rem; margin-right: 0.5rem;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="eliminarPaquete(${p.id})" class="btn btn-danger" style="padding: 0.5rem 1rem;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
            }
            
            return `
                <tr>
                    <td>${p.id}</td>
                    <td>${p.direccion}</td>
                    <td>${p.camionero?.nombre || '-'} ${p.camionero?.apellido || ''}</td>
                    <td><span class="badge badge-info">${p.estado?.estado || '-'}</span></td>
                    <td><span class="badge badge-success">${(p.detalles || []).length}</span></td>
                    ${acciones}
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

    function abrirModalNuevo() {
        document.getElementById('modalTitle').textContent = 'Nuevo Paquete';
        document.getElementById('formPaquete').reset();
        document.getElementById('paqueteId').value = '';
        document.getElementById('modalPaquete').style.display = 'block';
        limpiarErrores();
    }

    async function editarPaquete(id) {
        try {
            const response = await fetch(`/internal/paquetes/${id}`);
            const paquete = await response.json();
            
            document.getElementById('modalTitle').textContent = 'Editar Paquete';
            document.getElementById('paqueteId').value = paquete.id;
            document.getElementById('camioneroId').value = paquete.camionero.id;
            document.getElementById('estadoId').value = paquete.estado.id;
            document.getElementById('direccion').value = paquete.direccion;
            document.getElementById('modalPaquete').style.display = 'block';
            limpiarErrores();
        } catch (error) {
            alert('Error al cargar el paquete: ' + error.message);
        }
    }

    async function guardarPaquete(e) {
        e.preventDefault();
        limpiarErrores();

        const id = document.getElementById('paqueteId').value;
        const data = {
            camioneros_id: parseInt(document.getElementById('camioneroId').value),
            estados_paquetes_id: parseInt(document.getElementById('estadoId').value),
            direccion: document.getElementById('direccion').value
        };

        try {
            const url = id ? `/internal/paquetes/${id}` : '/internal/paquetes';
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.errors) {
                    mostrarErrores(result.errors);
                } else {
                    alert(result.message || 'Error al guardar el paquete');
                }
                return;
            }

            cerrarModal();
            loadPaquetes(currentPage);
            alert(id ? 'Paquete actualizado exitosamente' : 'Paquete creado exitosamente');
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    async function eliminarPaquete(id) {
        if (!confirm('¿Estás seguro de que quieres eliminar este paquete?')) {
            return;
        }

        try {
            const response = await fetch(`/internal/paquetes/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                const data = await response.json();
                alert(data.message || 'Error al eliminar el paquete');
                return;
            }

            loadPaquetes(currentPage);
            alert('Paquete eliminado exitosamente');
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    function cerrarModal() {
        document.getElementById('modalPaquete').style.display = 'none';
        document.getElementById('formPaquete').reset();
        limpiarErrores();
    }

    function mostrarErrores(errors) {
        for (const field in errors) {
            const input = document.getElementById(field.replace('camioneros_id', 'camioneroId').replace('estados_paquetes_id', 'estadoId'));
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = errors[field][0];
                    feedback.style.display = 'block';
                }
            }
        }
    }

    function limpiarErrores() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalPaquete')?.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
</script>
@endpush