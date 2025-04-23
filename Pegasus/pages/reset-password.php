<?php
/**
 * Página para restablecer contraseña
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

// Verificar que se haya proporcionado un token
if (!isset($_GET['token']) || empty($_GET['token'])) {
    redirect('/pages/login.php');
    exit();
}

$token = $_GET['token'];
$validToken = false;
$userId = null;
$errorMsg = '';
$successMsg = '';

// Verificar el token
try {
    global $db_config;
    $db = Database::getInstance($db_config);
    
    $query = "SELECT FIDE_USUARIO_ID, FIDE_FECHA_EXPIRACION 
              FROM FIDE_TOKENS_SEGURIDAD_TB 
              WHERE FIDE_TOKEN = :token 
              AND FIDE_ESTADO = 'ACTIVO'";
    
    $tokenInfo = $db->queryOne($query, ['token' => $token]);
    
    if ($tokenInfo) {
        // Verificar si el token ha expirado
        $now = new DateTime();
        $expiracion = new DateTime($tokenInfo['FIDE_FECHA_EXPIRACION']);
        
        if ($now < $expiracion) {
            $validToken = true;
            $userId = $tokenInfo['FIDE_USUARIO_ID'];
        } else {
            $errorMsg = 'El enlace de restablecimiento ha expirado.';
        }
    } else {
        $errorMsg = 'El enlace de restablecimiento no es válido.';
    }
} catch (Exception $e) {
    logError("Error al verificar token: " . $e->getMessage());
    $errorMsg = 'Error del sistema. Intente más tarde.';
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    // Verificar CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errorMsg = 'Error de seguridad: Token inválido.';
    } else {
        // Obtener datos del formulario
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Validar contraseñas
        if (empty($password) || empty($confirmPassword)) {
            $errorMsg = 'Por favor, complete todos los campos.';
        } elseif ($password !== $confirmPassword) {
            $errorMsg = 'Las contraseñas no coinciden.';
        } elseif (strlen($password) < 8) {
            $errorMsg = 'La contraseña debe tener al menos 8 caracteres.';
        } else {
            try {
                // Actualizar la contraseña
                $db->query("UPDATE FIDE_USUARIOS_TB 
                           SET FIDE_CONTRASENA = :password, 
                               FIDE_INTENTOS_FALLIDOS = 0
                           WHERE FIDE_USUARIO_ID = :user_id", 
                          [
                              'password' => $password, 
                              'user_id' => $userId
                          ]);
                
                // Marcar el token como usado
                $db->query("UPDATE FIDE_TOKENS_SEGURIDAD_TB 
                           SET FIDE_ESTADO = 'USADO' 
                           WHERE FIDE_TOKEN = :token", 
                          ['token' => $token]);
                
                // Registrar la acción
                $db->executeProcedure("FIDE_SEGURIDAD_PKG.FIDE_REGISTRAR_LOG_ACCESO_PROC", [
                    'p_usuario_id' => $userId,
                    'p_ip_acceso' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                    'p_accion' => 'RESET_PASSWORD',
                    'p_resultado' => 'EXITO'
                ]);
                
                $successMsg = 'Su contraseña ha sido restablecida correctamente.';
            } catch (Exception $e) {
                logError("Error al restablecer contraseña: " . $e->getMessage());
                $errorMsg = 'Error del sistema. Intente más tarde.';
            }
        }
    }
}

// Título de la página
$pageTitle = 'Restablecer Contraseña';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="<?php echo CSS_URL; ?>/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="brand-logo">
                <i class="fas fa-hospital"></i>
                <h1 class="h4"><?php echo SITE_NAME; ?></h1>
                <p class="text-muted">Restablecer Contraseña</p>
            </div>
            
            <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $errorMsg; ?>
            </div>
            <div class="mt-3 text-center">
                <a href="<?php echo BASE_URL; ?>/pages/login.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Volver al Login
                </a>
            </div>
            <?php elseif (!empty($successMsg)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $successMsg; ?>
            </div>
            <div class="mt-3 text-center">
                <a href="<?php echo BASE_URL; ?>/pages/login.php" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt"></i> Ir al Login
                </a>
            </div>
            <?php elseif ($validToken): ?>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?token=' . $token; ?>">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required autofocus>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Restablecer Contraseña
                    </button>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="<?php echo BASE_URL; ?>/pages/login.php">Volver al Login</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>