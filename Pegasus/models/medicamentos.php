<?php
/**
 * Registra una reserva de medicamento para un paciente
 * 
 * @param string $codigo_medicamento Código del medicamento
 * @param string $cedula_paciente Cédula del paciente
 * @param int $cantidad Cantidad de medicamento a reservar
 * @param string $cedula_medico Cédula del médico que prescribe
 * @param string $observaciones Observaciones sobre la reserva
 * @param string $usuario_id ID del usuario que registra la reserva
 * @return bool Éxito o fracaso de la operación
 */
function reservarMedicamento($codigo_medicamento, $cedula_paciente, $cantidad, $cedula_medico, $observaciones, $usuario_id) {
    try {
        // Validar datos
        if (empty($codigo_medicamento) || empty($cedula_paciente) || empty($cantidad) || empty($cedula_medico)) {
            showError("Los campos código del medicamento, cédula del paciente, cantidad y cédula del médico son obligatorios");
            return false;
        }
        
        if (!is_numeric($cantidad) || $cantidad <= 0) {
            showError("La cantidad debe ser un valor numérico mayor que cero");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Iniciar una transacción
        $db->beginTransaction();
        
        try {
            // Verificar que el medicamento exista y tenga stock suficiente
            $query = "SELECT FIDE_NOMBRE_MEDICAMENTO, FIDE_STOCK_ACTUAL 
                      FROM FIDE_MEDICAMENTOS_TB 
                      WHERE FIDE_CODIGO_MEDICAMENTO = :codigo 
                      AND FIDE_ESTADO = 'ACTIVO' 
                      AND FIDE_FECHA_VENCIMIENTO >= SYSDATE 
                      FOR UPDATE";
            
            $medicamento = $db->queryOne($query, ['codigo' => $codigo_medicamento]);
            
            if (!$medicamento) {
                throw new Exception("El medicamento no existe, está inactivo o ha vencido");
            }
            
            if ($medicamento['FIDE_STOCK_ACTUAL'] < $cantidad) {
                throw new Exception("No hay suficiente stock disponible. Stock actual: " . $medicamento['FIDE_STOCK_ACTUAL']);
            }
            
            // Verificar que el paciente exista
            $query = "SELECT FIDE_NOMBRE_PACIENTE, FIDE_APELLIDOS_PACIENTE 
                      FROM FIDE_PACIENTES_TB 
                      WHERE FIDE_PACIENTE_CEDULA = :cedula";
            
            $paciente = $db->queryOne($query, ['cedula' => $cedula_paciente]);
            
            if (!$paciente) {
                throw new Exception("El paciente no existe en el sistema");
            }
            
            // Verificar que el médico exista
            $query = "SELECT FIDE_NOMBRE_EMPLEADO, FIDE_APELLIDOS_EMPLEADO 
                      FROM FIDE_EMPLEADOS_TB 
                      WHERE FIDE_EMPLEADO_CEDULA = :cedula 
                      AND FIDE_TIPO_EMPLEADO_ID = 1"; // Asumiendo que el tipo 1 es para médicos
            
            $medico = $db->queryOne($query, ['cedula' => $cedula_medico]);
            
            if (!$medico) {
                throw new Exception("El médico no existe en el sistema o no es un médico registrado");
            }
            
            // Registrar la reserva
            $db->executeProcedure('FIDE_MEDICAMENTOS_DISPONIBLES_PKG.FIDE_REGISTRAR_RESERVA_PROC', [
                'p_codigo_medicamento' => $codigo_medicamento,
                'p_cedula_paciente' => $cedula_paciente,
                'p_cantidad' => $cantidad,
                'p_cedula_medico' => $cedula_medico,
                'p_observaciones' => $observaciones,
                'p_usuario_id' => $usuario_id
            ]);
            
            // Actualizar el stock del medicamento
            $query = "UPDATE FIDE_MEDICAMENTOS_TB 
                      SET FIDE_STOCK_ACTUAL = FIDE_STOCK_ACTUAL - :cantidad 
                      WHERE FIDE_CODIGO_MEDICAMENTO = :codigo";
            
            $db->query($query, [
                'codigo' => $codigo_medicamento,
                'cantidad' => $cantidad
            ]);
            
            // Registrar movimiento de stock
            $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_REGISTRAR_MOVIMIENTO_STOCK_PROC', [
                'p_codigo_medicamento' => $codigo_medicamento,
                'p_tipo_movimiento' => 'SALIDA',
                'p_cantidad' => $cantidad,
                'p_motivo' => "Reserva para paciente: $cedula_paciente",
                'p_usuario_id' => $usuario_id
            ]);
            
            // Confirmar la transacción
            $db->commit();
            
            showSuccess("Medicamento reservado correctamente para el paciente " . $paciente['FIDE_NOMBRE_PACIENTE'] . " " . $paciente['FIDE_APELLIDOS_PACIENTE']);
            return true;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $db->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        logError("Error al reservar medicamento: " . $e->getMessage());
        showError("No se pudo reservar el medicamento: " . $e->getMessage());
        return false;
    }
}

/**
 * Cancela una reserva de medicamento
 * 
 * @param int $reserva_id ID de la reserva
 * @param string $motivo_cancelacion Motivo de la cancelación
 * @param string $usuario_id ID del usuario que cancela la reserva
 * @return bool Éxito o fracaso de la operación
 */
function cancelarReservaMedicamento($reserva_id, $motivo_cancelacion, $usuario_id) {
    try {
        // Validar datos
        if (empty($reserva_id) || empty($motivo_cancelacion) || empty($usuario_id)) {
            showError("Los campos ID de reserva, motivo de cancelación y usuario son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Iniciar una transacción
        $db->beginTransaction();
        
        try {
            // Obtener información de la reserva
            $query = "SELECT 
                        r.FIDE_CODIGO_MEDICAMENTO, 
                        r.FIDE_CANTIDAD,
                        r.FIDE_ESTADO,
                        m.FIDE_NOMBRE_MEDICAMENTO
                      FROM 
                        FIDE_RESERVAS_MEDICAMENTOS_TB r
                      JOIN 
                        FIDE_MEDICAMENTOS_TB m ON r.FIDE_CODIGO_MEDICAMENTO = m.FIDE_CODIGO_MEDICAMENTO
                      WHERE 
                        r.FIDE_RESERVA_ID = :reserva_id
                      FOR UPDATE";
            
            $reserva = $db->queryOne($query, ['reserva_id' => $reserva_id]);
            
            if (!$reserva) {
                throw new Exception("La reserva no existe en el sistema");
            }
            
            if ($reserva['FIDE_ESTADO'] !== 'ACTIVO') {
                throw new Exception("La reserva ya ha sido entregada o cancelada");
            }
            
            // Cancelar la reserva
            $query = "UPDATE FIDE_RESERVAS_MEDICAMENTOS_TB 
                      SET FIDE_ESTADO = 'CANCELADO',
                          FIDE_MOTIVO_CANCELACION = :motivo,
                          FIDE_FECHA_CANCELACION = SYSTIMESTAMP,
                          FIDE_USUARIO_CANCELACION_ID = :usuario_id
                      WHERE FIDE_RESERVA_ID = :reserva_id";
            
            $db->query($query, [
                'reserva_id' => $reserva_id,
                'motivo' => $motivo_cancelacion,
                'usuario_id' => $usuario_id
            ]);
            
            // Devolver los medicamentos al stock
            $query = "UPDATE FIDE_MEDICAMENTOS_TB 
                      SET FIDE_STOCK_ACTUAL = FIDE_STOCK_ACTUAL + :cantidad 
                      WHERE FIDE_CODIGO_MEDICAMENTO = :codigo";
            
            $db->query($query, [
                'codigo' => $reserva['FIDE_CODIGO_MEDICAMENTO'],
                'cantidad' => $reserva['FIDE_CANTIDAD']
            ]);
            
            // Registrar movimiento de stock
            $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_REGISTRAR_MOVIMIENTO_STOCK_PROC', [
                'p_codigo_medicamento' => $reserva['FIDE_CODIGO_MEDICAMENTO'],
                'p_tipo_movimiento' => 'ENTRADA',
                'p_cantidad' => $reserva['FIDE_CANTIDAD'],
                'p_motivo' => "Cancelación de reserva #$reserva_id: $motivo_cancelacion",
                'p_usuario_id' => $usuario_id
            ]);
            
            // Confirmar la transacción
            $db->commit();
            
            showSuccess("Reserva cancelada correctamente. Se han devuelto " . $reserva['FIDE_CANTIDAD'] . " unidades de " . $reserva['FIDE_NOMBRE_MEDICAMENTO'] . " al inventario.");
            return true;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $db->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        logError("Error al cancelar reserva: " . $e->getMessage());
        showError("No se pudo cancelar la reserva: " . $e->getMessage());
        return false;
    }
}

/**
 * Marca una reserva de medicamento como entregada
 * 
 * @param int $reserva_id ID de la reserva
 * @param string $observaciones_entrega Observaciones sobre la entrega
 * @param string $usuario_id ID del usuario que entrega el medicamento
 * @return bool Éxito o fracaso de la operación
 */
function entregarReservaMedicamento($reserva_id, $observaciones_entrega, $usuario_id) {
    try {
        // Validar datos
        if (empty($reserva_id) || empty($usuario_id)) {
            showError("Los campos ID de reserva y usuario son obligatorios");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Obtener información de la reserva
        $query = "SELECT 
                    r.FIDE_CODIGO_MEDICAMENTO, 
                    r.FIDE_CANTIDAD,
                    r.FIDE_ESTADO,
                    m.FIDE_NOMBRE_MEDICAMENTO,
                    p.FIDE_NOMBRE_PACIENTE,
                    p.FIDE_APELLIDOS_PACIENTE
                  FROM 
                    FIDE_RESERVAS_MEDICAMENTOS_TB r
                  JOIN 
                    FIDE_MEDICAMENTOS_TB m ON r.FIDE_CODIGO_MEDICAMENTO = m.FIDE_CODIGO_MEDICAMENTO
                  JOIN 
                    FIDE_PACIENTES_TB p ON r.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
                  WHERE 
                    r.FIDE_RESERVA_ID = :reserva_id";
        
        $reserva = $db->queryOne($query, ['reserva_id' => $reserva_id]);
        
        if (!$reserva) {
            showError("La reserva no existe en el sistema");
            return false;
        }
        
        if ($reserva['FIDE_ESTADO'] !== 'ACTIVO') {
            showError("La reserva ya ha sido entregada o cancelada");
            return false;
        }
        
        // Marcar la reserva como entregada
        $db->executeProcedure('FIDE_MEDICAMENTOS_DISPONIBLES_PKG.FIDE_ENTREGAR_RESERVA_PROC', [
            'p_reserva_id' => $reserva_id,
            'p_observaciones' => $observaciones_entrega,
            'p_usuario_id' => $usuario_id
        ]);
        
        showSuccess("Medicamento entregado correctamente a " . $reserva['FIDE_NOMBRE_PACIENTE'] . " " . $reserva['FIDE_APELLIDOS_PACIENTE']);
        return true;
    } catch (Exception $e) {
        logError("Error al entregar reserva: " . $e->getMessage());
        showError("No se pudo entregar la reserva: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene la lista de reservas de medicamentos
 * 
 * @param string $estado Estado de las reservas a obtener (ACTIVO, ENTREGADO, CANCELADO, TODOS)
 * @param string $cedula_paciente Cédula del paciente para filtrar (opcional)
 * @return array Lista de reservas
 */
function obtenerReservasMedicamentos($estado = 'TODOS', $cedula_paciente = null) {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar condiciones para el filtro
        $condiciones = "WHERE 1=1";
        $params = [];
        
        if ($estado && $estado != 'TODOS') {
            $condiciones .= " AND r.FIDE_ESTADO = :estado";
            $params['estado'] = $estado;
        }
        
        if ($cedula_paciente) {
            $condiciones .= " AND r.FIDE_PACIENTE_CEDULA = :cedula_paciente";
            $params['cedula_paciente'] = $cedula_paciente;
        }
        
        // Consulta SQL para obtener las reservas
        $query = "SELECT 
                    r.FIDE_RESERVA_ID, 
                    r.FIDE_CODIGO_MEDICAMENTO,
                    m.FIDE_NOMBRE_MEDICAMENTO,
                    r.FIDE_PACIENTE_CEDULA,
                    p.FIDE_NOMBRE_PACIENTE,
                    p.FIDE_APELLIDOS_PACIENTE,
                    r.FIDE_CANTIDAD,
                    r.FIDE_MEDICO_CEDULA,
                    e.FIDE_NOMBRE_EMPLEADO || ' ' || e.FIDE_APELLIDOS_EMPLEADO AS FIDE_NOMBRE_MEDICO,
                    r.FIDE_OBSERVACIONES,
                    r.FIDE_FECHA_RESERVA,
                    r.FIDE_ESTADO,
                    r.FIDE_FECHA_ENTREGA,
                    r.FIDE_USUARIO_ENTREGA_ID,
                    u1.FIDE_NOMBRE_COMPLETO AS FIDE_USUARIO_ENTREGA_NOMBRE,
                    r.FIDE_OBSERVACIONES_ENTREGA,
                    r.FIDE_FECHA_CANCELACION,
                    r.FIDE_MOTIVO_CANCELACION,
                    r.FIDE_USUARIO_CANCELACION_ID,
                    u2.FIDE_NOMBRE_COMPLETO AS FIDE_USUARIO_CANCELACION_NOMBRE,
                    u3.FIDE_NOMBRE_COMPLETO AS FIDE_USUARIO_CREACION_NOMBRE
                FROM 
                    FIDE_RESERVAS_MEDICAMENTOS_TB r
                JOIN 
                    FIDE_MEDICAMENTOS_TB m ON r.FIDE_CODIGO_MEDICAMENTO = m.FIDE_CODIGO_MEDICAMENTO
                JOIN 
                    FIDE_PACIENTES_TB p ON r.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
                JOIN 
                    FIDE_EMPLEADOS_TB e ON r.FIDE_MEDICO_CEDULA = e.FIDE_EMPLEADO_CEDULA
                JOIN 
                    FIDE_USUARIOS_TB u3 ON r.FIDE_USUARIO_ID = u3.FIDE_USUARIO_ID
                LEFT JOIN 
                    FIDE_USUARIOS_TB u1 ON r.FIDE_USUARIO_ENTREGA_ID = u1.FIDE_USUARIO_ID
                LEFT JOIN 
                    FIDE_USUARIOS_TB u2 ON r.FIDE_USUARIO_CANCELACION_ID = u2.FIDE_USUARIO_ID
                $condiciones
                ORDER BY 
                    r.FIDE_FECHA_RESERVA DESC";
        
        // Ejecutar la consulta
        $result = $db->query($query, $params);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener reservas: " . $e->getMessage());
        showError("No se pudo obtener la lista de reservas: " . $e->getMessage());
        return [];
    }
}

/**
 * Modelo para la gestión de medicamentos
 * Sistema de Gestión Hospitalaria Pegasus
 * Interactúa con los paquetes FIDE_MEDICAMENTOS_PKG y FIDE_MEDICAMENTOS_DISPONIBLES_PKG
 */

/**
 * Registra un nuevo medicamento en el sistema
 * 
 * @param string $codigo Código del medicamento
 * @param string $nombre Nombre del medicamento
 * @param string $descripcion Descripción del medicamento
 * @param float $precio Precio unitario del medicamento
 * @param int $stock_inicial Stock inicial del medicamento
 * @param int $categoria_id ID de la categoría del medicamento
 * @param string $fabricante Fabricante del medicamento
 * @param string $fecha_vencimiento Fecha de vencimiento (formato YYYY-MM-DD)
 * @return bool Éxito o fracaso de la operación
 */
function registrarMedicamento($codigo, $nombre, $descripcion, $precio, $stock_inicial, $categoria_id, $fabricante, $fecha_vencimiento) {
    try {
        // Validar datos
        if (empty($codigo) || empty($nombre) || empty($precio) || empty($stock_inicial)) {
            showError("Los campos código, nombre, precio y stock inicial son obligatorios");
            return false;
        }
        
        if (!is_numeric($precio) || $precio <= 0) {
            showError("El precio debe ser un valor numérico mayor que cero");
            return false;
        }
        
        if (!is_numeric($stock_inicial) || $stock_inicial < 0) {
            showError("El stock inicial debe ser un valor numérico no negativo");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar si el código ya existe
        $query = "SELECT COUNT(*) AS existe FROM FIDE_MEDICAMENTOS_TB WHERE FIDE_CODIGO_MEDICAMENTO = :codigo";
        $resultado = $db->queryValue($query, ['codigo' => $codigo], 'EXISTE');
        
        if ($resultado > 0) {
            showError("Ya existe un medicamento con el código: $codigo");
            return false;
        }
        
        // Preparar parámetros para el procedimiento
        $params = [
            'p_codigo' => $codigo,
            'p_nombre' => $nombre,
            'p_descripcion' => $descripcion,
            'p_precio' => $precio,
            'p_stock' => $stock_inicial,
            'p_categoria_id' => $categoria_id,
            'p_fabricante' => $fabricante,
            'p_fecha_vencimiento' => $fecha_vencimiento
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_REGISTRAR_MEDICAMENTO_PROC', $params);
        
        showSuccess("Medicamento registrado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al registrar medicamento: " . $e->getMessage());
        showError("No se pudo registrar el medicamento: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualiza un medicamento existente
 * 
 * @param string $codigo Código del medicamento
 * @param string $nombre Nombre del medicamento
 * @param string $descripcion Descripción del medicamento
 * @param float $precio Precio unitario del medicamento
 * @param int $categoria_id ID de la categoría del medicamento
 * @param string $fabricante Fabricante del medicamento
 * @param string $fecha_vencimiento Fecha de vencimiento (formato YYYY-MM-DD)
 * @param string $estado Estado del medicamento (ACTIVO, INACTIVO)
 * @return bool Éxito o fracaso de la operación
 */
function actualizarMedicamento($codigo, $nombre, $descripcion, $precio, $categoria_id, $fabricante, $fecha_vencimiento, $estado) {
    try {
        // Validar datos
        if (empty($codigo) || empty($nombre) || empty($precio)) {
            showError("Los campos código, nombre y precio son obligatorios");
            return false;
        }
        
        if (!is_numeric($precio) || $precio <= 0) {
            showError("El precio debe ser un valor numérico mayor que cero");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar parámetros para el procedimiento
        $params = [
            'p_codigo' => $codigo,
            'p_nombre' => $nombre,
            'p_descripcion' => $descripcion,
            'p_precio' => $precio,
            'p_categoria_id' => $categoria_id,
            'p_fabricante' => $fabricante,
            'p_fecha_vencimiento' => $fecha_vencimiento,
            'p_estado' => $estado
        ];
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_ACTUALIZAR_MEDICAMENTO_PROC', $params);
        
        showSuccess("Medicamento actualizado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al actualizar medicamento: " . $e->getMessage());
        showError("No se pudo actualizar el medicamento: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene un medicamento por su código
 * 
 * @param string $codigo Código del medicamento
 * @return array|null Datos del medicamento o null si no se encuentra
 */
function obtenerMedicamento($codigo) {
    try {
        if (empty($codigo)) {
            showError("El código del medicamento es obligatorio");
            return null;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener los datos del medicamento
        $query = "SELECT 
                    m.FIDE_CODIGO_MEDICAMENTO, 
                    m.FIDE_NOMBRE_MEDICAMENTO,
                    m.FIDE_DESCRIPCION_MEDICAMENTO,
                    m.FIDE_PRECIO_UNITARIO,
                    m.FIDE_STOCK_ACTUAL,
                    m.FIDE_CATEGORIA_ID,
                    c.FIDE_NOMBRE_CATEGORIA,
                    m.FIDE_FABRICANTE,
                    m.FIDE_FECHA_VENCIMIENTO,
                    m.FIDE_ESTADO,
                    m.FIDE_FECHA_REGISTRO
                FROM 
                    FIDE_MEDICAMENTOS_TB m
                JOIN 
                    FIDE_CATEGORIAS_MEDICAMENTOS_TB c ON m.FIDE_CATEGORIA_ID = c.FIDE_CATEGORIA_ID
                WHERE 
                    m.FIDE_CODIGO_MEDICAMENTO = :codigo";
        
        // Ejecutar la consulta
        $result = $db->queryOne($query, ['codigo' => $codigo]);
        
        if (!$result) {
            showInfo("No se encontró el medicamento con el código: $codigo");
            return null;
        }
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener medicamento: " . $e->getMessage());
        showError("No se pudo obtener el medicamento: " . $e->getMessage());
        return null;
    }
}

/**
 * Elimina un medicamento del sistema
 * 
 * @param string $codigo Código del medicamento
 * @return bool Éxito o fracaso de la operación
 */
function eliminarMedicamento($codigo) {
    try {
        if (empty($codigo)) {
            showError("El código del medicamento es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar si el medicamento está asociado a alguna receta o prescripción
        $query = "SELECT COUNT(*) AS cuenta 
                  FROM FIDE_RECETAS_DETALLES_TB 
                  WHERE FIDE_CODIGO_MEDICAMENTO = :codigo";
        
        $resultado = $db->queryValue($query, ['codigo' => $codigo], 'CUENTA');
        
        if ($resultado > 0) {
            showError("No se puede eliminar el medicamento porque está asociado a recetas o prescripciones");
            return false;
        }
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_ELIMINAR_MEDICAMENTO_PROC', [
            'p_codigo' => $codigo
        ]);
        
        showSuccess("Medicamento eliminado correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al eliminar medicamento: " . $e->getMessage());
        showError("No se pudo eliminar el medicamento: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualiza el stock de un medicamento
 * 
 * @param string $codigo Código del medicamento
 * @param int $cantidad Cantidad a incrementar (si es positiva) o decrementar (si es negativa)
 * @param string $motivo Motivo del ajuste de stock
 * @param string $usuario_id ID del usuario que realiza el ajuste
 * @return bool Éxito o fracaso de la operación
 */
function actualizarStockMedicamento($codigo, $cantidad, $motivo, $usuario_id) {
    try {
        // Validar datos
        if (empty($codigo) || !isset($cantidad) || empty($motivo) || empty($usuario_id)) {
            showError("Los campos código, cantidad, motivo y usuario son obligatorios");
            return false;
        }
        
        if (!is_numeric($cantidad)) {
            showError("La cantidad debe ser un valor numérico");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Iniciar una transacción
        $db->beginTransaction();
        
        try {
            // Obtener stock actual
            $query = "SELECT FIDE_STOCK_ACTUAL FROM FIDE_MEDICAMENTOS_TB WHERE FIDE_CODIGO_MEDICAMENTO = :codigo FOR UPDATE";
            $stock_actual = $db->queryValue($query, ['codigo' => $codigo], 'FIDE_STOCK_ACTUAL');
            
            if ($stock_actual === null) {
                throw new Exception("El medicamento no existe");
            }
            
            // Verificar que la operación no dé lugar a un stock negativo
            $nuevo_stock = $stock_actual + $cantidad;
            if ($nuevo_stock < 0) {
                throw new Exception("No hay suficiente stock disponible. Stock actual: $stock_actual");
            }
            
            // Actualizar stock
            $query = "UPDATE FIDE_MEDICAMENTOS_TB 
                      SET FIDE_STOCK_ACTUAL = FIDE_STOCK_ACTUAL + :cantidad 
                      WHERE FIDE_CODIGO_MEDICAMENTO = :codigo";
            
            $db->query($query, [
                'codigo' => $codigo,
                'cantidad' => $cantidad
            ]);
            
            // Registrar movimiento de stock
            $tipo_movimiento = ($cantidad > 0) ? 'ENTRADA' : 'SALIDA';
            $cantidad_abs = abs($cantidad);
            
            $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_REGISTRAR_MOVIMIENTO_STOCK_PROC', [
                'p_codigo_medicamento' => $codigo,
                'p_tipo_movimiento' => $tipo_movimiento,
                'p_cantidad' => $cantidad_abs,
                'p_motivo' => $motivo,
                'p_usuario_id' => $usuario_id
            ]);
            
            // Confirmar la transacción
            $db->commit();
            
            showSuccess("Stock actualizado correctamente. Nuevo stock: $nuevo_stock");
            return true;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $db->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        logError("Error al actualizar stock: " . $e->getMessage());
        showError("No se pudo actualizar el stock: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene el historial de movimientos de stock de un medicamento
 * 
 * @param string $codigo Código del medicamento
 * @param string $fecha_inicio Fecha de inicio para filtrar (formato YYYY-MM-DD)
 * @param string $fecha_fin Fecha de fin para filtrar (formato YYYY-MM-DD)
 * @return array Historial de movimientos
 */
function obtenerHistorialMovimientosStock($codigo, $fecha_inicio = null, $fecha_fin = null) {
    try {
        if (empty($codigo)) {
            showError("El código del medicamento es obligatorio");
            return [];
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar condiciones para el filtro de fechas
        $condiciones = "WHERE ms.FIDE_CODIGO_MEDICAMENTO = :codigo";
        $params = ['codigo' => $codigo];
        
        if ($fecha_inicio) {
            $condiciones .= " AND ms.FIDE_FECHA_MOVIMIENTO >= TO_DATE(:fecha_inicio, 'YYYY-MM-DD')";
            $params['fecha_inicio'] = $fecha_inicio;
        }
        
        if ($fecha_fin) {
            $condiciones .= " AND ms.FIDE_FECHA_MOVIMIENTO <= TO_DATE(:fecha_fin, 'YYYY-MM-DD') + INTERVAL '1' DAY - INTERVAL '1' SECOND";
            $params['fecha_fin'] = $fecha_fin;
        }
        
        // Consulta SQL para obtener el historial de movimientos
        $query = "SELECT 
                    ms.FIDE_MOVIMIENTO_ID, 
                    ms.FIDE_TIPO_MOVIMIENTO, 
                    ms.FIDE_CANTIDAD, 
                    ms.FIDE_MOTIVO, 
                    ms.FIDE_FECHA_MOVIMIENTO,
                    u.FIDE_NOMBRE_COMPLETO AS FIDE_USUARIO_NOMBRE
                FROM 
                    FIDE_MEDICAMENTOS_STOCK_TB ms
                JOIN 
                    FIDE_USUARIOS_TB u ON ms.FIDE_USUARIO_ID = u.FIDE_USUARIO_ID
                $condiciones
                ORDER BY 
                    ms.FIDE_FECHA_MOVIMIENTO DESC";
        
        // Ejecutar la consulta
        $result = $db->query($query, $params);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener historial de movimientos: " . $e->getMessage());
        showError("No se pudo obtener el historial de movimientos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de todos los medicamentos
 * 
 * @param string $filtro Filtro para buscar por código o nombre (opcional)
 * @param int $categoria_id ID de la categoría para filtrar (opcional)
 * @param string $estado Estado para filtrar (ACTIVO, INACTIVO, TODOS) (opcional)
 * @return array Lista de medicamentos
 */
function obtenerTodosMedicamentos($filtro = null, $categoria_id = null, $estado = 'TODOS') {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar condiciones para el filtro
        $condiciones = "WHERE 1=1";
        $params = [];
        
        if ($filtro) {
            $condiciones .= " AND (UPPER(m.FIDE_CODIGO_MEDICAMENTO) LIKE UPPER('%' || :filtro || '%') OR 
                            UPPER(m.FIDE_NOMBRE_MEDICAMENTO) LIKE UPPER('%' || :filtro || '%'))";
            $params['filtro'] = $filtro;
        }
        
        if ($categoria_id) {
            $condiciones .= " AND m.FIDE_CATEGORIA_ID = :categoria_id";
            $params['categoria_id'] = $categoria_id;
        }
        
        if ($estado && $estado != 'TODOS') {
            $condiciones .= " AND m.FIDE_ESTADO = :estado";
            $params['estado'] = $estado;
        }
        
        // Consulta SQL para obtener todos los medicamentos
        $query = "SELECT 
                    m.FIDE_CODIGO_MEDICAMENTO, 
                    m.FIDE_NOMBRE_MEDICAMENTO, 
                    m.FIDE_DESCRIPCION_MEDICAMENTO, 
                    m.FIDE_PRECIO_UNITARIO, 
                    m.FIDE_STOCK_ACTUAL, 
                    c.FIDE_NOMBRE_CATEGORIA,
                    m.FIDE_FABRICANTE,
                    m.FIDE_FECHA_VENCIMIENTO,
                    m.FIDE_ESTADO,
                    CASE 
                        WHEN m.FIDE_FECHA_VENCIMIENTO < SYSDATE THEN 'VENCIDO'
                        WHEN m.FIDE_FECHA_VENCIMIENTO < SYSDATE + 30 THEN 'POR VENCER'
                        ELSE 'VIGENTE'
                    END AS ESTADO_VENCIMIENTO,
                    CASE 
                        WHEN m.FIDE_STOCK_ACTUAL = 0 THEN 'AGOTADO'
                        WHEN m.FIDE_STOCK_ACTUAL < 10 THEN 'BAJO'
                        ELSE 'NORMAL'
                    END AS ESTADO_STOCK
                FROM 
                    FIDE_MEDICAMENTOS_TB m
                JOIN 
                    FIDE_CATEGORIAS_MEDICAMENTOS_TB c ON m.FIDE_CATEGORIA_ID = c.FIDE_CATEGORIA_ID
                $condiciones
                ORDER BY 
                    m.FIDE_NOMBRE_MEDICAMENTO";
        
        // Ejecutar la consulta
        $result = $db->query($query, $params);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener medicamentos: " . $e->getMessage());
        showError("No se pudo obtener la lista de medicamentos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de medicamentos disponibles (con stock > 0 y no vencidos)
 * 
 * @param string $filtro Filtro para buscar por código o nombre (opcional)
 * @param int $categoria_id ID de la categoría para filtrar (opcional)
 * @return array Lista de medicamentos disponibles
 */
function obtenerMedicamentosDisponibles($filtro = null, $categoria_id = null) {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Preparar condiciones para el filtro
        $condiciones = "WHERE m.FIDE_STOCK_ACTUAL > 0 AND m.FIDE_FECHA_VENCIMIENTO >= SYSDATE AND m.FIDE_ESTADO = 'ACTIVO'";
        $params = [];
        
        if ($filtro) {
            $condiciones .= " AND (UPPER(m.FIDE_CODIGO_MEDICAMENTO) LIKE UPPER('%' || :filtro || '%') OR 
                            UPPER(m.FIDE_NOMBRE_MEDICAMENTO) LIKE UPPER('%' || :filtro || '%'))";
            $params['filtro'] = $filtro;
        }
        
        if ($categoria_id) {
            $condiciones .= " AND m.FIDE_CATEGORIA_ID = :categoria_id";
            $params['categoria_id'] = $categoria_id;
        }
        
        // Consulta SQL para obtener los medicamentos disponibles
        $query = "SELECT 
                    m.FIDE_CODIGO_MEDICAMENTO, 
                    m.FIDE_NOMBRE_MEDICAMENTO, 
                    m.FIDE_DESCRIPCION_MEDICAMENTO, 
                    m.FIDE_PRECIO_UNITARIO, 
                    m.FIDE_STOCK_ACTUAL, 
                    c.FIDE_NOMBRE_CATEGORIA,
                    m.FIDE_FABRICANTE,
                    m.FIDE_FECHA_VENCIMIENTO
                FROM 
                    FIDE_MEDICAMENTOS_TB m
                JOIN 
                    FIDE_CATEGORIAS_MEDICAMENTOS_TB c ON m.FIDE_CATEGORIA_ID = c.FIDE_CATEGORIA_ID
                $condiciones
                ORDER BY 
                    m.FIDE_NOMBRE_MEDICAMENTO";
        
        // Ejecutar la consulta
        $result = $db->query($query, $params);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener medicamentos disponibles: " . $e->getMessage());
        showError("No se pudo obtener la lista de medicamentos disponibles: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de medicamentos por vencer en los próximos X días
 * 
 * @param int $dias Número de días para considerar como "por vencer"
 * @return array Lista de medicamentos por vencer
 */
function obtenerMedicamentosPorVencer($dias = 30) {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener los medicamentos por vencer
        $query = "SELECT 
                    m.FIDE_CODIGO_MEDICAMENTO, 
                    m.FIDE_NOMBRE_MEDICAMENTO, 
                    m.FIDE_DESCRIPCION_MEDICAMENTO, 
                    m.FIDE_PRECIO_UNITARIO, 
                    m.FIDE_STOCK_ACTUAL, 
                    c.FIDE_NOMBRE_CATEGORIA,
                    m.FIDE_FABRICANTE,
                    m.FIDE_FECHA_VENCIMIENTO,
                    ROUND(m.FIDE_FECHA_VENCIMIENTO - SYSDATE) AS DIAS_RESTANTES
                FROM 
                    FIDE_MEDICAMENTOS_TB m
                JOIN 
                    FIDE_CATEGORIAS_MEDICAMENTOS_TB c ON m.FIDE_CATEGORIA_ID = c.FIDE_CATEGORIA_ID
                WHERE 
                    m.FIDE_FECHA_VENCIMIENTO BETWEEN SYSDATE AND SYSDATE + :dias
                    AND m.FIDE_STOCK_ACTUAL > 0
                    AND m.FIDE_ESTADO = 'ACTIVO'
                ORDER BY 
                    m.FIDE_FECHA_VENCIMIENTO";
        
        // Ejecutar la consulta
        $result = $db->query($query, ['dias' => $dias]);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener medicamentos por vencer: " . $e->getMessage());
        showError("No se pudo obtener la lista de medicamentos por vencer: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de medicamentos con stock bajo
 * 
 * @param int $limite Límite de stock para considerar como "bajo"
 * @return array Lista de medicamentos con stock bajo
 */
function obtenerMedicamentosStockBajo($limite = 10) {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener los medicamentos con stock bajo
        $query = "SELECT 
                    m.FIDE_CODIGO_MEDICAMENTO, 
                    m.FIDE_NOMBRE_MEDICAMENTO, 
                    m.FIDE_DESCRIPCION_MEDICAMENTO, 
                    m.FIDE_PRECIO_UNITARIO, 
                    m.FIDE_STOCK_ACTUAL, 
                    c.FIDE_NOMBRE_CATEGORIA,
                    m.FIDE_FABRICANTE,
                    m.FIDE_FECHA_VENCIMIENTO
                FROM 
                    FIDE_MEDICAMENTOS_TB m
                JOIN 
                    FIDE_CATEGORIAS_MEDICAMENTOS_TB c ON m.FIDE_CATEGORIA_ID = c.FIDE_CATEGORIA_ID
                WHERE 
                    m.FIDE_STOCK_ACTUAL <= :limite
                    AND m.FIDE_STOCK_ACTUAL > 0
                    AND m.FIDE_ESTADO = 'ACTIVO'
                ORDER BY 
                    m.FIDE_STOCK_ACTUAL, m.FIDE_NOMBRE_MEDICAMENTO";
        
        // Ejecutar la consulta
        $result = $db->query($query, ['limite' => $limite]);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener medicamentos con stock bajo: " . $e->getMessage());
        showError("No se pudo obtener la lista de medicamentos con stock bajo: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la lista de categorías de medicamentos
 * 
 * @return array Lista de categorías
 */
function obtenerCategoriasMedicamentos() {
    try {
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Consulta SQL para obtener las categorías
        $query = "SELECT 
                    FIDE_CATEGORIA_ID, 
                    FIDE_NOMBRE_CATEGORIA, 
                    FIDE_DESCRIPCION_CATEGORIA
                FROM 
                    FIDE_CATEGORIAS_MEDICAMENTOS_TB
                ORDER BY 
                    FIDE_NOMBRE_CATEGORIA";
        
        // Ejecutar la consulta
        $result = $db->query($query);
        
        return $result;
    } catch (Exception $e) {
        logError("Error al obtener categorías: " . $e->getMessage());
        showError("No se pudo obtener la lista de categorías: " . $e->getMessage());
        return [];
    }
}

/**
 * Registra una nueva categoría de medicamentos
 * 
 * @param string $nombre Nombre de la categoría
 * @param string $descripcion Descripción de la categoría
 * @return bool Éxito o fracaso de la operación
 */
function registrarCategoriaMedicamento($nombre, $descripcion) {
    try {
        // Validar datos
        if (empty($nombre)) {
            showError("El nombre de la categoría es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar si la categoría ya existe
        $query = "SELECT COUNT(*) AS existe FROM FIDE_CATEGORIAS_MEDICAMENTOS_TB WHERE UPPER(FIDE_NOMBRE_CATEGORIA) = UPPER(:nombre)";
        $resultado = $db->queryValue($query, ['nombre' => $nombre], 'EXISTE');
        
        if ($resultado > 0) {
            showError("Ya existe una categoría con el nombre: $nombre");
            return false;
        }
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_REGISTRAR_CATEGORIA_PROC', [
            'p_nombre' => $nombre,
            'p_descripcion' => $descripcion
        ]);
        
        showSuccess("Categoría registrada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al registrar categoría: " . $e->getMessage());
        showError("No se pudo registrar la categoría: " . $e->getMessage());
        return false;
    }
}

/**
 * Elimina una categoría de medicamentos
 * 
 * @param int $categoria_id ID de la categoría
 * @return bool Éxito o fracaso de la operación
 */
function eliminarCategoriaMedicamento($categoria_id) {
    try {
        if (empty($categoria_id)) {
            showError("El ID de la categoría es obligatorio");
            return false;
        }
        
        // Obtener la conexión a la base de datos
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar si hay medicamentos asociados a la categoría
        $query = "SELECT COUNT(*) AS cuenta FROM FIDE_MEDICAMENTOS_TB WHERE FIDE_CATEGORIA_ID = :categoria_id";
        $resultado = $db->queryValue($query, ['categoria_id' => $categoria_id], 'CUENTA');
        
        if ($resultado > 0) {
            showError("No se puede eliminar la categoría porque hay medicamentos asociados a ella");
            return false;
        }
        
        // Ejecutar el procedimiento almacenado
        $db->executeProcedure('FIDE_MEDICAMENTOS_PKG.FIDE_ELIMINAR_CATEGORIA_PROC', [
            'p_categoria_id' => $categoria_id
        ]);
        
        showSuccess("Categoría eliminada correctamente");
        return true;
    } catch (Exception $e) {
        logError("Error al eliminar categoría: " . $e->getMessage());
        showError("No se pudo eliminar la categoría: " . $e->getMessage());
        return false;
    }
}

