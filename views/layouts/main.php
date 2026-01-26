<?php
// ==========================================
// views/layouts/main.php - Layout Principal
// ==========================================
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Transcooler' ?></title>

    <!-- ========================================== -->
    <!-- jQuery (DEBE IR ANTES de Bootstrap y DataTables) -->
    <!-- ========================================== -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- ========================================== -->
    <!-- jQuery UI (si lo necesitas) -->
    <!-- ========================================== -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" type="text/javascript"></script> 

    <!-- ========================================== -->
    <!-- Bootstrap Icons -->
    <!-- ========================================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- ========================================== -->
    <!-- Bootstrap 5 CSS -->
    <!-- ========================================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ========================================== -->
    <!-- DataTables CSS (Bootstrap 5 Integration) -->
    <!-- ========================================== -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">


    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">

    <?= $additionalCss ?? '' ?>
</head>

<body>
    <div class="topbar">
        <div class="d-flex align-items-center">
            <button class="btn-toggle-topbar" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <div class="logo-container">
                <img src="<?= asset('img/logo1.png') ?>" alt="Logo" class="logo">
            </div>
        </div>

        <div class="user-actions d-flex align-items-center gap-3">
            <button onclick="toggleTheme()" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px;">
                🌙 / ☀️
            </button>
            <div class="user-info">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-md-inline"><?= Usuario::current()->getNombreCompleto() ?></span>
            </div>
        </div>
    </div>

    <?php
    $usuario = Usuario::current();
    $menus = $usuario->getMenus();
    ?>

    <div class="sidebar" id="sidebar">
        <ul>
            <?php foreach ($menus as $menu): ?>
                <!-- Si tiene hijos (submenú) -->
                <?php if (isset($menu['children']) && count($menu['children']) > 0): ?>
                    <li class="has-submenu">
                        <!-- Botón padre (NO tiene href porque abre/cierra) -->
                        <a class="menu__father" href="javascript:void(0)">
                            <i class="bi <?= $menu['icon'] ?? 'bi-question-lg' ?>"></i>
                            <span class="menu-text"><?= $menu['nombre'] ?></span>
                            <i class="bi bi-chevron-down arrow-icon"></i>
                        </a>
                        <ul class="submenu">
                            <?php foreach ($menu['children'] as $child): ?>
                                <!-- Cada hijo va en su propio <li> -->
                                <li>
                                    <a class="menu__children" href="<?= url($child['url']) ?>">
                                        <span class="menu-text"><?= $child['nombre'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <!-- Si NO tiene hijos (menú simple) -->
                <?php else: ?>
                    <li>
                        <a class="menu__father" href="<?= url($menu['url']) ?>">
                            <i class="bi <?= $menu['icon'] ?? 'bi-question-lg' ?>"></i>
                            <span class="menu-text"><?= $menu['nombre'] ?></span>
                        </a>
                    </li>
                <?php endif; ?>

            <?php endforeach; ?>

            <!-- Botón de cerrar sesión -->
            <li>
                <a class="menu__father btn-logout" href="<?= url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="menu-text">Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <div class="content">
        <?php
        if ($error = flash('error')):
            echo component('alert', ['type' => 'danger', 'message' => $error]);
        endif;

        if ($success = flash('success')):
            echo component('alert', ['type' => 'success', 'message' => $success]);
        endif;
        ?>

        <?= $content ?>
    </div>

</body>
<!-- ========================================== -->
<!-- Bootstrap 5 Bundle (JS + Popper) -->
<!-- ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ========================================== -->
<!-- DataTables Core JS -->
<!-- ========================================== -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- ========================================== -->
<!-- DataTables Extensiones  Responsive (para móviles) -->
<!-- ========================================== -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- Buttons (para exportar a Excel, PDF, etc.) -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!--<script src="asset('js/app.js') "></script>-->

<?= $additionalJs ?? '' ?>

<script>
    // Toggle sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    }

    // Tema Oscuro / Claro
    const toggleTheme = () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    }

    // Cargar tema guardado al inicio
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();


    document.addEventListener('DOMContentLoaded', function() {

        const dropdownTriggers = document.querySelectorAll('.has-submenu > .menu__father');

        dropdownTriggers.forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const parentLi = this.parentElement;

                const wasOpen = parentLi.classList.contains('open');

                document.querySelectorAll('.has-submenu').forEach(item => {
                    item.classList.remove('open');
                });

                if (!wasOpen) {
                    parentLi.classList.add('open');
                }
            });
        });

        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar.contains(e.target)) {
                document.querySelectorAll('.has-submenu').forEach(item => {
                    item.classList.remove('open');
                });
            }
        });
    });
</script>

</html>