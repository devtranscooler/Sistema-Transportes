<?php
// ==========================================
// PAGINATION COMPONENT - views/components/pagination.php
// ==========================================
// USO: component('pagination', ['current' => 1, 'total' => 10, 'url' => '/usuarios'])

$current = $current ?? 1;
$total = $total ?? 1;
$url = $url ?? '#';
?>

<?php if ($total > 1): ?>
    <nav>
        <ul class="pagination">
            <!-- Previous -->
            <li class="page-item <?= $current == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $url ?>?page=<?= $current - 1 ?>">Anterior</a>
            </li>

            <!-- Pages -->
            <?php for ($i = 1; $i <= $total; $i++): ?>
                <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $url ?>?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= $current == $total ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $url ?>?page=<?= $current + 1 ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>