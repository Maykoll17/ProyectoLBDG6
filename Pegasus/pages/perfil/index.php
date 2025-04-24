<?php
/**
 * Página de perfil de usuario
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Verificar sesión
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/perfil.php';

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once INCLUDES_DIR . '/Database.php';
require_once INCLUDES_DIR . '/functions.php';
require_once MODELS_DIR . '/empleados.php';

// Iniciar sesión
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Obtener el perfil del usuario
$usuario_id = $_SESSION['user_id'];
$perfil = obtenerPerfilUsuario($usuario_id);

// Obtener el historial de actividades
$actividades = obtenerHistorialActividades($usuario_id, 10);

// Procesar formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        showError("Error de seguridad: token inválido");
    } else {
        // Sanitizar datos
        $nombre_completo = sanitizeInput($_POST['nombre_completo']);
        $correo = sanitizeInput($_POST['correo']);
        $telefono = sanitizeInput($_POST['telefono']);
        
        // Actualizar perfil
        if (actualizarPerfilUsuario($usuario_id, $nombre_completo, $correo, $telefono)) {
            // Registrar actividad
            registrarActividad($usuario_id, 'PERFIL', 'Actualización de datos de perfil');
            
            // Recargar datos del perfil
            $perfil = obtenerPerfilUsuario($usuario_id);
        }
    }
}

// Incluir header
$pageTitle = "Mi Perfil";
include_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4"><i class="fas fa-user-circle"></i> Mi Perfil</h1>
    
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
    
    <?php if ($info = getMessage('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $info; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Datos del perfil -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-id-card"></i> Información Personal</h5>
                </div>
                <div class="card-body">
                    <?php if ($perfil): ?>
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="usuario_nombre" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="usuario_nombre" value="<?php echo htmlspecialchars($perfil['FIDE_USUARIO_NOMBRE']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nombre_completo" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($perfil['FIDE_NOMBRE_COMPLETO']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($perfil['FIDE_CORREO']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($perfil['FIDE_TELEFONO']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <input type="text" class="form-control" id="rol" value="<?php echo htmlspecialchars($perfil['FIDE_ROL_NOMBRE']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha_creacion" class="form-label">Fecha de Registro</label>
                            <input type="text" class="form-control" id="fecha_creacion" value="<?php echo formatDate($perfil['FIDE_FECHA_CREACION']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ultimo_acceso" class="form-label">Último Acceso</label>
                            <input type="text" class="form-control" id="ultimo_acceso" value="<?php echo formatDate($perfil['FIDE_ULTIMO_ACCESO']); ?>" readonly>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="actualizar_perfil" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="cambiar-clave.php" class="btn btn-outline-secondary">
                                <i class="fas fa-key"></i> Cambiar Contraseña
                            </a>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No se pudo cargar la información del perfil.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Historial de actividades -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-history"></i> Actividad Reciente</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($actividades)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($actividades as $actividad): ?>
                                <tr>
                                    <td><?php echo formatDate($actividad['FIDE_ACTIVIDAD_FECHA']); ?></td>
                                    <td>
                                        <?php
                                        $icono = 'fa-info-circle text-info';
                                        switch ($actividad['FIDE_ACTIVIDAD_TIPO']) {
                                            case 'LOGIN':
                                                $icono = 'fa-sign-in-alt text-success';
                                                break;
                                            case 'LOGOUT':
                                                $icono = 'fa-sign-out-alt text-warning';
                                                break;
                                            case 'PERFIL':
                                                $icono = 'fa-user-edit text-primary';
                                                break;
                                            case 'ERROR':
                                                $icono = 'fa-exclamation-circle text-danger';
                                                break;
                                        }
                                        ?>
                                        <i class="fas <?php echo $icono; ?>"></i>
                                        <?php echo htmlspecialchars($actividad['FIDE_ACTIVIDAD_TIPO']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($actividad['FIDE_ACTIVIDAD_DESCRIPCION']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay actividades recientes para mostrar.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir footer
include_once '../../includes/footer.php';
?>