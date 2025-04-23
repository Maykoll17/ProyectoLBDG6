<?php
/**
 * Página de cierre de sesión
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Incluir archivos necesarios
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/Database.php';

// Registrar el evento de logout si el usuario estaba autenticado
if (isLoggedIn()) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Registrar el cierre de sesión
        $db->executeProcedure("FIDE_SEGURIDAD_PKG.FIDE_REGISTRAR_LOG_ACCESO_PROC", [
            'p_usuario_id' => $_SESSION['user_id'],
            'p_ip_acceso' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'p_accion' => 'LOGOUT',
            'p_resultado' => 'EXITO'
        ]);
    } catch (Exception $e) {
        logError("Error al registrar cierre de sesión: " . $e->getMessage());
    }
}

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al login
redirect('/pages/login.php');
exit();