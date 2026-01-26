<?php
// ==========================================
// MODAL COMPONENT - views/components/modal.php
// ==========================================

//<!-- USO: component('modal', ['id' => 'deleteModal', 'title' => 'Confirmar', 'content' => '¿Seguro?'])

$id = $id ?? 'modal';
$icon = $icon ?? ''; 
$color = $color ?? 'primary';
$btntext = $btntext ?? 'Abrir modal'; 
$title = $title ?? 'Modal';
$content = $content ?? '';
$size = $size ?? 'modal-xl'; // 'modal-sm', 'modal-lg', 'modal-xl'
$footer = $footer ?? '';
?>

<button type="button" class="fw-semibold btn btn-<?= $color ?>" data-bs-toggle="modal" data-bs-target="#<?= $id ?>">
    <?php if ($icon): ?>
        <i class="bi bi-<?= $icon ?>"></i>
    <?php endif; ?>
    <?= $btntext ?>
</button>


<div class="modal fade" id="<?= $id ?>" tabindex="-1">
    <div class="modal-dialog <?= $size ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $title ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?= $content ?>
            </div>
            <?php if ($footer): ?>
                <div class="modal-footer">
                    <?= $footer ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
