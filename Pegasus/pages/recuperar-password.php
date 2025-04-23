<?php
/**
 * Página de recuperación de contraseña
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

// Variables para mensajes
$errorMsg = '';
$successMsg = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errorMsg = 'Error de seguridad: Token inválido.';
    } else {
        // Obtener datos del formulario
        $username = sanitizeInput($_POST['username']);
        
        // Validar datos
        if (empty($username)) {
            $errorMsg = 'Por favor, ingrese su nombre de usuario.';
        } else {
            try {
                global $db_config;
                $db = Database::getInstance($db_config);
                
                // Verificar si el usuario existe
                $query = "SELECT u.FIDE_USUARIO_ID, u.FIDE_NOMBRE_USUARIO, 
                                 e.FIDE_CORREO_EMPLEADO as EMAIL
                          FROM FIDE_USUARIOS_TB u
                          JOIN FIDE_EMPLEADOS_TB e ON u.FIDE_USUARIO_ID = e.FIDE_EMPLEADO_CEDULA
                          WHERE UPPER(u.FIDE_NOMBRE_USUARIO) = UPPER(:username)
                          AND u.FIDE_ESTADO_USUARIO = 'ACTIVO'";
                
                $usuario = $db->queryOne($query, ['username' => $username]);
                
                if ($usuario && !empty($usuario['EMAIL'])) {
                    // Generar token único
                    $token = bin2hex(random_bytes(32));
                    $expiracion = date('Y-m-d H:i:s', strtotime('+24 hours'));
                    
                    // Guardar token en la base de datos
                    $db->query("
                        INSERT INTO FIDE_TOKENS_SEGURIDAD_TB 
                        (FIDE_USUARIO_ID, FIDE_TOKEN, FIDE_FECHA_CREACION, FIDE_FECHA_EXPIRACION, FIDE_ESTADO)
                        VALUES (:user_id, :token, SYSTIMESTAMP, 
                                TO_TIMESTAMP(:expiracion, 'YYYY-MM-DD HH24:MI:SS'), 'ACTIVO')
                    ", [
                        'user_id' => $usuario['FIDE_USUARIO_ID'],
                        'token' => $token,
                        'expiracion' => $expiracion
                    ]);
                    
                    // URL para restablecer contraseña
                    $resetUrl = BASE_URL . '/pages/reset-password.php?token=' . $token;
                    
                    // En un sistema real, aquí enviarías un correo electrónico
                    // Por ahora, solo mostraremos un mensaje de éxito simulando el envío
                    
                    $successMsg = "Se ha enviado un correo a la dirección asociada con instrucciones para restablecer su contraseña.";
                    
                    // Para propósitos de desarrollo, mostramos el enlace (eliminar en producción)
                    $successMsg .= "<br><small>Enlace de desarrollo: <a href='$resetUrl'>Restablecer contraseña</a></small>";
                    
                    // Registrar la acción
                    $db->executeProcedure("FIDE_SEGURIDAD_PKG.FIDE_REGISTRAR_LOG_ACCESO_PROC", [
                        'p_usuario_id' => $usuario['FIDE_USUARIO_ID'],
                        'p_ip_acceso' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                        'p_accion' => 'RECUPERAR_PASSWORD',
                        'p_resultado' => 'SOLICITUD'
                    ]);
                    
                } else {
                    // No revelamos si el usuario existe por seguridad
                    $successMsg = "Si el usuario existe, se ha enviado un correo con instrucciones para restablecer su contraseña.";
                }
            } catch (Exception $e) {
                logError("Error en recuperación de contraseña: " . $e->getMessage());
                $errorMsg = 'Error del sistema. Intente más tarde.';
            }
        }
    }
}

// Título de la página
$pageTitle = 'Recuperar Contraseña';
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
                <p class="text-muted">Recuperación de Contraseña</p>
            </div>
            
            <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $errorMsg; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $successMsg; ?>
            </div>
            <div class="mt-3 text-center">
                <a href="<?php echo BASE_URL; ?>/pages/login.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Volver al Login
                </a>
            </div>
            <?php else: ?>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-text">
                        Ingrese su nombre de usuario y le enviaremos instrucciones para restablecer su contraseña.
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Enviar Instrucciones
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