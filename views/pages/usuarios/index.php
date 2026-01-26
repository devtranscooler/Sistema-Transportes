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
            <?php
            echo component('forms/button', [
                'text' => 'Nuevo Usuario',
                'icon' => 'plus-circle',
                'color' => 'primary',
                'onclick' => "location.href='" . url('usuarios/crear') . "'"
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

    <?php
    // Construir filas de la tabla
    $rows = [];
    foreach ($usuarios as $usuario) {
        $rows[] = [
            'ID' => $usuario->id,
            'Nombre' => $usuario->getNombreCompleto(),
            'Email' => $usuario->email,
            'Celular' => $usuario->movil,
            'Estatus' => $usuario->estatus
        ];
    }

    // Renderizar tabla usando componente
    echo component('table', [
        'id' => 'tablaUsuariosConAcciones',
        'headers' => ['ID', 'Nombre', 'Email', 'Celular', 'Estatus'],
        'rows' => $rows,
        'class' => 'table-striped table-hover',
        'useDataTable' => true,
        'dataTableOptions' => [
            'pageLength' => 10,
        ],
        'actions' => fn($row) =>
        component('forms/button', [
            'text' => '',
            'icon' => 'pencil',
            'color' => 'warning',
            'size' => 'sm',
            'onclick' => "editarUsuario({$row['ID']})"
        ]) . ' ' .
            component('forms/button', [
                'text' => '',
                'icon' => 'trash',
                'color' => 'danger',
                'size' => 'sm',
                'onclick' => "eliminarUsuario({$row['ID']})"
            ])
    ]);
    ?>

</div>

<script>
    function editarUsuario(id) {
        location.href = '<?= url("usuarios/editar") ?>/' + id;
    }

    function eliminarUsuario(id) {
        if (confirm('¿Estás seguro de eliminar este usuario?')) {
            fetch('<?= url("usuarios/eliminar") ?>/' + id, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }
    }
</script>