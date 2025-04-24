<?php
/**
 * Modelo para la autenticación de usuarios
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con el paquete FIDE_AUTENTICACION_PKG
 */

/**
 * Autentica un usuario en el sistema
 * 
 * @param string $username Nombre de usuario
 * @param string $password Contraseña
 * @return bool|array False si falla la autenticación, array con datos del usuario si tiene éxito
 */
function autenticarUsuario($username, $password) {
    try {
        // Validar datos
        if (empty($username) || empty($password)) {
            showError("El nombre de usuario y la contraseña son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para verificar credenciales del usuario
        $query = "SELECT 
                    u.FIDE_USUARIO_ID, 
                    u.FIDE_USUARIO_NOMBRE,
                    u.FIDE_NOMBRE_COMPLETO,
                    u.FIDE_CORREO,
                    r.FIDE_ROL_ID,
                    r.FIDE_ROL_NOMBRE,
                    e.FIDE_EMPLEADO_CEDULA
                FROM 
                    FIDE_USUARIOS_TB u
                JOIN 
                    FIDE_ROLES_TB r ON u.FIDE_ROL_ID = r.FIDE_ROL_ID
                LEFT JOIN 
                    FIDE_EMPLEADOS_TB e ON u.FIDE_USUARIO_ID = e.FIDE_USUARIO_ID
                WHERE 
                    u.FIDE_USUARIO_NOMBRE = :username
                AND 
                    u.FIDE_CLAVE = DBMS_OBFUSCATION_TOOLKIT.MD5(input => UTL_RAW.CAST_TO_RAW(:password))
                AND 
                    u.FIDE_ESTADO = 'ACTIVO'";
        
        // Ejecutar la consulta
        $result = $db->queryOne($query, [
            'username' => $username,
            'password' => $password
        ]);
        
        if (!$result) {
            showError("Nombre de usuario o contraseña incorrectos");
            // Registrar intento fallido si existe el usuario
            $user_exists = $db->queryValue(
                "SELECT COUNT(*) FROM FIDE_USUARIOS_TB WHERE FIDE_USUARIO_NOMBRE = :username", 
                ['username' => $username]
            );
            
            if ($user_exists > 0) {
                // Obtener el ID del usuario para registrar la actividad
                $user_id = $db->queryValue(
                    "SELECT FIDE_USUARIO_ID FROM FIDE_USUARIOS_TB WHERE FIDE_USUARIO_NOMBRE = :username", 
                    ['username' => $username]
                );
                
                // Registrar intento fallido
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
                
                $db->executeProcedure('FIDE_AUTENTICACION_PKG.FIDE_REGISTRAR_INTENTO_FALLIDO_PROC', [
                    'p_usuario_id' => $user_id,
                    'p_ip' => $ip
                ]);
            }
            
            return false;
        }
        
        // Actualizar último acceso
        $db->executeProcedure('FIDE_AUTENTICACION_PKG.FIDE_ACTUALIZAR_ULTIMO_ACCESO_PROC', [
            'p_usuario_id' => $result['FIDE_USUARIO_ID']
        ]);
        
        // Registrar inicio de sesión exitoso
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        
        // Registrar actividad de login
        require_once dirname(__FILE__) . '/perfil.php';
        registrarActividad($result['FIDE_USUARIO_ID'], 'LOGIN', 'Inicio de sesión exitoso');
        
        return $result;
    } catch (Exception $e) {
        logError("Error al autenticar usuario: " . $e->getMessage());
        showError("Error al intentar iniciar sesión: " . $e->getMessage());
        return false;
    }
}

/**
 * Cierra la sesión del usuario actual
 * 
 * @return void
 */
function cerrarSesion() {
    try {
        // Si hay un usuario logueado, registrar la actividad de cierre de sesión
        if (isset($_SESSION['user_id'])) {
            require_once dirname(__FILE__) . '/perfil.php';
            registrarActividad($_SESSION['user_id'], 'LOGOUT', 'Cierre de sesión');
        }
        
        // Destruir la sesión
        session_unset();
        session_destroy();
        
    } catch (Exception $e) {
        logError("Error al cerrar sesión: " . $e->getMessage());
    }
}

/**
 * Verifica si una contraseña cumple con los requisitos de seguridad
 * 
 * @param string $password Contraseña a verificar
 * @return bool True si cumple los requisitos, false en caso contrario
 */
function verificarRequisitosClave($password) {
    // Mínimo 8 caracteres
    if (strlen($password) < 8) {
        return false;
    }
    
    // Debe contener al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // Debe contener al menos una letra minúscula
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // Debe contener al menos un número
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    return true;
}

/**
 * Solicita restablecimiento de contraseña
 * 
 * @param string $email Correo electrónico del usuario
 * @return bool True si se pudo enviar la solicitud, false en caso contrario
 */
function solicitarRestablecimientoClave($email) {
    try {
        if (empty($email) || !isValidEmail($email)) {
            showError("Debe proporcionar un correo electrónico válido");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar si existe el usuario con ese correo
        $query = "SELECT FIDE_USUARIO_ID FROM FIDE_USUARIOS_TB WHERE FIDE_CORREO = :email AND FIDE_ESTADO = 'ACTIVO'";
        $usuario_id = $db->queryValue($query, ['email' => $email]);
        
        if (!$usuario_id) {
            showError("No se encontró ningún usuario activo con ese correo electrónico");
            return false;
        }
        
        // Generar token de restablecimiento
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Guardar token en la base de datos
        $db->executeProcedure('FIDE_AUTENTICACION_PKG.FIDE_CREAR_TOKEN_RESET_PROC', [
            'p_usuario_id' => $usuario_id,
            'p_token' => $token,
            'p_expira' => $expira
        ]);
        
        // Enviar correo con el token (simulado)
        // En un entorno real, aquí se enviaría un correo electrónico con el enlace
        $reset_url = BASE_URL . 'pages/reset-password.php?token=' . $token;
        
        // Registrar actividad
        registrarActividad($usuario_id, 'PERFIL', 'Solicitud de restablecimiento de contraseña');
        
        showSuccess("Se ha enviado un correo con instrucciones para restablecer su contraseña");
        return true;
    } catch (Exception $e) {
        logError("Error al solicitar restablecimiento: " . $e->getMessage());
        showError("No se pudo procesar la solicitud de restablecimiento de contraseña");
        return false;
    }
}

/**
 * Restablece la contraseña de un usuario usando un token
 * 
 * @param string $token Token de restablecimiento
 * @param string $password Nueva contraseña
 * @param string $confirm_password Confirmación de nueva contraseña
 * @return bool True si se pudo restablecer la contraseña, false en caso contrario
 */
function restablecerClave($token, $password, $confirm_password) {
    try {
        if (empty($token) || empty($password) || empty($confirm_password)) {
            showError("Todos los campos son obligatorios");
            return false;
        }
        
        if ($password !== $confirm_password) {
            showError("Las contraseñas no coinciden");
            return false;
        }
        
        if (!verificarRequisitosClave($password)) {
            showError("La contraseña no cumple con los requisitos mínimos de seguridad");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar que el token sea válido y no haya expirado
        $query = "SELECT 
                    tr.FIDE_USUARIO_ID
                FROM 
                    FIDE_TOKENS_RESET_TB tr
                WHERE 
                    tr.FIDE_TOKEN = :token
                AND 
                    tr.FIDE_EXPIRA > SYSTIMESTAMP
                AND 
                    tr.FIDE_USADO = 'NO'";
        
        $usuario_id = $db->queryValue($query, ['token' => $token]);
        
        if (!$usuario_id) {
            showError("El enlace de restablecimiento es inválido o ha expirado");
            return false;
        }
        
        // Actualizar la contraseña
        $db->executeProcedure('FIDE_AUTENTICACION_PKG.FIDE_RESTABLECER_CLAVE_PROC', [
            'p_usuario_id' => $usuario_id,
            'p_clave_nueva' => $password,
            'p_token' => $token
        ]);
        
        // Registrar actividad
        registrarActividad($usuario_id, 'PERFIL', 'Restablecimiento de contraseña');
        
        showSuccess("Su contraseña ha sido restablecida correctamente. Ya puede iniciar sesión");
        return true;
    } catch (Exception $e) {
        logError("Error al restablecer contraseña: " . $e->getMessage());
        showError("No se pudo restablecer la contraseña: " . $e->getMessage());
        return false;
    }
}