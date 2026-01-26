<?php
// ==========================================
// SELECT COMPONENT - views/components/forms/select.php
// ==========================================
?>
<!-- USO: <?= component('forms/select', ['name' => 'tipo', 'label' => 'Tipo Usuario', 'options' => $tipos]) ?> -->

<?php
$name = $name ?? '';
$label = $label ?? '';
$options = $options ?? [];
$value = $value ?? old($name);
$required = $required ?? false;
$class = $class ?? '';
$error = $error ?? '';
$placeholder = $placeholder ?? 'Seleccione...';
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
    
    <select 
        id="<?= $name ?>" 
        name="<?= $name ?>" 
        class="form-select <?= $error ? 'is-invalid' : '' ?> <?= $class ?>"
        <?= $required ? 'required' : '' ?>
    >
        <option value=""><?= $placeholder ?></option>
        <?php foreach ($options as $key => $text): ?>
            <option value="<?= $key ?>" <?= $value == $key ? 'selected' : '' ?>>
                <?= $text ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <?php if ($error): ?>
        <div class="invalid-feedback">
            <?= $error ?>
        </div>
    <?php endif; ?>
</div>