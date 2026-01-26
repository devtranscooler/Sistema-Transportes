<?php
// ==========================================
// views/pages/usuarios/form.php - Formulario de Usuario
// ==========================================
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <?php
            $cardContent = '
                <form action="' . url('usuarios/guardar') . '" method="POST" id="usuarioForm">
                    <input type="hidden" name="id" value="' . ($usuario->id ?? '') . '">
                    
                    <div class="row">
                        <div class="col-md-6">
                            ' . component('forms/input', [
                                'name' => 'nombre',
                                'label' => 'Nombre',
                                'value' => $usuario->nombre ?? '',
                                'required' => true
                            ]) . '
                        </div>
                        <div class="col-md-6">
                            ' . component('forms/input', [
                                'name' => 'apellidoP',
                                'label' => 'Apellido Paterno',
                                'value' => $usuario->apellidoP ?? '',
                                'required' => true
                            ]) . '
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            ' . component('forms/input', [
                                'name' => 'apellidoM',
                                'label' => 'Apellido Materno',
                                'value' => $usuario->apellidoM ?? ''
                            ]) . '
                        </div>
                        <div class="col-md-6">
                            ' . component('forms/input', [
                                'name' => 'email',
                                'type' => 'email',
                                'label' => 'Email',
                                'value' => $usuario->email ?? '',
                                'required' => true
                            ]) . '
                        </div>
                    </div>

                    <div class="text-end">
                        ' . component('forms/button', [
                            'text' => 'Cancelar',
                            'color' => 'secondary',
                            'onclick' => "history.back()"
                        ]) . ' ' .
                        component('forms/button', [
                            'text' => 'Guardar',
                            'type' => 'submit',
                            'color' => 'primary',
                            'icon' => 'save'
                        ]) . '
                    </div>
                </form>
            ';

            echo component('card', [
                'title' => ($usuario->id ?? false) ? 'Editar Usuario' : 'Nuevo Usuario',
                'content' => $cardContent
            ]);
            ?>
        </div>
    </div>
</div>