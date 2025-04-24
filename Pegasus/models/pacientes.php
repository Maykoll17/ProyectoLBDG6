<?php
/**
 * Modelo para la gestión de pacientes
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con los paquetes FIDE_PACIENTES_PKG, FIDE_PACIENTES_DEUDA_PKG y FIDE_PACIENTES_HOSPITALIZADOS_PKG
 */

/**
 * Registra un nuevo paciente en el sistema
 * 
 * @param string $cedula Número de identificación del paciente
 * @param string $nombre Nombre del paciente
 * @param string $apellidos Apellidos del paciente
 * @param string $telefono Teléfono del paciente
 * @param string $direccion Dirección del paciente
 * @param string $correo Correo electrónico del paciente
 * @param int $estado_id ID del estado del paciente
 * @param float $deuda Deuda inicial del paciente (por defecto 0)
 * @return bool Éxito o fracaso de la operación
 */
function registrarPaciente($cedula, $nombre, $apellidos, $telefono, $direccion, $correo, $estado_id, $deuda = 0) {
    try {
        // Validar datos
        if (empty($cedula) || empty($nombre) || empty($apellidos)) {
            showError("Los campos cédula, nombre y apellidos son obligatorios");
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
            'p_cedula' => $cedula,
            'p_nombre' => $nombre,
            'p_apellidos' => $apellidos,
            'p_telefono' => $telefono,
            'p_direccion' => $direccion,
            'p_correo' => $correo,
            'p_estado_id' => $estado_id,
            'p_deuda' => $deuda
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('fide_pacientes_pkg.fide_registrar_paciente_proc', $params);
        
        showSuccess("Paciente registrado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al registrar paciente: " . $e->getMessage());
        showError("No se pudo registrar el paciente: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualiza los datos de un paciente existente
 * 
 * @param string $cedula Número de identificación del paciente
 * @param string $nombre Nombre del paciente
 * @param string $apellidos Apellidos del paciente
 * @param string $telefono Teléfono del paciente
 * @param string $direccion Dirección del paciente
 * @param string $correo Correo electrónico del paciente
 * @param int $estado_id ID del estado del paciente
 * @param float $deuda Deuda inicial del paciente
 * @return bool Éxito o fracaso de la operación
 */
function actualizarPaciente($cedula, $nombre, $apellidos, $telefono, $direccion, $correo, $estado_id, $deuda) {
    try {
        // Validar datos
        if (empty($cedula) || empty($nombre) || empty($apellidos)) {
            showError("Los campos cédula, nombre y apellidos son obligatorios");
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
            'p_cedula' => $cedula,
            'p_nombre' => $nombre,
            'p_apellidos' => $apellidos,
            'p_telefono' => $telefono,
            'p_direccion' => $direccion,
            'p_correo' => $correo,
            'p_estado_id' => $estado_id,
            'p_deuda' => $deuda
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('fide_pacientes_pkg.fide_actualizar_paciente_proc', $params);
        
        showSuccess("Paciente actualizado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al actualizar paciente: " . $e->getMessage());
        showError("No se pudo actualizar el paciente: " . $e->getMessage());
        return false;
    }
}

/**
 * Busca un paciente por su número de cédula
 * 
 * @param string $cedula Número de identificación del paciente
 * @return array|null Datos del paciente o null si no se encuentra
 */
function buscarPaciente($cedula) {
    try {
        if (empty($cedula)) {
            showError("El número de cédula es obligatorio");
            return null;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener los datos del paciente
        $query = "SELECT 
                    p.FIDE_PACIENTE_CEDULA, 
                    p.FIDE_NOMBRE_PACIENTE, 
                    p.FIDE_APELLIDOS_PACIENTE, 
                    p.FIDE_TELEFONO_PACIENTE, 
                    p.FIDE_DIRECCION_PACIENTE, 
                    p.FIDE_CORREO_PACIENTE, 
                    p.FIDE_ESTADO_PACIENTE_ID, 
                    p.FIDE_DEUDA_PACIENTE,
                    e.FIDE_DESCRIPCION_ESTADO_PACIENTE
                FROM 
                    FIDE_PACIENTES_TB p
                JOIN 
                    FIDE_ESTADOS_PACIENTES_TB e ON p.FIDE_ESTADO_PACIENTE_ID = e.FIDE_ESTADO_PACIENTE_ID
                WHERE 
                    p.FIDE_PACIENTE_CEDULA = :cedula";
        
        // Ejecutar la consulta
        $result = $db->queryOne($query, ['cedula' => $cedula]);
        
        if (!$result) {
            showInfo("No se encontró ningún paciente con la cédula: $cedula");
            return null;
        }
        
        return $result;
    } catch (Exception $e) {
        logError("Error al buscar paciente: " . $e->getMessage());
        showError("No se pudo buscar el paciente: " . $e->getMessage());
        return null;
    }
}

/**
 * Elimina un paciente del sistema
 * 
 * @param string $cedula Número de identificación del paciente
 * @return bool Éxito o fracaso de la operación
 */
function eliminarPaciente($cedula) {
    try {
        if (empty($cedula)) {
            showError("El número de cédula es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('fide_pacientes_pkg.fide_eliminar_paciente_proc', ['p_cedula' => $cedula]);
        
        showSuccess("Paciente eliminado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al eliminar paciente: " . $e->getMessage());
        showError("No se pudo eliminar el paciente: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene el historial médico de un paciente
 * 
 * @param string $cedula Número de identificación del paciente
 * @return array Historial médico del paciente
 */
function obtenerHistorialMedico($cedula) {
    try {
        if (empty($cedula)) {
            showError("El número de cédula es obligatorio");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener el historial médico
        $query = "SELECT 
                    hm.FIDE_HISTORIAL_ID,
                    hm.FIDE_FECHA_REGISTRO,
                    hm.FIDE_DIAGNOSTICO,
                    hm.FIDE_TRATAMIENTO,
                    hm.FIDE_OBSERVACIONES,
                    e.FIDE_NOMBRE_EMPLEADO,
                    e.FIDE_APELLIDOS_EMPLEADO
                FROM 
                    FIDE_HISTORIAL_MEDICO_TB hm
                JOIN 
                    FIDE_EMPLEADOS_TB e ON hm.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
                WHERE 
                    hm.FIDE_PACIENTE_CEDULA = :cedula
                ORDER BY 
                    hm.FIDE_FECHA_REGISTRO DESC";
        
        // Ejecutar la consulta
        $result = $db->query($query, ['cedula' => $cedula]);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener historial médico: " . $e->getMessage());
        showError("No se pudo obtener el historial médico: " . $e->getMessage());
        return [];
    }
}

/**
 * Registra una nueva entrada en el historial médico de un paciente
 * 
 * @param string $cedula_paciente Número de identificación del paciente
 * @param string $cedula_empleado Número de identificación del empleado (médico)
 * @param string $diagnostico Diagnóstico médico
 * @param string $tratamiento Tratamiento prescrito
 * @param string $observaciones Observaciones adicionales
 * @return bool Éxito o fracaso de la operación
 */
function registrarHistorialMedico($cedula_paciente, $cedula_empleado, $diagnostico, $tratamiento, $observaciones) {
    try {
        // Validar datos
        if (empty($cedula_paciente) || empty($cedula_empleado) || empty($diagnostico)) {
            showError("Los campos cédula del paciente, cédula del empleado y diagnóstico son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar parámetros para el procedimiento
        $params = [
            'p_cedula_paciente' => $cedula_paciente,
            'p_cedula_empleado' => $cedula_empleado,
            'p_diagnostico' => $diagnostico,
            'p_tratamiento' => $tratamiento,
            'p_observaciones' => $observaciones
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('fide_pacientes_pkg.fide_registrar_historial_medico_proc', $params);
        
        showSuccess("Historial médico registrado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al registrar historial médico: " . $e->getMessage());
        showError("No se pudo registrar el historial médico: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene la lista de pacientes con deuda
 * 
 * @return array Lista de pacientes con deuda
 */
function obtenerPacientesConDeuda() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener los pacientes con deuda
        $query = "SELECT 
                    p.FIDE_PACIENTE_CEDULA, 
                    p.FIDE_NOMBRE_PACIENTE,
                    p.FIDE_APELLIDOS_PACIENTE,
                    p.FIDE_TELEFONO_PACIENTE,
                    p.FIDE_CORREO_PACIENTE,
                    ep.FIDE_DESCRIPCION_ESTADO_PACIENTE, 
                    p.FIDE_DEUDA_PACIENTE,
                    (SELECT COUNT(*) FROM FIDE_FACTURAS_TB f WHERE f.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA AND f.FIDE_ESTADO_FACTURA = 'PENDIENTE') AS facturas_pendientes
                FROM 
                    FIDE_PACIENTES_TB p
                JOIN 
                    FIDE_ESTADOS_PACIENTES_TB ep ON p.FIDE_ESTADO_PACIENTE_ID = ep.FIDE_ESTADO_PACIENTE_ID
                WHERE 
                    p.FIDE_DEUDA_PACIENTE > 0
                ORDER BY 
                    p.FIDE_DEUDA_PACIENTE DESC";
        
        // Ejecutar la consulta
        $result = $db->query($query);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener pacientes con deuda: " . $e->getMessage());
        showError("No se pudo obtener la lista de pacientes con deuda: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de pacientes hospitalizados
 * 
 * @return array Lista de pacientes hospitalizados
 */
function obtenerPacientesHospitalizados() {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection(); // ← conexión OCI

        $stmt = oci_parse($conn, "BEGIN FIDE_PACIENTES_HOSPITALIZADOS_PKG.FIDE_LISTAR_PACIENTES_HOSPITALIZADOS_PROC(:cursor); END;");
        $cursor = oci_new_cursor($conn);
        oci_bind_by_name($stmt, ":cursor", $cursor, -1, OCI_B_CURSOR);

        oci_execute($stmt);      // Ejecuta el bloque PL/SQL
        oci_execute($cursor);    // Ejecuta el cursor para lectura

        $result = [];
        while (($row = oci_fetch_assoc($cursor)) != false) {
            $result[] = $row;
        }

        oci_free_statement($stmt);
        oci_free_statement($cursor);

        return $result;
    } catch (Exception $e) {
        logError("Error al obtener pacientes hospitalizados: " . $e->getMessage());
        showError("No se pudo obtener la lista de pacientes hospitalizados.");
        return [];
    }
}

/**
 * Obtiene la lista de empleados activos
 * 
 * @return array Lista de empleados activos
 */
function obtenerEmpleadosActivos() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Crear un cursor para recibir los resultados
        $cursor = oci_new_cursor($conn);
        
        // Preparar y ejecutar el procedimiento almacenado
        $sql = "BEGIN FIDE_EMPLEADOS_ACTIVOS_PKG.FIDE_LISTAR_EMPLEADOS_ACTIVOS_PROC(:p_cursor); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_cursor", $cursor, -1, OCI_B_CURSOR);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_execute($cursor);
        
        // Recopilar los resultados
        $empleados = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $empleados[] = $row;
        }
        
        // Liberar recursos
        oci_free_statement($cursor);
        oci_free_statement($stmt);
        
        return $empleados;
    } catch (Exception $e) {
        logError("Error al obtener empleados activos: " . $e->getMessage());
        showError("No se pudo obtener la lista de empleados activos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene el historial de hospitalizaciones de un paciente
 * 
 * @param string $cedula Número de identificación del paciente
 * @return array Historial de hospitalizaciones
 */
function obtenerHistorialHospitalizaciones($cedula) {
    try {
        if (empty($cedula)) {
            showError("El número de cédula es obligatorio");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener el historial de hospitalizaciones
        $query = "SELECT 
                    h.FIDE_HOSPITALIZACION_ID, 
                    h.FIDE_SALA_ID, 
                    ts.FIDE_DESCRIPCION_TIPO_SALA, 
                    h.FIDE_EMPLEADO_CEDULA, 
                    e.FIDE_NOMBRE_EMPLEADO, 
                    e.FIDE_APELLIDOS_EMPLEADO, 
                    h.FIDE_FECHA_INGRESO, 
                    h.FIDE_FECHA_ALTA, 
                    h.FIDE_MOTIVO_INGRESO, 
                    h.FIDE_DIAGNOSTICO_INGRESO, 
                    h.FIDE_ESTADO 
                FROM 
                    FIDE_HOSPITALIZACIONES_TB h 
                JOIN 
                    FIDE_EMPLEADOS_TB e ON h.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA 
                JOIN 
                    FIDE_SALAS_TB s ON h.FIDE_SALA_ID = s.FIDE_SALA_ID 
                JOIN 
                    FIDE_TIPOS_SALAS_TB ts ON s.FIDE_TIPO_SALA_ID = ts.FIDE_TIPO_SALA_ID 
                WHERE 
                    h.FIDE_PACIENTE_CEDULA = :cedula 
                ORDER BY 
                    h.FIDE_FECHA_INGRESO DESC";
        
        // Ejecutar la consulta
        $result = $db->query($query, ['cedula' => $cedula]);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener historial de hospitalizaciones: " . $e->getMessage());
        showError("No se pudo obtener el historial de hospitalizaciones: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de todos los pacientes
 * 
 * @return array Lista de pacientes
 */
function obtenerTodosPacientes() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener todos los pacientes
        $query = "SELECT 
                    p.FIDE_PACIENTE_CEDULA, 
                    p.FIDE_NOMBRE_PACIENTE, 
                    p.FIDE_APELLIDOS_PACIENTE, 
                    p.FIDE_TELEFONO_PACIENTE, 
                    p.FIDE_DIRECCION_PACIENTE, 
                    p.FIDE_CORREO_PACIENTE, 
                    ep.FIDE_DESCRIPCION_ESTADO_PACIENTE, 
                    p.FIDE_DEUDA_PACIENTE
                FROM 
                    FIDE_PACIENTES_TB p
                JOIN 
                    FIDE_ESTADOS_PACIENTES_TB ep ON p.FIDE_ESTADO_PACIENTE_ID = ep.FIDE_ESTADO_PACIENTE_ID
                ORDER BY 
                    p.FIDE_APELLIDOS_PACIENTE, p.FIDE_NOMBRE_PACIENTE";
        
        // Ejecutar la consulta
        $result = $db->query($query);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener todos los pacientes: " . $e->getMessage());
        showError("No se pudo obtener la lista de pacientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de estados de pacientes
 * 
 * @return array Lista de estados de pacientes
 */
function obtenerEstadosPacientes() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener los estados de pacientes
        $query = "SELECT 
                    FIDE_ESTADO_PACIENTE_ID, 
                    FIDE_DESCRIPCION_ESTADO_PACIENTE
                FROM 
                    FIDE_ESTADOS_PACIENTES_TB
                ORDER BY 
                    FIDE_DESCRIPCION_ESTADO_PACIENTE";
        
        // Ejecutar la consulta
        $result = $db->query($query);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener estados de pacientes: " . $e->getMessage());
        showError("No se pudo obtener la lista de estados de pacientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Registra una nueva hospitalización
 * 
 * @param string $cedula_paciente Cédula del paciente
 * @param int $sala_id ID de la sala
 * @param string $cedula_medico Cédula del médico responsable
 * @param string $motivo Motivo de la hospitalización
 * @param string $diagnostico Diagnóstico inicial
 * @return bool Éxito o fracaso de la operación
 */
function registrarHospitalizacion($cedula_paciente, $sala_id, $cedula_medico, $motivo, $diagnostico) {
    try {
        // Validar datos
        if (empty($cedula_paciente) || empty($cedula_medico) || empty($motivo) || $sala_id <= 0) {
            showError("Los campos cédula del paciente, cédula del médico, sala y motivo son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar la llamada al procedimiento
        $sql = "BEGIN FIDE_PACIENTES_HOSPITALIZADOS_PKG.FIDE_REGISTRAR_HOSPITALIZACION_PROC(:p_cedula_paciente, :p_sala_id, :p_cedula_medico, :p_motivo, :p_diagnostico); END;";
        $stmt = oci_parse($db->getConnection(), $sql);
        
        // Asociar los parámetros
        oci_bind_by_name($stmt, ":p_cedula_paciente", $cedula_paciente);
        oci_bind_by_name($stmt, ":p_sala_id", $sala_id);
        oci_bind_by_name($stmt, ":p_cedula_medico", $cedula_medico);
        oci_bind_by_name($stmt, ":p_motivo", $motivo);
        oci_bind_by_name($stmt, ":p_diagnostico", $diagnostico);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        
        // Liberar recursos
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Hospitalización registrada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al registrar hospitalización: " . $e->getMessage());
        showError("No se pudo registrar la hospitalización: " . $e->getMessage());
        return false;
    }
}