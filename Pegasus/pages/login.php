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

// Variable para almacenar errores
$loginError = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $loginError = 'Error de seguridad: Token inválido.';
    } else {
        // Obtener datos del formulario
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password']; // No sanitizamos la contraseña para no alterarla
        
        // Validar datos
        if (empty($username) || empty($password)) {
            $loginError = 'Por favor, complete todos los campos.';
        } else {
            try {
                global $db_config;
                $db = Database::getInstance($db_config);
                
                // Consultar usuario en la base de datos
                $query = "SELECT u.FIDE_USUARIO_ID, u.FIDE_NOMBRE_USUARIO, u.FIDE_CONTRASENA, 
                                 u.FIDE_ROL_ID, r.FIDE_NOMBRE_ROL, u.FIDE_ESTADO_USUARIO
                          FROM FIDE_USUARIOS_TB u
                          JOIN FIDE_ROLES_TB r ON u.FIDE_ROL_ID = r.FIDE_ROL_ID
                          WHERE UPPER(u.FIDE_NOMBRE_USUARIO) = UPPER(:username)";
                
                $usuario = $db->queryOne($query, ['username' => $username]);
                
                if ($usuario) {
                    // Verificar estado del usuario
                    if ($usuario['FIDE_ESTADO_USUARIO'] !== 'ACTIVO') {
                        $loginError = 'Su cuenta está bloqueada. Contacte al administrador.';
                    } 
                    // Verificar contraseña (en un sistema real, debería estar hasheada)
                    else if ($usuario['FIDE_CONTRASENA'] === $password) {
                        // Autenticación exitosa - configurar la sesión
                        $_SESSION['user_id'] = $usuario['FIDE_USUARIO_ID'];
                        $_SESSION['username'] = $usuario['FIDE_NOMBRE_USUARIO'];
                        $_SESSION['user_role'] = $usuario['FIDE_NOMBRE_ROL'];
                        
                        // Registrar el acceso exitoso
                        $db->executeProcedure("FIDE_SEGURIDAD_PKG.FIDE_REGISTRAR_LOG_ACCESO_PROC", [
                            'p_usuario_id' => $usuario['FIDE_USUARIO_ID'],
                            'p_ip_acceso' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                            'p_accion' => 'LOGIN',
                            'p_resultado' => 'EXITO'
                        ]);
                        
                        // Actualizar última conexión
                        $db->query("UPDATE FIDE_USUARIOS_TB SET FIDE_ULTIMA_CONEXION = SYSTIMESTAMP WHERE FIDE_USUARIO_ID = :user_id", 
                                  ['user_id' => $usuario['FIDE_USUARIO_ID']]);
                        
                        // Redirigir al dashboard
                        redirect('/pages/dashboard.php');
                        exit();
                    } else {
                        // Contraseña incorrecta
                        $loginError = 'Nombre de usuario o contraseña incorrectos.';
                        
                        // Incrementar contador de intentos fallidos
                        $db->query("UPDATE FIDE_USUARIOS_TB SET FIDE_INTENTOS_FALLIDOS = FIDE_INTENTOS_FALLIDOS + 1 WHERE FIDE_USUARIO_ID = :user_id", 
                                  ['user_id' => $usuario['FIDE_USUARIO_ID']]);
                        
                        // Registrar el acceso fallido
                        $db->executeProcedure("FIDE_SEGURIDAD_PKG.FIDE_REGISTRAR_LOG_ACCESO_PROC", [
                            'p_usuario_id' => $usuario['FIDE_USUARIO_ID'],
                            'p_ip_acceso' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                            'p_accion' => 'LOGIN',
                            'p_resultado' => 'FALLIDO'
                        ]);
                    }
                } else {
                    // Usuario no encontrado
                    $loginError = 'Nombre de usuario o contraseña incorrectos.';
                }
            } catch (Exception $e) {
                logError("Error en login: " . $e->getMessage());
                $loginError = 'Error del sistema. Intente más tarde.';
            }
        }
    }
}

// Título de la página
$pageTitle = 'Iniciar Sesión';
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
                <p class="text-muted">Sistema de Gestión Hospitalaria</p>
            </div>
            
            <?php if (!empty($loginError)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $loginError; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="<?php echo BASE_URL; ?>/pages/recuperar-password.php">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>