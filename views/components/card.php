<?php
// ==========================================
// CARD COMPONENT - views/components/card.php
// ==========================================
//<!-- USO: component('card', ['title' => 'Usuarios', 'content' => $content])

$title = $title ?? '';
$content = $content ?? '';
$footer = $footer ?? '';
$class = $class ?? '';
?>

<div class="card <?= $class ?>">
    <?php if ($title): ?>
        <div class="card-header">
            <h5 class="card-title mb-0"><?= $title ?></h5>
        </div>
    <?php endif; ?>
    
    <div class="card-body">
        <?= $content ?>
    </div>
    
    <?php if ($footer): ?>
        <div class="card-footer">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>