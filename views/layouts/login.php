<?php
// ==========================================
// views/auth/login.php - Vista de Login
// ==========================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Transcooler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #063a61 0%, #0a5280 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
            padding: 3rem;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-container img {
            max-width: 70%;
            height: auto;
        }
        .btn-login {
            background-color: #fabf19;
            color: #063a61;
            font-weight: bold;
            border: none;
            padding: 0.75rem;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: #e5ae17;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(250, 191, 25, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-container">
            <img src="<?= asset('img/logo1.png') ?>" alt="Transcooler Logo">
        </div>

        <h4 class="text-center mb-4">Iniciar Sesión</h4>

        <?php 
        // Mostrar alertas si hay errores o mensajes
        if ($error = flash('error')): 
            echo component('alert', ['type' => 'danger', 'message' => $error]);
        endif;
        
        if ($success = flash('success')): 
            echo component('alert', ['type' => 'success', 'message' => $success]);
        endif;
        ?>

        <form action="<?= url('login') ?>" method="POST">
            <?php 

            echo component('forms/input', [
                'name' => 'email',
                'type' => 'text',
                'label' => 'Email',
                'placeholder' => 'tu@email.com',
                'required' => true,
                'class' => 'mb-3'
            ]);

            echo component('forms/input', [
                'name' => 'password',
                'type' => 'password',
                'label' => 'Contraseña',
                'placeholder' => '••••••••',
                'required' => true,
                'class' => 'mb-3'
            ]);
            ?>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>

            <?php 
            echo component('forms/button', [
                'type' => 'submit',
                'text' => 'Ingresar',
                'color' => 'login',
                'class' => 'w-100'
            ]);
            ?>

            <div class="text-center mt-3">
                <a href="<?= url('forgot-password') ?>" class="text-muted">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

