<?php
// ==========================================
// ALERT COMPONENT - views/components/alert.php
// ==========================================
// USO:  component('alert', ['type' => 'success', 'message' => 'Guardado correctamente']) 

$type = $type ?? 'info'; // success, danger, warning, info
$message = $message ?? '';
$dismissible = $dismissible ?? true;
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $type ?> <?= $dismissible ? 'alert-dismissible fade show' : '' ?>" role="alert">
        <?= $message ?>
        <?php if ($dismissible): ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <?php endif; ?>
    </div>
<?php endif; ?>