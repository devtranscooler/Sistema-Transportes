<?php
// ==========================================
// TABLE COMPONENT CON DATATABLES.NET
// views/components/table.php
// ==========================================
// USO:  component('table', [
//         'headers' => ['ID', 'Nombre', 'Email'], 
//         'rows' => $usuarios,
//         'id' => 'miTabla', // IMPORTANTE: cada tabla necesita un ID único
//         'useDataTable' => true, // Activa DataTables
//         'dataTableOptions' => [ // Opciones opcionales de DataTables
//             'pageLength' => 10,
//             'order' => [[0, 'desc']]
//         ]
//       ]) 

// ========== PARÁMETROS ==========
$headers = $headers ?? []; // Encabezados de la tabla
$rows = $rows ?? []; // Filas de datos
$class = $class ?? 'table-striped'; // Clases CSS adicionales
$actions = $actions ?? null; // Callback para columna de acciones
$id = $id ?? 'dataTable_' . uniqid(); // ID único para la tabla (importante para DataTables)
$useDataTable = $useDataTable ?? false; // ¿Activar DataTables?
$dataTableOptions = $dataTableOptions ?? []; // Opciones personalizadas de DataTables

// ========== OPCIONES POR DEFECTO DE DATATABLES ==========
$defaultOptions = [
    'pageLength' => 10, // Registros por página
    'responsive' => false, // Responsivo para móviles
    'dom' => 'Bfrtip', // Layout: Buttons, filter, table, info, pagination
    'buttons' => ['copy', 'csv', 'excel', 'pdf', 'print'] // Botones de exportación
];

// Combinar opciones por defecto con las personalizadas
$finalOptions = array_merge($defaultOptions, $dataTableOptions);
$optionsJson = json_encode($finalOptions);
?>

<!-- ========== ESTRUCTURA DE LA TABLA ========== -->
<div class="table-responsive">
    <table id="<?= $id ?>" class="table <?= $class ?>">
        <thead>
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th><?= htmlspecialchars($header) ?></th>
                <?php endforeach; ?>
                <?php if ($actions): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <!-- Mensaje cuando no hay datos -->
                <tr>
                    <td colspan="<?= count($headers) + ($actions ? 1 : 0) ?>" class="text-center">
                        No hay datos disponibles
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?= htmlspecialchars($cell) ?></td>
                        <?php endforeach; ?>
                        <?php if ($actions): ?>
                            <td><?= $actions($row) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($useDataTable): ?>
<!-- ========== SCRIPTS DE DATATABLES ========== -->
<script>
// Solo cargamos DataTables si aún no está cargado
if (typeof jQuery === 'undefined') {
    console.error('⚠️ jQuery no está cargado. DataTables lo necesita.');
}

// Esperar a que el DOM esté listo
jQuery(document).ready(function($) {
    // Inicializar DataTable en esta tabla específica
    var table = $('#<?= $id ?>').DataTable(<?= $optionsJson ?>);
    
    // 🎨 EXTRA: Puedes agregar eventos personalizados aquí
    // Por ejemplo, resaltar fila al hacer clic:
    $('#<?= $id ?> tbody').on('click', 'tr', function() {
        $(this).toggleClass('table-active');
    });
    
    //console.log('✅ DataTable inicializado en #<?= $id ?>');
});
</script>
<?php endif; ?>