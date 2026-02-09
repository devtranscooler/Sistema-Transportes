<?php
// ==========================================
// views/pages/usuarios/form.php - Formulario de Usuario
// ==========================================
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><?php echo(($usuario->id ?? false) ? 'Editar Usuario' : 'Nuevo Usuario') ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('usuarios/guardar') ?>"
                        method="POST"
                        enctype="multipart/form-data">
        
                        <input type="hidden" name="id" value="<?= $usuario->id ?? null ?>">
        
                        <!-- Información Personal -->
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person"></i> Información Personal
                        </h5>
        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control"
                                    value="<?= htmlspecialchars($usuario->nombre ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Apellido Paterno *</label>
                                <input type="text" name="apellidoP" class="form-control"
                                    value="<?= htmlspecialchars($usuario->apellidoP ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="apellidoM" class="form-control"
                                    value="<?= htmlspecialchars($usuario->apellidoM ?? '') ?>">
                            </div>
                        </div>
        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($usuario->email ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="fecNac" class="form-control"
                                    value="<?= $usuario->fecNac ?? '' ?>">
                            </div>
                            <!-- <div class="col-md-4 mb-3">
                                <label class="form-label">Foto</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <small class="text-muted">Máximo 2MB. Formatos: JPG, PNG, GIF</small>
                            </div> -->
                        </div>
        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Móvil</label>
                                <input type="tel" name="movil" class="form-control"
                                    value="<?= htmlspecialchars($usuario->movil ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control"
                                    value="<?= htmlspecialchars($usuario->telefono ?? '') ?>">
                            </div>
                        </div>
        
                        <!-- Información Laboral -->
                        <h5 class="border-bottom pb-2 mb-3 mt-2">
                            <i class="bi bi-briefcase"></i> Información Laboral
                        </h5>
        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No. Empleado</label>
                                <input type="text" name="noEmpleado" class="form-control"
                                    value="<?= htmlspecialchars($usuario->noEmpleado ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Puesto</label>
                                <input type="text" name="puesto" class="form-control"
                                    value="<?= htmlspecialchars($usuario->puesto ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Área</label>
                                <input type="text" name="area" class="form-control"
                                    value="<?= htmlspecialchars($usuario->area ?? '') ?>">
                            </div>
                        </div>
        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">CEDIS</label>
                                <input type="text" name="cedis" class="form-control"
                                    value="<?= htmlspecialchars($usuario->cedis ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jefe Inmediato</label>
                                <input type="text" name="jefeInmediato" class="form-control"
                                    value="<?= htmlspecialchars($usuario->jefeInmediato ?? '') ?>">
                            </div>

                            <input type="hidden" name="idRol" value="1">
                        </div>
        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha Contratación</label>
                                <input type="date" name="fecContratacion" class="form-control"
                                    value="<?= $usuario->fecContratacion ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Días de Vacaciones</label>
                                <input type="number" name="diasVacaciones" class="form-control"
                                    value="<?= $usuario->diasVacaciones ?? 0 ?>" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Días Disfrutados</label>
                                <input type="number" name="diasVacDisfrutados" class="form-control"
                                    value="<?= $usuario->diasVacDisfrutados ?? 0 ?>" min="0">
                            </div>
                        </div>
        
                        <!-- Seguridad -->
                        <h5 class="border-bottom pb-2 mb-3 mt-2">
                            <i class="bi bi-shield-lock"></i> Seguridad
                        </h5>
        
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <label class="form-label">
                                    Contraseña <?= isset($usuario->id) ? '(dejar vacío para no cambiar)' : '*' ?>
                                </label>
                                <input type="password" name="password" class="form-control"
                                    <?= isset($usuario->id) ? 'required' : '' ?>
                                    minlength="6">
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estatus</label>
                                <select name="estatus" class="form-select">
                                    <option value="ACTIVO" <?= ($usuario->estatus ?? 'ACTIVO') === 'ACTIVO' ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="INACTIVO" <?= ($usuario->estatus ?? '') === 'INACTIVO' ? 'selected' : '' ?>>
                                        Inactivo
                                    </option>
                                </select>
                            </div>
                        </div>
        
                        <!-- Botones -->
                        <div class="text-end mt-1">
                            <a href="<?= url('usuarios') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>