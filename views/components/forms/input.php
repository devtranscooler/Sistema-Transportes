<?php
// ==========================================
// INPUT COMPONENT - views/components/forms/input.php
// ==========================================

//? USO: component('forms/input', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true])  

/**
 * Props disponibles:
 * - name: nombre del campo (requerido)
 * - label: etiqueta del campo
 * - type: tipo de input (text, email, password, etc.)
 * - value: valor inicial
 * - placeholder: placeholder
 * - required: si es requerido
 * - class: clases CSS adicionales
 * - error: mensaje de error a mostrar
 */

$name = $name ?? '';
$label = $label ?? '';
$type = $type ?? 'text';
$value = $value ?? old($name);
$placeholder = $placeholder ?? '';
$required = $required ?? false;
$class = $class ?? '';
$error = $error ?? '';
?>

<div class="mb-3">
    <?php if ($label): ?>
        <label for="<?= $name ?>" class="form-label">
            <?= $label ?>
            <?php if ($required): ?>
                <span class="text-danger">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    
    <input 
        type="<?= $type ?>" 
        id="<?= $name ?>" 
        name="<?= $name ?>" 
        class="form-control <?= $error ? 'is-invalid' : '' ?> <?= $class ?>"
        value="<?= htmlspecialchars($value) ?>"
        placeholder="<?= $placeholder ?>"
        <?= $required ? 'required' : '' ?>
    >
    
    <?php if ($error): ?>
        <div class="invalid-feedback">
            <?= $error ?>
        </div>
    <?php endif; ?>
</div>