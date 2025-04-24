<?php
/**
 * Modelo para la gestión de salas
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con los paquetes FIDE_SALAS_PKG y FIDE_SALAS_DISPONIBLES_PKG
 */

/**
 * Obtiene todas las salas disponibles
 * 
 * @return array Lista de salas disponibles
 */
function obtenerSalasDisponibles() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Crear un cursor para recibir los resultados
        $cursor = oci_new_cursor($db->getConnection());
        
        // Preparar la llamada al procedimiento
        $sql = "BEGIN FIDE_SALAS_DISPONIBLES_PKG.FIDE_LISTAR_SALAS_DISPONIBLES_PROC(:cursor); END;";
        $stmt = oci_parse($db->getConnection(), $sql);
        
        // Asociar el cursor como parámetro de salida
        oci_bind_by_name($stmt, ":cursor", $cursor, -1, OCI_B_CURSOR);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_execute($cursor);
        
        // Obtener los resultados
        $salas = [];
        while ($sala = oci_fetch_assoc($cursor)) {
            $salas[] = $sala;
        }
        
        // Liberar recursos
        oci_free_statement($cursor);
        oci_free_statement($stmt);
        
        return $salas;
    } catch (Exception $e) {
        logError("Error al obtener salas disponibles: " . $e->getMessage());
        showError("No se pudo obtener la lista de salas disponibles: " . $e->getMessage());
        return [];
    }
}