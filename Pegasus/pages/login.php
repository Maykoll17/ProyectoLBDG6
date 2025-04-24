<?php
/**
 * Página de inicio de sesión
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Incluir archivos necesarios
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/Database.php';

// Si el usuario ya está autenticado, redirigir al dashboard
if (isLoggedIn()) {
    redirect('/pages/dashboard.php');
    exit();
}

// Incluir archivos necesarios
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/Database.php';
require_once '../models/auth.php';

// Procesar formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password']; // No sanitizar password para preservar caracteres especiales
    
    // Intentar autenticar
    $usuario = autenticarUsuario($username, $password);
    
    // Si la autenticación fue exitosa
    if ($usuario) {
        // Establecer variables de sesión
        $_SESSION['user_id'] = $usuario['FIDE_USUARIO_ID'];
        $_SESSION['username'] = $usuario['FIDE_USUARIO_NOMBRE'];
        $_SESSION['nombre_completo'] = $usuario['FIDE_NOMBRE_COMPLETO'];
        $_SESSION['user_role'] = $usuario['FIDE_ROL_NOMBRE'];
        $_SESSION['role_id'] = $usuario['FIDE_ROL_ID'];
        
        // Si es un empleado, guardar su cédula
        if (isset($usuario['FIDE_EMPLEADO_CEDULA']) && !empty($usuario['FIDE_EMPLEADO_CEDULA'])) {
            $_SESSION['empleado_cedula'] = $usuario['FIDE_EMPLEADO_CEDULA'];
        }
        
        // Redirigir al dashboard
        redirect('dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Gestión Hospitalaria Pegasus</title>
    
    <!-- Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Estilos personalizados -->
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            max-width: 400px;
            margin: 0 auto;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .login-header {
            text-align: center;
            padding: 2rem 0;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card">
            <div class="login-header">
                <img src="../assets/img/logo.png" alt="Logo Pegasus" class="logo">
                <h2>Sistema de Gestión Hospitalaria</h2>
                <p class="text-muted">Inicie sesión para continuar</p>
            </div>
            
            <div class="card-body p-4">
                <?php 
                // Mostrar mensajes
                if ($error = getMessage('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success = getMessage('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required 
                                   placeholder="Nombre de usuario" autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required 
                                   placeholder="Contraseña">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <a href="forgot-password.php" class="text-decoration-none">¿Olvidó su contraseña?</a>
                </div>
            </div>
            
            <div class="card-footer text-center py-3">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Pegasus Hospital Management</p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts personalizados -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.querySelector('.toggle-password');
            
            toggleButton.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const inputField = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    inputField.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>