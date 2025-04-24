<?php
/**
 * Modelo para la gestión de perfiles de usuario
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con el paquete FIDE_USUARIOS_PKG
 */

/**
 * Obtiene los datos del perfil de un usuario
 * 
 * @param int $usuario_id ID del usuario
 * @return array|null Datos del perfil o null si no se encuentra
 */
function obtenerPerfilUsuario($usuario_id) {
    try {
        if (empty($usuario_id)) {
            showError("El ID de usuario es obligatorio");
            return null;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener los datos del usuario
        $query = "SELECT 
                    u.FIDE_USUARIO_ID, 
                    u.FIDE_USUARIO_NOMBRE,
                    u.FIDE_NOMBRE_COMPLETO,
                    u.FIDE_CORREO,
                    u.FIDE_TELEFONO,
                    r.FIDE_ROL_NOMBRE,
                    u.FIDE_FECHA_CREACION,
                    u.FIDE_ULTIMO_ACCESO
                FROM 
                    FIDE_USUARIOS_TB u
                JOIN 
                    FIDE_ROLES_TB r ON u.FIDE_ROL_ID = r.FIDE_ROL_ID
                WHERE 
                    u.FIDE_USUARIO_ID = :usuario_id";
        
        // Ejecutar la consulta
        $result = $db->queryOne($query, ['usuario_id' => $usuario_id]);
        
        if (!$result) {
            showInfo("No se encontró el perfil de usuario");
            return null;
        }
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener perfil de usuario: " . $e->getMessage());
        showError("No se pudo obtener el perfil de usuario: " . $e->getMessage());
        return null;
    }
}

/**
 * Actualiza los datos del perfil de un usuario
 * 
 * @param int $usuario_id ID del usuario
 * @param string $nombre_completo Nombre completo del usuario
 * @param string $correo Correo electrónico del usuario
 * @param string $telefono Teléfono del usuario
 * @return bool Éxito o fracaso de la operación
 */
function actualizarPerfilUsuario($usuario_id, $nombre_completo, $correo, $telefono) {
    try {
        // Validar datos
        if (empty($usuario_id) || empty($nombre_completo)) {
            showError("El ID de usuario y nombre completo son obligatorios");
            return false;
        }
        
        if (!empty($correo) && !isValidEmail($correo)) {
            showError("El correo electrónico no es válido");
            return false;
        }
        
        if (!empty($telefono) && !isValidPhone($telefono)) {
            showError("El número de teléfono no es válido");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar parámetros para el procedimiento
        $params = [
            'p_usuario_id' => $usuario_id,
            'p_nombre_completo' => $nombre_completo,
            'p_correo' => $correo,
            'p_telefono' => $telefono
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_USUARIOS_PKG.FIDE_ACTUALIZAR_PERFIL_PROC', $params);
        
        showSuccess("Perfil actualizado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al actualizar perfil: " . $e->getMessage());
        showError("No se pudo actualizar el perfil: " . $e->getMessage());
        return false;
    }
}

/**
 * Cambia la contraseña del usuario
 * 
 * @param int $usuario_id ID del usuario
 * @param string $clave_actual Contraseña actual
 * @param string $clave_nueva Nueva contraseña
 * @param string $clave_confirmacion Confirmación de la nueva contraseña
 * @return bool Éxito o fracaso de la operación
 */
function cambiarClaveUsuario($usuario_id, $clave_actual, $clave_nueva, $clave_confirmacion) {
    try {
        // Validar datos
        if (empty($usuario_id) || empty($clave_actual) || empty($clave_nueva) || empty($clave_confirmacion)) {
            showError("Todos los campos son obligatorios");
            return false;
        }
        
        if ($clave_nueva !== $clave_confirmacion) {
            showError("La nueva contraseña y su confirmación no coinciden");
            return false;
        }
        
        if (strlen($clave_nueva) < 8) {
            showError("La nueva contraseña debe tener al menos 8 caracteres");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar la contraseña actual
        $query = "SELECT COUNT(*) AS VALIDO 
                  FROM FIDE_USUARIOS_TB 
                  WHERE FIDE_USUARIO_ID = :usuario_id 
                  AND FIDE_CLAVE = DBMS_OBFUSCATION_TOOLKIT.MD5(input => UTL_RAW.CAST_TO_RAW(:clave_actual))";
        
        $resultado = $db->queryValue($query, [
            'usuario_id' => $usuario_id,
            'clave_actual' => $clave_actual
        ], 'VALIDO');
        
        if ($resultado != 1) {
            showError("La contraseña actual es incorrecta");
            return false;
        }
        
        // Preparar parámetros para el procedimiento
        $params = [
            'p_usuario_id' => $usuario_id,
            'p_clave_nueva' => $clave_nueva
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_USUARIOS_PKG.FIDE_CAMBIAR_CLAVE_PROC', $params);
        
        showSuccess("Contraseña cambiada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al cambiar contraseña: " . $e->getMessage());
        showError("No se pudo cambiar la contraseña: " . $e->getMessage());
        return false;
    }
}

/**
 * Registra la última fecha de acceso del usuario
 * 
 * @param int $usuario_id ID del usuario
 * @return bool Éxito o fracaso de la operación
 */
function actualizarUltimoAcceso($usuario_id) {
    try {
        if (empty($usuario_id)) {
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Actualizar la fecha de último acceso
        $query = "UPDATE FIDE_USUARIOS_TB 
                  SET FIDE_ULTIMO_ACCESO = SYSTIMESTAMP 
                  WHERE FIDE_USUARIO_ID = :usuario_id";
        
        $db->query($query, ['usuario_id' => $usuario_id]);
        
        return true;
    } catch (Exception $e) {
        logError("Error al actualizar último acceso: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene el historial de actividades del usuario
 * 
 * @param int $usuario_id ID del usuario
 * @param int $limite Límite de registros a obtener (por defecto 50)
 * @return array Historial de actividades
 */
function obtenerHistorialActividades($usuario_id, $limite = 50) {
    try {
        if (empty($usuario_id)) {
            showError("El ID de usuario es obligatorio");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener el historial de actividades
        $query = "SELECT 
                    FIDE_ACTIVIDAD_ID,
                    FIDE_ACTIVIDAD_TIPO,
                    FIDE_ACTIVIDAD_DESCRIPCION,
                    FIDE_ACTIVIDAD_FECHA,
                    FIDE_ACTIVIDAD_IP
                FROM 
                    FIDE_ACTIVIDADES_USUARIOS_TB
                WHERE 
                    FIDE_USUARIO_ID = :usuario_id
                ORDER BY 
                    FIDE_ACTIVIDAD_FECHA DESC
                FETCH FIRST :limite ROWS ONLY";
        
        // Ejecutar la consulta
        $result = $db->query($query, [
            'usuario_id' => $usuario_id,
            'limite' => $limite
        ]);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener historial de actividades: " . $e->getMessage());
        showError("No se pudo obtener el historial de actividades: " . $e->getMessage());
        return [];
    }
}

/**
 * Registra una actividad del usuario
 * 
 * @param int $usuario_id ID del usuario
 * @param string $tipo Tipo de actividad
 * @param string $descripcion Descripción de la actividad
 * @return bool Éxito o fracaso de la operación
 */
function registrarActividad($usuario_id, $tipo, $descripcion) {
    try {
        if (empty($usuario_id) || empty($tipo) || empty($descripcion)) {
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Obtener la IP del usuario
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        
        // Preparar parámetros para el procedimiento
        $params = [
            'p_usuario_id' => $usuario_id,
            'p_tipo' => $tipo,
            'p_descripcion' => $descripcion,
            'p_ip' => $ip
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_USUARIOS_PKG.FIDE_REGISTRAR_ACTIVIDAD_PROC', $params);
        
        return true;
    } catch (Exception $e) {
        logError("Error al registrar actividad: " . $e->getMessage());
        return false;
    }
}