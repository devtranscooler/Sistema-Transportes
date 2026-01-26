<?php
// ==========================================
// BUTTON COMPONENT - views/components/forms/button.php
// ==========================================

//? USO:  component('forms/button', ['text' => 'Guardar', 'type' => 'submit', 'color' => 'primary']) 

$text = $text ?? 'Button';
$type = $type ?? 'button';
$color = $color ?? 'primary';
$size = $size ?? '';
$icon = $icon ?? '';
$class = $class ?? '';
$onclick = $onclick ?? '';
$disabled = $disabled ?? false;
?>

<button 
    type="<?= $type ?>" 
    class="fw-semibold btn btn-<?= $color ?> <?= $size ? 'btn-' . $size : '' ?> <?= $class ?>"
    <?= $onclick ? 'onclick="' . $onclick . '"' : '' ?>
    <?= $disabled ? 'disabled' : '' ?>
>
    <?php if ($icon): ?>
        <i class="bi bi-<?= $icon ?>"></i>
    <?php endif; ?>
    <?= $text ?>
</button>