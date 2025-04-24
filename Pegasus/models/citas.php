<?php
/**
 * Modelo para la gestión de citas
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con los paquetes FIDE_CITAS_PKG y FIDE_HISTORIAL_CITAS_PKG
 */

/**
 * Agenda una nueva cita médica
 * 
 * @param string $paciente_cedula Cédula del paciente
 * @param string $empleado_cedula Cédula del empleado (médico)
 * @param string $fecha_cita Fecha y hora de la cita
 * @param int $sala_id ID de la sala
 * @param string $motivo_cita Motivo de la cita
 * @return bool Éxito o fracaso de la operación
 */
function agendarCita($paciente_cedula, $empleado_cedula, $fecha_cita, $sala_id, $motivo_cita) {
    try {
        // Validar datos
        if (empty($paciente_cedula) || empty($empleado_cedula) || empty($fecha_cita) || empty($sala_id)) {
            showError("Los campos paciente, médico, fecha y sala son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento almacenado
        $sql = "BEGIN FIDE_CITAS_PKG.FIDE_AGENDAR_CITA_PROC(:p_paciente_cedula, :p_empleado_cedula, 
                TO_TIMESTAMP(:p_fecha_cita, 'YYYY-MM-DD HH24:MI:SS'), :p_sala_id, :p_motivo_cita); END;";
        
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_paciente_cedula", $paciente_cedula);
        oci_bind_by_name($stmt, ":p_empleado_cedula", $empleado_cedula);
        oci_bind_by_name($stmt, ":p_fecha_cita", $fecha_cita);
        oci_bind_by_name($stmt, ":p_sala_id", $sala_id);
        oci_bind_by_name($stmt, ":p_motivo_cita", $motivo_cita);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Cita agendada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al agendar cita: " . $e->getMessage());
        showError("No se pudo agendar la cita: " . $e->getMessage());
        return false;
    }
}

/**
 * Cancela una cita programada
 * 
 * @param int $cita_id ID de la cita a cancelar
 * @return bool Éxito o fracaso de la operación
 */
function cancelarCita($cita_id) {
    try {
        if (empty($cita_id)) {
            showError("El ID de la cita es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Actualizar el estado de la cita primero
        $sql_update = "UPDATE FIDE_CITAS_TB SET FIDE_ESTADO_CITA = 'CANCELADA' WHERE FIDE_CITA_ID = :cita_id";
        $stmt_update = oci_parse($conn, $sql_update);
        oci_bind_by_name($stmt_update, ":cita_id", $cita_id);
        oci_execute($stmt_update);
        oci_free_statement($stmt_update);
        
        // Llamar al procedimiento almacenado para cancelar la cita
        $sql = "BEGIN FIDE_CITAS_PKG.FIDE_CANCELAR_CITA_PROC(:p_cita_id); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetro
        oci_bind_by_name($stmt, ":p_cita_id", $cita_id);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Cita cancelada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al cancelar cita: " . $e->getMessage());
        showError("No se pudo cancelar la cita: " . $e->getMessage());
        return false;
    }
}

/**
 * Reprograma una cita a una nueva fecha y hora
 * 
 * @param int $cita_id ID de la cita a reprogramar
 * @param string $nueva_fecha Nueva fecha y hora de la cita
 * @return bool Éxito o fracaso de la operación
 */
function reprogramarCita($cita_id, $nueva_fecha) {
    try {
        if (empty($cita_id) || empty($nueva_fecha)) {
            showError("El ID de la cita y la nueva fecha son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento almacenado
        $sql = "BEGIN FIDE_CITAS_PKG.FIDE_REPROGRAMAR_CITA_PROC(:p_cita_id, 
                TO_TIMESTAMP(:p_nueva_fecha, 'YYYY-MM-DD HH24:MI:SS')); END;";
        
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_cita_id", $cita_id);
        oci_bind_by_name($stmt, ":p_nueva_fecha", $nueva_fecha);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Cita reprogramada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al reprogramar cita: " . $e->getMessage());
        showError("No se pudo reprogramar la cita: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene las citas de un paciente específico
 * 
 * @param string $paciente_cedula Cédula del paciente
 * @return array Lista de citas del paciente
 */
function obtenerCitasPaciente($paciente_cedula) {
    try {
        if (empty($paciente_cedula)) {
            showError("La cédula del paciente es obligatoria");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Ejecutar el procedimiento almacenado para listar citas del paciente
        $sql = "BEGIN FIDE_CITAS_PKG.FIDE_LISTAR_CITAS_PACIENTE_PROC(:p_paciente_cedula); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetro
        oci_bind_by_name($stmt, ":p_paciente_cedula", $paciente_cedula);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_free_statement($stmt);
        
        // Para obtener los resultados, necesitamos hacer una consulta adicional
        $query = "SELECT 
                    c.FIDE_CITA_ID, 
                    c.FIDE_FECHA_CITA, 
                    c.FIDE_MOTIVO_CITA,
                    c.FIDE_ESTADO_CITA,
                    e.FIDE_NOMBRE_EMPLEADO,
                    e.FIDE_APELLIDOS_EMPLEADO,
                    s.FIDE_SALA_ID,
                    ts.FIDE_DESCRIPCION_TIPO_SALA
                FROM 
                    FIDE_CITAS_TB c
                JOIN 
                    FIDE_EMPLEADOS_TB e ON c.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
                JOIN 
                    FIDE_SALAS_TB s ON c.FIDE_SALA_ID = s.FIDE_SALA_ID
                JOIN 
                    FIDE_TIPOS_SALAS_TB ts ON s.FIDE_TIPO_SALA_ID = ts.FIDE_TIPO_SALA_ID
                WHERE 
                    c.FIDE_PACIENTE_CEDULA = :cedula
                ORDER BY 
                    c.FIDE_FECHA_CITA";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":cedula", $paciente_cedula);
        oci_execute($stmt);
        
        $citas = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $citas[] = $row;
        }
        
        oci_free_statement($stmt);
        
        return $citas;
    } catch (Exception $e) {
        logError("Error al obtener citas del paciente: " . $e->getMessage());
        showError("No se pudieron obtener las citas del paciente: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene las citas de un empleado (médico) específico
 * 
 * @param string $empleado_cedula Cédula del empleado (médico)
 * @return array Lista de citas del empleado
 */
function obtenerCitasEmpleado($empleado_cedula) {
    try {
        if (empty($empleado_cedula)) {
            showError("La cédula del empleado es obligatoria");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Ejecutar el procedimiento almacenado para listar citas del empleado
        $sql = "BEGIN FIDE_CITAS_PKG.FIDE_LISTAR_CITAS_EMPLEADO_PROC(:p_empleado_cedula); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetro
        oci_bind_by_name($stmt, ":p_empleado_cedula", $empleado_cedula);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_free_statement($stmt);
        
        // Para obtener los resultados, necesitamos hacer una consulta adicional
        $query = "SELECT 
                    c.FIDE_CITA_ID, 
                    c.FIDE_FECHA_CITA, 
                    c.FIDE_MOTIVO_CITA,
                    c.FIDE_ESTADO_CITA,
                    p.FIDE_PACIENTE_CEDULA,
                    p.FIDE_NOMBRE_PACIENTE,
                    p.FIDE_APELLIDOS_PACIENTE,
                    s.FIDE_SALA_ID,
                    ts.FIDE_DESCRIPCION_TIPO_SALA
                FROM 
                    FIDE_CITAS_TB c
                JOIN 
                    FIDE_PACIENTES_TB p ON c.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
                JOIN 
                    FIDE_SALAS_TB s ON c.FIDE_SALA_ID = s.FIDE_SALA_ID
                JOIN 
                    FIDE_TIPOS_SALAS_TB ts ON s.FIDE_TIPO_SALA_ID = ts.FIDE_TIPO_SALA_ID
                WHERE 
                    c.FIDE_EMPLEADO_CEDULA = :cedula
                ORDER BY 
                    c.FIDE_FECHA_CITA";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":cedula", $empleado_cedula);
        oci_execute($stmt);
        
        $citas = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $citas[] = $row;
        }
        
        oci_free_statement($stmt);
        
        return $citas;
    } catch (Exception $e) {
        logError("Error al obtener citas del empleado: " . $e->getMessage());
        showError("No se pudieron obtener las citas del empleado: " . $e->getMessage());
        return [];
    }
}

/**
 * Verifica la disponibilidad de una sala para una fecha y hora específica
 * 
 * @param int $sala_id ID de la sala
 * @param string $fecha_cita Fecha y hora de la cita
 * @return bool True si está disponible, false si no
 */
function verificarDisponibilidadSala($sala_id, $fecha_cita) {
    try {
        if (empty($sala_id) || empty($fecha_cita)) {
            showError("El ID de la sala y la fecha son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento para verificar disponibilidad
        $sql = "BEGIN FIDE_CITAS_PKG.FIDE_VERIFICAR_DISPONIBILIDAD_CITA_PROC(:p_sala_id, 
                TO_TIMESTAMP(:p_fecha_cita, 'YYYY-MM-DD HH24:MI:SS')); END;";
        
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_sala_id", $sala_id);
        oci_bind_by_name($stmt, ":p_fecha_cita", $fecha_cita);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_free_statement($stmt);
        
        // Este procedimiento imprime en la salida si la sala está disponible
        // Para determinar realmente si está disponible, consultamos si hay citas
        $query = "SELECT COUNT(*) AS total
                FROM FIDE_CITAS_TB 
                WHERE FIDE_SALA_ID = :sala_id 
                AND FIDE_FECHA_CITA = TO_TIMESTAMP(:fecha_cita, 'YYYY-MM-DD HH24:MI:SS')
                AND FIDE_ESTADO_CITA != 'CANCELADA'";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":sala_id", $sala_id);
        oci_bind_by_name($stmt, ":fecha_cita", $fecha_cita);
        oci_execute($stmt);
        
        $row = oci_fetch_assoc($stmt);
        $count = $row['TOTAL'];
        
        oci_free_statement($stmt);
        
        return ($count == 0); // Retorna true si no hay citas (está disponible)
    } catch (Exception $e) {
        logError("Error al verificar disponibilidad: " . $e->getMessage());
        showError("No se pudo verificar la disponibilidad: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene el historial de citas de un paciente
 * 
 * @param string $cedula_paciente Cédula del paciente
 * @return array Historial de citas del paciente
 */
function obtenerHistorialCitasPaciente($cedula_paciente) {
    try {
        if (empty($cedula_paciente)) {
            showError("La cédula del paciente es obligatoria");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Crear un cursor para recibir los resultados
        $cursor = oci_new_cursor($conn);
        
        // Preparar y ejecutar el procedimiento almacenado
        $sql = "BEGIN FIDE_HISTORIAL_CITAS_PKG.FIDE_LISTAR_HISTORIAL_CITAS_PACIENTE_PROC(:p_cedula_paciente, :p_cursor); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_cedula_paciente", $cedula_paciente);
        oci_bind_by_name($stmt, ":p_cursor", $cursor, -1, OCI_B_CURSOR);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_execute($cursor);
        
        // Recopilar los resultados
        $historial = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $historial[] = $row;
        }
        
        // Liberar recursos
        oci_free_statement($cursor);
        oci_free_statement($stmt);
        
        return $historial;
    } catch (Exception $e) {
        logError("Error al obtener historial de citas: " . $e->getMessage());
        showError("No se pudo obtener el historial de citas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene el historial completo de citas
 * 
 * @return array Historial completo de citas
 */
function obtenerHistorialCitas() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Preparar y ejecutar el procedimiento almacenado
        $sql = "BEGIN FIDE_HISTORIAL_CITAS_PKG.FIDE_LISTAR_HISTORIAL_CITAS_PROC; END;";
        $stmt = oci_parse($conn, $sql);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_free_statement($stmt);
        
        // Ahora que el procedimiento se ha ejecutado, consultamos la vista o tabla donde se guardaron los resultados
        $query = "SELECT 
                    c.FIDE_CITA_ID, 
                    c.FIDE_PACIENTE_CEDULA, 
                    p.FIDE_NOMBRE_PACIENTE,
                    p.FIDE_APELLIDOS_PACIENTE,
                    c.FIDE_EMPLEADO_CEDULA, 
                    e.FIDE_NOMBRE_EMPLEADO,
                    e.FIDE_APELLIDOS_EMPLEADO,
                    c.FIDE_FECHA_CITA, 
                    c.FIDE_MOTIVO_CITA,
                    c.FIDE_ESTADO_CITA,
                    c.FIDE_SALA_ID,
                    ts.FIDE_DESCRIPCION_TIPO_SALA,
                    hm.FIDE_DIAGNOSTICO,
                    hm.FIDE_TRATAMIENTO
                FROM 
                    FIDE_CITAS_TB c
                JOIN 
                    FIDE_PACIENTES_TB p ON c.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
                JOIN 
                    FIDE_EMPLEADOS_TB e ON c.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
                JOIN
                    FIDE_SALAS_TB s ON c.FIDE_SALA_ID = s.FIDE_SALA_ID
                JOIN
                    FIDE_TIPOS_SALAS_TB ts ON s.FIDE_TIPO_SALA_ID = ts.FIDE_TIPO_SALA_ID
                LEFT JOIN
                    FIDE_HISTORIAL_MEDICO_TB hm ON c.FIDE_PACIENTE_CEDULA = hm.FIDE_PACIENTE_CEDULA
                ORDER BY 
                    c.FIDE_FECHA_CITA DESC";
        
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);
        
        $citas = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $citas[] = $row;
        }
        
        oci_free_statement($stmt);
        
        return $citas;
    } catch (Exception $e) {
        logError("Error al obtener historial de citas: " . $e->getMessage());
        showError("No se pudo obtener el historial de citas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene todas las citas programadas para la fecha actual
 * 
 * @return array Lista de citas del día
 */
function obtenerCitasHoy() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Consulta para obtener las citas del día actual
        $query = "SELECT 
                    c.FIDE_CITA_ID, 
                    c.FIDE_PACIENTE_CEDULA, 
                    c.FIDE_EMPLEADO_CEDULA, 
                    c.FIDE_FECHA_CITA, 
                    c.FIDE_MOTIVO_CITA,
                    c.FIDE_ESTADO_CITA,
                    c.FIDE_SALA_ID,
                    p.FIDE_NOMBRE_PACIENTE,
                    p.FIDE_APELLIDOS_PACIENTE,
                    e.FIDE_NOMBRE_EMPLEADO,
                    e.FIDE_APELLIDOS_EMPLEADO,
                    ts.FIDE_DESCRIPCION_TIPO_SALA
                FROM 
                    FIDE_CITAS_TB c
                JOIN 
                    FIDE_PACIENTES_TB p ON c.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
                JOIN 
                    FIDE_EMPLEADOS_TB e ON c.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
                JOIN
                    FIDE_SALAS_TB s ON c.FIDE_SALA_ID = s.FIDE_SALA_ID
                JOIN
                    FIDE_TIPOS_SALAS_TB ts ON s.FIDE_TIPO_SALA_ID = ts.FIDE_TIPO_SALA_ID
                WHERE 
                    TRUNC(c.FIDE_FECHA_CITA) = TRUNC(SYSDATE)
                    AND c.FIDE_ESTADO_CITA != 'CANCELADA'
                ORDER BY 
                    c.FIDE_FECHA_CITA";
        
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);
        
        $citas = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $citas[] = $row;
        }
        
        oci_free_statement($stmt);
        
        return $citas;
    } catch (Exception $e) {
        logError("Error al obtener citas de hoy: " . $e->getMessage());
        showError("No se pudieron obtener las citas de hoy: " . $e->getMessage());
        return [];
    }
}