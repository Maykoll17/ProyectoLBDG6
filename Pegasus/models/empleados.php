<?php
/**
 * Modelo para la gestión de empleados
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con el paquete FIDE_EMPLEADOS_ACTIVOS_PKG
 */

/**
 * Obtiene todos los médicos activos
 * 
 * @return array Lista de médicos activos
 */
function obtenerMedicosActivos() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Crear un cursor para recibir los resultados
        $cursor = oci_new_cursor($db->getConnection());
        
        // Preparar la llamada al procedimiento
        $sql = "BEGIN FIDE_EMPLEADOS_ACTIVOS_PKG.FIDE_LISTAR_EMPLEADOS_ACTIVOS_PROC(:cursor); END;";
        $stmt = oci_parse($db->getConnection(), $sql);
        
        // Asociar el cursor como parámetro de salida
        oci_bind_by_name($stmt, ":cursor", $cursor, -1, OCI_B_CURSOR);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_execute($cursor);
        
        // Obtener los resultados y filtrar solo médicos
        $medicos = [];
        while ($empleado = oci_fetch_assoc($cursor)) {
            // Asumiendo que hay un campo que identifica si es médico (por ejemplo FIDE_DESCRIPCION_TIPO_EMPLEADO)
            if (isset($empleado['FIDE_DESCRIPCION_TIPO_EMPLEADO']) && 
                (strpos(strtoupper($empleado['FIDE_DESCRIPCION_TIPO_EMPLEADO']), 'MEDICO') !== false || 
                 strpos(strtoupper($empleado['FIDE_DESCRIPCION_TIPO_EMPLEADO']), 'DOCTOR') !== false ||
                 strpos(strtoupper($empleado['FIDE_DESCRIPCION_TIPO_EMPLEADO']), 'ESPECIALISTA') !== false)) {
                // Agregar campo de especialidad para que sea accesible como $medico['FIDE_ESPECIALIDAD']
                $empleado['FIDE_ESPECIALIDAD'] = $empleado['FIDE_DESCRIPCION_TIPO_EMPLEADO'];
                $medicos[] = $empleado;
            }
        }
        
        // Liberar recursos
        oci_free_statement($cursor);
        oci_free_statement($stmt);
        
        return $medicos;
    } catch (Exception $e) {
        logError("Error al obtener médicos activos: " . $e->getMessage());
        showError("No se pudo obtener la lista de médicos activos: " . $e->getMessage());
        return [];
    }
}