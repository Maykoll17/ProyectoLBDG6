<?php
/**
 * Modelo para la gestión de facturación
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con los paquetes FIDE_FACTURACION_PKG y FIDE_ESTADO_FACTURACION_PKG
 */

/**
 * Genera una nueva factura para un paciente
 * 
 * @param string $paciente_cedula Cédula del paciente
 * @return bool Éxito o fracaso de la operación
 */
function generarFactura($paciente_cedula) {
    try {
        if (empty($paciente_cedula)) {
            showError("La cédula del paciente es obligatoria");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento almacenado
        $sql = "BEGIN FIDE_FACTURACION_PKG.FIDE_GENERAR_FACTURA_PROC(:p_paciente_cedula); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_paciente_cedula", $paciente_cedula);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Factura generada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al generar factura: " . $e->getMessage());
        showError("No se pudo generar la factura: " . $e->getMessage());
        return false;
    }
}

/**
 * Registra el pago de una factura
 * 
 * @param int $factura_id ID de la factura
 * @param string $metodo_pago Método de pago utilizado
 * @param float $monto_pagado Monto pagado
 * @param string $referencia_pago Referencia del pago (opcional)
 * @return bool Éxito o fracaso de la operación
 */
function pagarFactura($factura_id, $metodo_pago, $monto_pagado, $referencia_pago = null) {
    try {
        if (empty($factura_id) || empty($metodo_pago) || empty($monto_pagado)) {
            showError("El ID de factura, método de pago y monto son obligatorios");
            return false;
        }
        
        // Validar que el monto sea un número positivo
        if (!is_numeric($monto_pagado) || $monto_pagado <= 0) {
            showError("El monto debe ser un número positivo");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento almacenado
        $sql = "BEGIN FIDE_FACTURACION_PKG.FIDE_PAGAR_FACTURA_PROC(:p_factura_id, :p_metodo_pago, :p_monto_pagado, :p_referencia_pago); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_factura_id", $factura_id);
        oci_bind_by_name($stmt, ":p_metodo_pago", $metodo_pago);
        oci_bind_by_name($stmt, ":p_monto_pagado", $monto_pagado);
        oci_bind_by_name($stmt, ":p_referencia_pago", $referencia_pago);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Pago registrado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al registrar pago: " . $e->getMessage());
        showError("No se pudo registrar el pago: " . $e->getMessage());
        return false;
    }
}

/**
 * Anula una factura
 * 
 * @param int $factura_id ID de la factura a anular
 * @return bool Éxito o fracaso de la operación
 */
function anularFactura($factura_id) {
    try {
        if (empty($factura_id)) {
            showError("El ID de factura es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento almacenado
        $sql = "BEGIN FIDE_FACTURACION_PKG.FIDE_ANULAR_FACTURA_PROC(:p_factura_id); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_factura_id", $factura_id);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Factura anulada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al anular factura: " . $e->getMessage());
        showError("No se pudo anular la factura: " . $e->getMessage());
        return false;
    }
}

/**
 * Aplica un descuento a una factura
 * 
 * @param int $factura_id ID de la factura
 * @param string $codigo_descuento Código del descuento a aplicar
 * @return bool Éxito o fracaso de la operación
 */
function aplicarDescuento($factura_id, $codigo_descuento) {
    try {
        if (empty($factura_id) || empty($codigo_descuento)) {
            showError("El ID de factura y el código de descuento son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento almacenado
        $sql = "BEGIN FIDE_FACTURACION_PKG.FIDE_APLICAR_DESCUENTO_PROC(:p_factura_id, :p_codigo_descuento); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_factura_id", $factura_id);
        oci_bind_by_name($stmt, ":p_codigo_descuento", $codigo_descuento);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        showSuccess("Descuento aplicado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al aplicar descuento: " . $e->getMessage());
        showError("No se pudo aplicar el descuento: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene el listado de estado de facturación general
 * 
 * @return array Lista de facturas con su estado
 */
function obtenerEstadoFacturacion() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Crear un cursor para recibir los resultados
        $cursor = oci_new_cursor($conn);
        
        // Preparar y ejecutar el procedimiento almacenado
        $sql = "BEGIN FIDE_ESTADO_FACTURACION_PKG.FIDE_LISTAR_ESTADO_FACTURACION_PROC(:p_cursor); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_cursor", $cursor, -1, OCI_B_CURSOR);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_execute($cursor);
        
        // Recopilar los resultados
        $facturas = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $facturas[] = $row;
        }
        
        // Liberar recursos
        oci_free_statement($cursor);
        oci_free_statement($stmt);
        
        return $facturas;
    } catch (Exception $e) {
        logError("Error al obtener estado de facturación: " . $e->getMessage());
        showError("No se pudo obtener el estado de facturación: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene el listado de facturas de un paciente específico
 * 
 * @param string $paciente_cedula Cédula del paciente
 * @return array Lista de facturas del paciente
 */
function obtenerFacturasPaciente($paciente_cedula) {
    try {
        if (empty($paciente_cedula)) {
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
        $sql = "BEGIN FIDE_ESTADO_FACTURACION_PKG.FIDE_LISTAR_ESTADO_FACTURACION_PACIENTE_PROC(:p_cedula_paciente, :p_cursor); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_cedula_paciente", $paciente_cedula);
        oci_bind_by_name($stmt, ":p_cursor", $cursor, -1, OCI_B_CURSOR);
        
        // Ejecutar el procedimiento
        oci_execute($stmt);
        oci_execute($cursor);
        
        // Recopilar los resultados
        $facturas = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $facturas[] = $row;
        }
        
        // Liberar recursos
        oci_free_statement($cursor);
        oci_free_statement($stmt);
        
        return $facturas;
    } catch (Exception $e) {
        logError("Error al obtener facturas del paciente: " . $e->getMessage());
        showError("No se pudo obtener las facturas del paciente: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los detalles de una factura específica
 * 
 * @param int $factura_id ID de la factura
 * @return array Detalles de la factura
 */
function obtenerDetallesFactura($factura_id) {
    try {
        if (empty($factura_id)) {
            showError("El ID de factura es obligatorio");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Consulta para obtener información básica de la factura
        $query_factura = "SELECT 
                            f.FIDE_FACTURA_ID, 
                            f.FIDE_PACIENTE_CEDULA, 
                            p.FIDE_NOMBRE_PACIENTE,
                            p.FIDE_APELLIDOS_PACIENTE,
                            f.FIDE_TOTAL_FACTURA, 
                            f.FIDE_ESTADO_FACTURA,
                            f.FIDE_PORCENTAJE_APLICADO
                        FROM 
                            FIDE_FACTURAS_TB f
                        JOIN 
                            FIDE_PACIENTES_TB p ON f.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
                        WHERE 
                            f.FIDE_FACTURA_ID = :factura_id";
        
        $stmt_factura = oci_parse($conn, $query_factura);
        oci_bind_by_name($stmt_factura, ":factura_id", $factura_id);
        oci_execute($stmt_factura);
        
        $factura = oci_fetch_assoc($stmt_factura);
        oci_free_statement($stmt_factura);
        
        if (!$factura) {
            showError("Factura no encontrada");
            return [];
        }
        
        // Consulta para obtener los detalles de la factura
$query_detalles = $query_detalles = "SELECT 
                    FIDE_DETALLE_FACTURA_ID,
                    FIDE_DESCRIPCION_FACTURA,
                    FIDE_MONTO_FACTURA
                FROM 
                    FIDE_DETALLES_FACTURAS_TB
                WHERE 
                    FIDE_FACTURA_ID = :factura_id
                ORDER BY 
                    FIDE_DETALLE_FACTURA_ID";
        
        $stmt_detalles = oci_parse($conn, $query_detalles);
        oci_bind_by_name($stmt_detalles, ":factura_id", $factura_id);
        oci_execute($stmt_detalles);
        
        $detalles = [];
        while ($row = oci_fetch_assoc($stmt_detalles)) {
            $detalles[] = $row;
        }
        oci_free_statement($stmt_detalles);
        
        // Consulta para obtener los recibos de pago
        $query_recibos = "SELECT 
                            FIDE_RECIBO_ID,
                            FIDE_METODO_PAGO,
                            FIDE_MONTO_PAGADO,
                            FIDE_REFERENCIA_PAGO,
                            FIDE_FECHA_PAGO
                        FROM 
                            FIDE_RECIBOS_TB
                        WHERE 
                            FIDE_FACTURA_ID = :factura_id
                        ORDER BY 
                            FIDE_FECHA_PAGO";
        
        $stmt_recibos = oci_parse($conn, $query_recibos);
        oci_bind_by_name($stmt_recibos, ":factura_id", $factura_id);
        oci_execute($stmt_recibos);
        
        $recibos = [];
        while ($row = oci_fetch_assoc($stmt_recibos)) {
            $recibos[] = $row;
        }
        oci_free_statement($stmt_recibos);
        
        // Combinar toda la información
        return [
            'factura' => $factura,
            'detalles' => $detalles,
            'recibos' => $recibos
        ];
    } catch (Exception $e) {
        logError("Error al obtener detalles de factura: " . $e->getMessage());
        showError("No se pudo obtener los detalles de la factura: " . $e->getMessage());
        return [];
    }
}

/**
 * Recalcula el total de una factura (después de cambios en detalles o descuentos)
 * 
 * @param int $factura_id ID de la factura
 * @return bool Éxito o fracaso de la operación
 */
function recalcularTotalFactura($factura_id) {
    try {
        if (empty($factura_id)) {
            showError("El ID de factura es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        $conn = $db->getConnection();
        
        // Llamar al procedimiento almacenado
        $sql = "BEGIN FIDE_FACTURACION_PKG.FIDE_CALCULAR_TOTAL_FACTURA_PROC(:p_factura_id); END;";
        $stmt = oci_parse($conn, $sql);
        
        // Bind de parámetros
        oci_bind_by_name($stmt, ":p_factura_id", $factura_id);
        
        // Ejecutar el procedimiento
        $result = oci_execute($stmt);
        oci_free_statement($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception($error['message']);
        }
        
        return true;
    } catch (Exception $e) {
        logError("Error al recalcular total de factura: " . $e->getMessage());
        showError("No se pudo recalcular el total de la factura: " . $e->getMessage());
        return false;
    }
}