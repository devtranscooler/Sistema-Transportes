<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>404 - No encontrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center" style="min-height: 100vh; align-items: center;">
            <div class="col-md-6 text-center">
                <h1 class="display-1 text-primary">404</h1>
                <h2>¡Ups! Página no encontrada</h2>
                <p class="text-muted">La página que buscas no existe o fue movida.</p>
                <a href="<?= url('dashboard') ?>" class="btn btn-primary">Ir al Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>