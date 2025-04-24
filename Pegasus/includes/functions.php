<?php
/**
 * Funciones generales del sistema
 * Sistema de Gestión Hospitalaria Pegasus
 */

/**
 * Redirecciona a una URL específica
 *
 * @param string $url URL a la que se redireccionará
 * @return void
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Muestra un mensaje de error en la sesión
 *
 * @param string $message Mensaje de error
 * @return void
 */
function showError($message) {
    $_SESSION['error_message'] = $message;
}

/**
 * Muestra un mensaje de éxito en la sesión
 *
 * @param string $message Mensaje de éxito
 * @return void
 */
function showSuccess($message) {
    $_SESSION['success_message'] = $message;
}

/**
 * Muestra un mensaje de información en la sesión
 *
 * @param string $message Mensaje de información
 * @return void
 */
function showInfo($message) {
    $_SESSION['info_message'] = $message;
}

/**
 * Obtiene y limpia mensajes almacenados en la sesión
 *
 * @param string $type Tipo de mensaje (error, success, info)
 * @return string|null Mensaje almacenado o null si no hay
 */
function getMessage($type) {
    $key = "{$type}_message";
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

/**
 * Limpia y sanitiza input de usuario
 *
 * @param string $data Datos a limpiar
 * @return string Datos limpios
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Valida si el usuario está autenticado
 *
 * @return boolean True si está autenticado, false en caso contrario
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verifica si el usuario tiene un rol específico
 *
 * @param string|array $roles Rol o roles a verificar
 * @return boolean True si tiene el rol, false en caso contrario
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    if (is_array($roles)) {
        return in_array($_SESSION['user_role'], $roles);
    }
    
    return $_SESSION['user_role'] == $roles;
}

/**
 * Obtiene la URL actual
 *
 * @return string URL actual
 */
function getCurrentUrl() {
    return $_SERVER['REQUEST_URI'];
}

/**
 * Verifica si una ruta está activa para el menú
 *
 * @param string $path Ruta a verificar
 * @return boolean True si está activa, false en caso contrario
 */
function isActiveMenu($path) {
    $current = getCurrentUrl();
    if ($path == '/' && $current == BASE_URL) {
        return true;
    }
    return strpos($current, BASE_URL . $path) !== false;
}

/**
 * Formatea una fecha a formato legible
 *
 * @param string $date Fecha en formato SQL
 * @param string $format Formato de salida
 * @return string Fecha formateada
 */
function formatDate($fechaOriginal, $formato = 'd/m/Y H:i') {
    try {
        // Reemplazar puntos con dos puntos para compatibilidad con PHP
        $fechaNormalizada = preg_replace('/(\d{2})\.(\d{2})\.(\d{2})/', '$1:$2:$3', $fechaOriginal);
        
        // Intentar crear objeto DateTime
        $fecha = new DateTime($fechaNormalizada);

        return $fecha->format($formato);
    } catch (Exception $e) {
        logError("Error al formatear fecha: " . $e->getMessage());
        return $fechaOriginal; // Devolver original si falla
    }
}


/**
 * Formatea un número como moneda
 *
 * @param float $amount Cantidad a formatear
 * @param string $symbol Símbolo de moneda
 * @return string Cantidad formateada
 */
function formatCurrency($amount, $symbol = '$') {
    return $symbol . number_format($amount, 2, '.', ',');
}

/**
 * Registra un error en el archivo de logs
 *
 * @param string $message Mensaje de error
 * @return void
 */
function logError($message) {
    $logFile = LOGS_DIR . '/error.log';
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user = $_SESSION['username'] ?? 'Not logged in';
    $logMessage = "[$date] [$ip] [$user] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Genera un token CSRF para seguridad de formularios
 *
 * @return string Token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 *
 * @param string $token Token a verificar
 * @return boolean True si es válido, false en caso contrario
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token) {
        return false;
    }
    return true;
}

/**
 * Convierte fecha de formato dd/mm/yyyy a formato SQL
 *
 * @param string $date Fecha en formato dd/mm/yyyy
 * @return string Fecha en formato SQL yyyy-mm-dd
 */
function dateToSQL($date) {
    if (!$date) return null;
    $parts = explode('/', $date);
    if (count($parts) !== 3) return null;
    return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
}

/**
 * Verifica si una cadena es una fecha válida en formato dd/mm/yyyy
 *
 * @param string $date Fecha a validar
 * @return boolean True si es válida, false en caso contrario
 */
function isValidDate($date) {
    if (!$date) return false;
    $parts = explode('/', $date);
    if (count($parts) !== 3) return false;
    return checkdate((int)$parts[1], (int)$parts[0], (int)$parts[2]);
}

/**
 * Incluye un archivo de la carpeta includes
 *
 * @param string $file Nombre del archivo
 * @return void
 */
function includeFile($file) {
    $path = INCLUDES_DIR . '/' . $file . '.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        logError("Archivo no encontrado: $path");
        echo "Error: Archivo no encontrado.";
    }
}

/**
 * Valida un correo electrónico
 *
 * @param string $email Correo a validar
 * @return boolean True si es válido, false en caso contrario
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valida un número de teléfono
 *
 * @param string $phone Teléfono a validar
 * @return boolean True si es válido, false en caso contrario
 */
function isValidPhone($phone) {
    // Acepta 8 a 20 caracteres con dígitos, espacios, +, -, paréntesis
    return preg_match('/^\+?[0-9\s\-()]{8,20}$/', $phone);
}


/**
 * Dar de alta a un paciente hospitalizado
 * 
 * @param int $hospitalizacion_id ID de la hospitalización
 * @param string $notas_alta Notas del alta médica
 * @return bool Éxito o fracaso de la operación
 */
function darAltaPaciente($hospitalizacion_id, $notas_alta) {
    try {
        // Validar datos
        if (empty($hospitalizacion_id)) {
            showError("El ID de hospitalización es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Iniciar una transacción
        $db->beginTransaction();
        
        try {
            // Verificar que la hospitalización existe y está activa
            $query = "SELECT FIDE_SALA_ID 
                      FROM FIDE_HOSPITALIZACIONES_TB 
                      WHERE FIDE_HOSPITALIZACION_ID = :id 
                      AND FIDE_FECHA_ALTA IS NULL 
                      AND FIDE_ESTADO = 'ACTIVO'";
            
            $sala_id = $db->queryValue($query, ['id' => $hospitalizacion_id], 'FIDE_SALA_ID');
            
            if (empty($sala_id)) {
                throw new Exception("La hospitalización no existe o ya ha sido dada de alta");
            }
            
            // Actualizar registro de hospitalización
            $query = "UPDATE FIDE_HOSPITALIZACIONES_TB 
                      SET FIDE_FECHA_ALTA = SYSTIMESTAMP, 
                          FIDE_NOTAS_ALTA = :notas,
                          FIDE_ESTADO = 'COMPLETADO'
                      WHERE FIDE_HOSPITALIZACION_ID = :id";
            
            $db->query($query, ['id' => $hospitalizacion_id, 'notas' => $notas_alta]);
            
            // Actualizar estado de la sala
            $query = "UPDATE FIDE_SALAS_TB 
                      SET FIDE_ESTADO_SALA_ID = 1 
                      WHERE FIDE_SALA_ID = :sala_id";
            
            $db->query($query, ['sala_id' => $sala_id]);
            
            // Confirmar la transacción
            $db->commit();
            
            showSuccess("Paciente dado de alta correctamente");
            return true;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $db->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        logError("Error al dar de alta: " . $e->getMessage());
        showError("No se pudo dar de alta al paciente: " . $e->getMessage());
        return false;
    }
}


