<?php
// ==========================================
// views/pages/usuarios/index.php - Lista de Usuarios 
// ==========================================
?>

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Inicio</li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <h2>Gestión de Usuarios</h2>
        </div>
        <div class="col-auto">
            <!-- Botón nuevo usuario que abre modal AJAX -->
            <?php
            echo component('forms/button', [
                'text' => 'Nuevo Usuario',
                'icon' => 'plus-circle',
                'color' => 'primary',
                'onclick' => "abrirModalNuevoUsuario()"
            ]);

            echo component('forms/button', [
                'text' => 'Exportar Excel',
                'icon' => 'file-earmark-excel',
                'color' => 'success',
                'onclick' => "location.href='" . url('usuarios/exportar') . "'"
            ]);

            echo component('modal', [
                'id' => 'import-user',
                'icon' => 'file-arrow-up-fill',
                'color' => 'warning',
                'btntext' => 'Importar usuarios',
                'title' =>  'Importar usuarios',
                'content'  => 'Hola desde el modal',
                'size' =>  'modal-fullscreen'
            ]);
            ?>
        </div>
    </div>

    <!-- Búsqueda en tiempo real -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    id="buscar-usuario"
                    placeholder="Buscar usuarios por nombre, email o móvil..."
                    autocomplete="off">
            </div>
            <div id="resultados-busqueda" class="autocomplete-results" style="display: none; position: relative; z-index: 1000;"></div>
        </div>
    </div>

    <?php
    // Construir filas de la tabla
    $rows = [];
    foreach ($usuarios as $usuario) {
        $rows[] = [
            'ID' => $usuario->id,
            'Nombre' => $usuario->getNombreCompleto(),
            'Email' => $usuario->email,
            'Celular' => $usuario->movil,
            'Puesto' => $usuario->puesto ?? '-',
            'Área' => $usuario->area ?? '-',
            'Estatus' => $usuario->estatus
        ];
    }

    // Renderizar tabla usando componente
    echo component('table', [
        'id' => 'tablaUsuariosConAcciones',
        'headers' => ['ID', 'Nombre', 'Email', 'Celular', 'Puesto', 'Área', 'Estatus'],
        'rows' => $rows,
        'actions' => fn($row) =>
        component('forms/button', [
            'text' => '',
            'icon' => 'pencil',
            'color' => 'warning',
            'size' => 'sm',
            'title' => 'Editar',
            'onclick' => "editarUsuarioAjax({$row['ID']})"
        ]) . ' ' .
            component('forms/button', [
                'text' => '',
                'icon' => 'trash',
                'color' => 'danger',
                'size' => 'sm',
                'title' => 'Eliminar',
                'onclick' => "eliminarUsuarioAjax({$row['ID']})"
            ])
    ]);
    ?>

</div>

<!-- Modal para editar/crear usuario  -->
<div class="modal fade" id="modal-usuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUsuarioLabel">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-usuario-ajax" data-ajax-form action="<?= url('usuarios/guardar') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="usuario-id">

                    <!-- Información Personal -->
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="bi bi-person"></i> Información Personal
                    </h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="nombre" id="usuario-nombre" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" name="apellidoP" id="usuario-apellidoP" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" name="apellidoM" id="usuario-apellidoM">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="usuario-email" required>
                            <small id="email-validacion" class="form-text"></small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fecNac" id="usuario-fecNac">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Foto</label>
                            <p><em>aqui deberia estar en campo para subir la foto</em></p>
                            <!-- <small class="text-muted">Máximo 2MB. Formatos: JPG, PNG, GIF</small> -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Móvil *</label>
                            <input type="tel" class="form-control" name="movil" id="usuario-movil" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" id="usuario-telefono">
                        </div>
                    </div>

                    <!-- Información Laboral -->
                    <h5 class="border-bottom pb-2 mb-3 mt-2">
                        <i class="bi bi-briefcase"></i> Información Laboral
                    </h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. Empleado *</label>
                            <input type="text" class="form-control" name="noEmpleado" id="usuario-noEmpleado" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Área *</label>
                            <input type="text" class="form-control" name="area" id="usuario-area" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">CEDIS *</label>
                            <input type="text" class="form-control" name="cedis" id="usuario-cedis" required>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jefe Inmediato</label>
                            <input type="text" class="form-control" name="jefeInmediato" id="usuario-jefeInmediato">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Contratación *</label>
                            <input type="date" class="form-control" name="fecContratacion" id="usuario-fecContratacion" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Días de Vacaciones</label>
                            <input type="number" class="form-control" name="diasVacaciones" id="usuario-diasVacaciones" value="0">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Días Disfrutados</label>
                            <input type="number" class="form-control" name="diasVacDisfrutados" id="usuario-diasVacDisfrutados" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Puesto *</label>
                            <input type="text" class="form-control" name="puesto" id="usuario-puesto" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Área *</label>
                            <input type="text" class="form-control" name="area" id="usuario-area" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">CEDIS *</label>
                            <input type="text" class="form-control" name="cedis" id="usuario-cedis" required>
                        </div>
                    </div>

                    <!-- Seguridad -->
                    <h5 class="border-bottom pb-2 mb-3 mt-2">
                        <i class="bi bi-shield-lock"></i> Seguridad
                    </h5>

                    <div class="row">
                        <div class="col-md-4 mb-3" id="password-group">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" name="password" id="usuario-password" minlength="6">
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rol *</label>
                            <select class="form-select" name="idRol" id="usuario-idRol" required>
                                <option value="">Seleccione un rol</option>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3">Supervisor</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estatus *</label>
                            <select class="form-select" name="estatus" id="usuario-estatus" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="suspendido">Suspendido</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // Variable global para el modal de Bootstrap
    let modalUsuario;

    // Inicializar cuando cargue la página
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar modal de Bootstrap
        modalUsuario = new bootstrap.Modal(document.getElementById('modal-usuario'));
    });

    const tbody = document.querySelector('#tablaUsuariosConAcciones tbody');

    /**
     * Abrir modal para NUEVO usuario
     */
    function abrirModalNuevoUsuario() {
        // Limpiar formulario
        document.getElementById('form-usuario-ajax').reset();
        document.getElementById('usuario-id').value = '';
        document.getElementById('modalUsuarioLabel').textContent = 'Nuevo Usuario';

        // Mostrar campo de password (requerido para nuevo)
        document.getElementById('password-group').style.display = 'block';
        document.getElementById('usuario-password').required = true;

        // Limpiar validación de email
        document.getElementById('email-validacion').textContent = '';

        // Abrir modal
        modalUsuario.show();
    }

    /**
     * Editar usuario con AJAX
     */
    async function editarUsuarioAjax(id) {
        try {
            // Mostrar loading en el modal
            document.getElementById('modalUsuarioLabel').textContent = 'Cargando...';
            modalUsuario.show();

            // Obtener datos del usuario por AJAX
            const usuario = await Ajax.get(`/ajax/usuarios/${id}`);

            // Llenar formulario
            document.getElementById('modalUsuarioLabel').textContent = 'Editar Usuario';
            document.getElementById('usuario-id').value = usuario.id;
            document.getElementById('usuario-nombre').value = usuario.nombre;
            document.getElementById('usuario-apellidoP').value = usuario.apellidoP;
            document.getElementById('usuario-apellidoM').value = usuario.apellidoM || '';
            document.getElementById('usuario-email').value = usuario.email;
            document.getElementById('usuario-fecNac').value = usuario.fecNac || '';
            document.getElementById('usuario-movil').value = usuario.movil;
            document.getElementById('usuario-telefono').value = usuario.telefono || '';
            document.getElementById('usuario-noEmpleado').value = usuario.noEmpleado;
            document.getElementById('usuario-puesto').value = usuario.puesto;
            document.getElementById('usuario-area').value = usuario.area;
            document.getElementById('usuario-cedis').value = usuario.cedis;
            document.getElementById('usuario-jefeInmediato').value = usuario.jefeInmediato || '';
            document.getElementById('usuario-idRol').value = usuario.idRol;
            document.getElementById('usuario-fecContratacion').value = usuario.fecContratacion;
            document.getElementById('usuario-diasVacaciones').value = usuario.diasVacaciones;
            document.getElementById('usuario-diasVacDisfrutados').value = usuario.diasVacDisfrutados;
            document.getElementById('usuario-estatus').value = usuario.estatus;

            // Ocultar campo de password (no requerido para edición)
            document.getElementById('password-group').style.display = 'none';
            document.getElementById('usuario-password').required = false;

        } catch (error) {
            modalUsuario.hide();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el usuario: ' + error.message
            });
        }
    }

    /**
     * Eliminar usuario con AJAX
     */
    function eliminarUsuarioAjax(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Sí, eliminar',
            cancelButtonText: '<i class="bi bi-x-circle"></i> Cancelar',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await Ajax.delete(`/usuarios/eliminar/${id}`);

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Recargar página después de 1 segundo
                    setTimeout(() => {
                        location.reload();
                    }, 1000);

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });
                }
            }
        });
    }

    /**
     * Configurar formulario AJAX
     */
    const formUsuarioAjax = new AjaxForm(document.getElementById('form-usuario-ajax'));

    formUsuarioAjax
        .success((response) => {
            // Cerrar modal
            modalUsuario.hide();

            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.message,
                timer: 2000,
                showConfirmButton: false
            });

            // Recargar página después de 1 segundo
            setTimeout(() => {
                location.reload();
            }, 1000);
        })
        .error((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });
        });

    /**
     * Búsqueda en tiempo real
     */
    let timeoutBusqueda = null;

    document.getElementById('buscar-usuario').addEventListener('input', (e) => {
        const termino = e.target.value.trim();
        const tbody = document.querySelector('#tablaUsuariosConAcciones tbody');

        clearTimeout(timeoutBusqueda);

        // Si es muy corto, no buscamos
        if (termino.length < 3) {
            return;
        }

        timeoutBusqueda = setTimeout(async () => {
            try {
                const usuarios = await Ajax.get('/ajax/usuarios/buscar', {
                    q: termino
                });

                // Limpiar tabla
                tbody.innerHTML = '';

                if (usuarios.length === 0) {
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No se encontraron resultados
                        </td>
                    </tr>
                `;
                    return;
                }

                // Pintar filas
                usuarios.forEach(usuario => {
                    tbody.innerHTML += `
                    <tr>
                        <td>${usuario.id}</td>
                        <td>${usuario.nombreCompleto}</td>
                        <td>${usuario.email}</td>
                        <td>${usuario.movil ?? ''}</td>
                        <td>${usuario.puesto ?? 'Sin puesto'}</td>
                        <td>${usuario.area ?? ''}</td>
                        <td>${usuario.estatus}</td>
                        <td>
                            <button class="btn btn-warning btn-sm"
                                onclick="editarUsuarioAjax(${usuario.id})">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button class="btn btn-danger btn-sm"
                                onclick="eliminarUsuarioAjax(${usuario.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                });

            } catch (error) {
                console.error('Error en búsqueda:', error);
            }
        }, 300);
    });


    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#buscar-usuario') && !e.target.closest('#resultados-busqueda')) {
            document.getElementById('resultados-busqueda').style.display = 'none';
        }
    });

    /**
     * Validación de email en tiempo real
     */
    document.getElementById('usuario-email').addEventListener('blur', async (e) => {
        const email = e.target.value;
        const userId = document.getElementById('usuario-id').value;
        const validacionSpan = document.getElementById('email-validacion');

        if (!email) return;

        validacionSpan.innerHTML = '<span class="text-muted">Verificando...</span>';
        e.target.classList.add('validating');

        try {
            const response = await Ajax.get('/ajax/usuarios/validar-email', {
                email: email,
                id: userId
            });

            e.target.classList.remove('validating');

            if (response.disponible) {
                e.target.classList.add('is-valid');
                e.target.classList.remove('is-invalid');
                validacionSpan.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i>' + response.message + '</span>';
                e.target.setCustomValidity('');
            } else {
                e.target.classList.add('is-invalid');
                e.target.classList.remove('is-valid');
                validacionSpan.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i>' + response.message + '</span>';
                e.target.setCustomValidity('Email ya está en uso');
            }

        } catch (error) {
            e.target.classList.remove('validating');
            validacionSpan.textContent = '';
        }
    });

    // Limpiar validación cuando cambia el email
    document.getElementById('usuario-email').addEventListener('input', (e) => {
        e.target.classList.remove('is-valid', 'is-invalid');
        document.getElementById('email-validacion').textContent = '';
        e.target.setCustomValidity('');
    });
</script>