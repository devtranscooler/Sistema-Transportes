

<div class="container-fluid" >
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Inicio</li>
            <li class="breadcrumb-item active">Roles</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <h2>Gestión de roles</h2>
        </div>
        <div class="col-auto">
            <?php 
                echo component('forms/button',[
                    'text' => 'Nuevo Rol',
                    'icon' => 'plus-circle',
                    'color' => 'primary'
                ])
            ?>
        </div>
    </div>

    <div class="row">
        <?php 
            $rows = [];
            foreach($roles as $role){
                $rows[] = [
                    'ID' => $role->id_rol,
                    'Nombre' => $role->rol_descripcion,
                    'Fecha alta' => $role->fecha_alta
                ];
            }

        echo component('table',[
            'id' => 'tbalaRoleConAcciones',
            'headers'=> ['ID','Nombre', 'Fecha alta'],
            'rows' =>$rows,
            'actions' => fn($row)=> component('forms/button',[
                'text' => '',
                'icon' => 'pencil',
                'size' => 'sm',
                'title' => 'Editar',
                
            ])
        ]);
        ?>
    </div>
</div>