<?php
/**
 * Modelo de Pacientes
 * Interactúa con los paquetes FIDE_PACIENTES_PKG, FIDE_PACIENTES_DEUDA_PKG y FIDE_PACIENTES_HOSPITALIZADOS_PKG
 * Sistema de Gestión Hospitalaria Pegasus
 */

require_once dirname(__DIR__) . '/includes/Database.php';

/**
 * Obtiene una lista de todos los pacientes
 * 
 * @param int $limit Límite de resultados
 * @param int $offset Desplazamiento para paginación
 * @return array Lista de pacientes
 */
function obtenerPacientes($limit = 10, $offset = 0) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        $query = "SELECT p.FIDE_PACIENTE_CEDULA, p.FIDE_NOMBRE_PACIENTE, p.FIDE_APELLIDOS_PACIENTE, 
                         p.FIDE_TELEFONO_PACIENTE, p.FIDE_CORREO_PACIENTE, p.FIDE_DEUDA_PACIENTE,
                         e.FIDE_DESCRIPCION_ESTADO_PACIENTE
                  FROM FIDE_PACIENTES_TB p
                  JOIN FIDE_ESTADOS_PACIENTES_TB e ON p.FIDE_ESTADO_PACIENTE_ID = e.FIDE_ESTADO_PACIENTE_ID
                  ORDER BY p.FIDE_APELLIDOS_PACIENTE, p.FIDE_NOMBRE_PACIENTE
                  OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
        
        $pacientes = $db->query($query, [
            'offset' => $offset,
            'limit' => $limit
        ]);
        
        $total = $db->queryValue("SELECT COUNT(*) FROM FIDE_PACIENTES_TB");
        
        return [
            'pacientes' => $pacientes,
            'total' => $total,
            'paginas' => ceil($total / $limit)
        ];
    } catch (Exception $e) {
        logError("Error en obtenerPacientes: " . $e->getMessage());
        return [
            'pacientes' => [],
            'total' => 0,
            'paginas' => 0
        ];
    }
}

/**
 * Busca pacientes por criterios
 * 
 * @param string $criterio Criterio de búsqueda (nombre, apellido, cédula, etc.)
 * @param string $valor Valor a buscar
 * @return array Lista de pacientes que coinciden
 */
function buscarPacientes($criterio, $valor) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        $query = "SELECT p.FIDE_PACIENTE_CEDULA, p.FIDE_NOMBRE_PACIENTE, p.FIDE_APELLIDOS_PACIENTE, 
                         p.FIDE_TELEFONO_PACIENTE, p.FIDE_CORREO_PACIENTE, p.FIDE_DEUDA_PACIENTE,
                         e.FIDE_DESCRIPCION_ESTADO_PACIENTE
                  FROM FIDE_PACIENTES_TB p
                  JOIN FIDE_ESTADOS_PACIENTES_TB e ON p.FIDE_ESTADO_PACIENTE_ID = e.FIDE_ESTADO_PACIENTE_ID
                  WHERE 1=1";
        
        $params = [];
        
        // Definir el criterio de búsqueda
        switch ($criterio) {
            case 'cedula':
                $query .= " AND p.FIDE_PACIENTE_CEDULA LIKE :valor";
                $params['valor'] = '%' . $valor . '%';
                break;
            case 'nombre':
                $query .= " AND UPPER(p.FIDE_NOMBRE_PACIENTE) LIKE UPPER(:valor)";
                $params['valor'] = '%' . $valor . '%';
                break;
            case 'apellido':
                $query .= " AND UPPER(p.FIDE_APELLIDOS_PACIENTE) LIKE UPPER(:valor)";
                $params['valor'] = '%' . $valor . '%';
                break;
            case 'telefono':
                $query .= " AND p.FIDE_TELEFONO_PACIENTE LIKE :valor";
                $params['valor'] = '%' . $valor . '%';
                break;
            case 'correo':
                $query .= " AND UPPER(p.FIDE_CORREO_PACIENTE) LIKE UPPER(:valor)";
                $params['valor'] = '%' . $valor . '%';
                break;
            default:
                // Búsqueda general en múltiples campos
                $query .= " AND (
                    p.FIDE_PACIENTE_CEDULA LIKE :valor OR
                    UPPER(p.FIDE_NOMBRE_PACIENTE) LIKE UPPER(:valor) OR
                    UPPER(p.FIDE_APELLIDOS_PACIENTE) LIKE UPPER(:valor) OR
                    p.FIDE_TELEFONO_PACIENTE LIKE :valor OR
                    UPPER(p.FIDE_CORREO_PACIENTE) LIKE UPPER(:valor)
                )";
                $params['valor'] = '%' . $valor . '%';
        }
        
        $query .= " ORDER BY p.FIDE_APELLIDOS_PACIENTE, p.FIDE_NOMBRE_PACIENTE";
        
        $pacientes = $db->query($query, $params);
        
        return $pacientes;
    } catch (Exception $e) {
        logError("Error en buscarPacientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los datos de un paciente específico
 * 
 * @param string $cedula Cédula del paciente
 * @return array|null Datos del paciente o null si no existe
 */
function obtenerPaciente($cedula) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        $query = "SELECT p.FIDE_PACIENTE_CEDULA, p.FIDE_NOMBRE_PACIENTE, p.FIDE_APELLIDOS_PACIENTE, 
                         p.FIDE_TELEFONO_PACIENTE, p.FIDE_CORREO_PACIENTE, p.FIDE_DIRECCION_PACIENTE,
                         p.FIDE_DEUDA_PACIENTE, p.FIDE_ESTADO_PACIENTE_ID,
                         e.FIDE_DESCRIPCION_ESTADO_PACIENTE
                  FROM FIDE_PACIENTES_TB p
                  JOIN FIDE_ESTADOS_PACIENTES_TB e ON p.FIDE_ESTADO_PACIENTE_ID = e.FIDE_ESTADO_PACIENTE_ID
                  WHERE p.FIDE_PACIENTE_CEDULA = :cedula";
        
        return $db->queryOne($query, ['cedula' => $cedula]);
    } catch (Exception $e) {
        logError("Error en obtenerPaciente: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtiene una lista de todos los estados de pacientes
 * 
 * @return array Lista de estados
 */
function obtenerEstadosPacientes() {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        $query = "SELECT FIDE_ESTADO_PACIENTE_ID, FIDE_DESCRIPCION_ESTADO_PACIENTE
                  FROM FIDE_ESTADOS_PACIENTES_TB
                  ORDER BY FIDE_DESCRIPCION_ESTADO_PACIENTE";
        
        return $db->query($query);
    } catch (Exception $e) {
        logError("Error en obtenerEstadosPacientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Inserta un nuevo paciente en la base de datos
 * 
 * @param array $paciente Datos del paciente
 * @return array Resultado de la operación
 */
function insertarPaciente($paciente) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar si ya existe un paciente con la misma cédula
        $existe = $db->queryValue("SELECT COUNT(*) FROM FIDE_PACIENTES_TB WHERE FIDE_PACIENTE_CEDULA = :cedula", 
                                ['cedula' => $paciente['cedula']]);
        
        if ($existe > 0) {
            return [
                'success' => false,
                'message' => 'Ya existe un paciente con esta cédula.'
            ];
        }
        
        // Ejecutar la inserción
        $db->query("INSERT INTO FIDE_PACIENTES_TB (
                      FIDE_PACIENTE_CEDULA, FIDE_NOMBRE_PACIENTE, FIDE_APELLIDOS_PACIENTE,
                      FIDE_TELEFONO_PACIENTE, FIDE_DIRECCION_PACIENTE, FIDE_CORREO_PACIENTE,
                      FIDE_ESTADO_PACIENTE_ID, FIDE_DEUDA_PACIENTE
                  ) VALUES (
                      :cedula, :nombre, :apellidos, :telefono, :direccion, :correo, :estado_id, 0
                  )", [
                      'cedula' => $paciente['cedula'],
                      'nombre' => $paciente['nombre'],
                      'apellidos' => $paciente['apellidos'],
                      'telefono' => $paciente['telefono'],
                      'direccion' => $paciente['direccion'],
                      'correo' => $paciente['correo'],
                      'estado_id' => $paciente['estado_id']
                  ]);
        
        return [
            'success' => true,
            'message' => 'Paciente registrado correctamente.'
        ];
    } catch (Exception $e) {
        logError("Error en insertarPaciente: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al registrar el paciente: ' . $e->getMessage()
        ];
    }
}

/**
 * Actualiza los datos de un paciente
 * 
 * @param array $paciente Datos del paciente
 * @return array Resultado de la operación
 */
function actualizarPaciente($paciente) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar si existe el paciente
        $existe = $db->queryValue("SELECT COUNT(*) FROM FIDE_PACIENTES_TB WHERE FIDE_PACIENTE_CEDULA = :cedula", 
                                ['cedula' => $paciente['cedula']]);
        
        if ($existe == 0) {
            return [
                'success' => false,
                'message' => 'No existe un paciente con esta cédula.'
            ];
        }
        
        // Ejecutar la actualización
        $db->query("UPDATE FIDE_PACIENTES_TB SET 
                      FIDE_NOMBRE_PACIENTE = :nombre,
                      FIDE_APELLIDOS_PACIENTE = :apellidos,
                      FIDE_TELEFONO_PACIENTE = :telefono,
                      FIDE_DIRECCION_PACIENTE = :direccion,
                      FIDE_CORREO_PACIENTE = :correo,
                      FIDE_ESTADO_PACIENTE_ID = :estado_id
                  WHERE FIDE_PACIENTE_CEDULA = :cedula", [
                      'cedula' => $paciente['cedula'],
                      'nombre' => $paciente['nombre'],
                      'apellidos' => $paciente['apellidos'],
                      'telefono' => $paciente['telefono'],
                      'direccion' => $paciente['direccion'],
                      'correo' => $paciente['correo'],
                      'estado_id' => $paciente['estado_id']
                  ]);
        
        return [
            'success' => true,
            'message' => 'Paciente actualizado correctamente.'
        ];
    } catch (Exception $e) {
        logError("Error en actualizarPaciente: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al actualizar el paciente: ' . $e->getMessage()
        ];
    }
}

/**
 * Elimina un paciente (solo si no tiene historial médico, citas, etc.)
 * 
 * @param string $cedula Cédula del paciente
 * @return array Resultado de la operación
 */
function eliminarPaciente($cedula) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Comprobar si el paciente tiene historial, citas, facturas, etc.
        $tieneHistorial = $db->queryValue("SELECT COUNT(*) FROM FIDE_HISTORIAL_MEDICO_TB WHERE FIDE_PACIENTE_CEDULA = :cedula", 
                                        ['cedula' => $cedula]);
        
        if ($tieneHistorial > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar el paciente porque tiene historial médico asociado.'
            ];
        }
        
        $tieneCitas = $db->queryValue("SELECT COUNT(*) FROM FIDE_CITAS_TB WHERE FIDE_PACIENTE_CEDULA = :cedula", 
                                    ['cedula' => $cedula]);
        
        if ($tieneCitas > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar el paciente porque tiene citas asociadas.'
            ];
        }
        
        $tieneFacturas = $db->queryValue("SELECT COUNT(*) FROM FIDE_FACTURAS_TB WHERE FIDE_PACIENTE_CEDULA = :cedula", 
                                       ['cedula' => $cedula]);
        
        if ($tieneFacturas > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar el paciente porque tiene facturas asociadas.'
            ];
        }
        
        $tieneHospitalizaciones = $db->queryValue("SELECT COUNT(*) FROM FIDE_HOSPITALIZACIONES_TB WHERE FIDE_PACIENTE_CEDULA = :cedula", 
                                                ['cedula' => $cedula]);
        
        if ($tieneHospitalizaciones > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar el paciente porque tiene hospitalizaciones asociadas.'
            ];
        }
        
        // Si no tiene relaciones, eliminarlo
        $db->query("DELETE FROM FIDE_PACIENTES_TB WHERE FIDE_PACIENTE_CEDULA = :cedula", 
                  ['cedula' => $cedula]);
        
        return [
            'success' => true,
            'message' => 'Paciente eliminado correctamente.'
        ];
    } catch (Exception $e) {
        logError("Error en eliminarPaciente: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al eliminar el paciente: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtiene el historial médico de un paciente
 * 
 * @param string $cedula Cédula del paciente
 * @return array Historial médico del paciente
 */
function obtenerHistorialMedico($cedula) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        $query = "SELECT h.FIDE_HISTORIAL_ID, h.FIDE_FECHA_REGISTRO, 
                         h.FIDE_DIAGNOSTICO, h.FIDE_TRATAMIENTO, h.FIDE_OBSERVACIONES,
                         e.FIDE_NOMBRE_EMPLEADO || ' ' || e.FIDE_APELLIDOS_EMPLEADO AS NOMBRE_DOCTOR
                  FROM FIDE_HISTORIAL_MEDICO_TB h
                  JOIN FIDE_EMPLEADOS_TB e ON h.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
                  WHERE h.FIDE_PACIENTE_CEDULA = :cedula
                  ORDER BY h.FIDE_FECHA_REGISTRO DESC";
        
        return $db->query($query, ['cedula' => $cedula]);
    } catch (Exception $e) {
        logError("Error en obtenerHistorialMedico: " . $e->getMessage());
        return [];
    }
}

/**
 * Registra una nueva entrada en el historial médico de un paciente
 * 
 * @param array $historial Datos del historial
 * @return array Resultado de la operación
 */
function registrarHistorialMedico($historial) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Ejecutar la inserción
        $db->query("INSERT INTO FIDE_HISTORIAL_MEDICO_TB (
                      FIDE_PACIENTE_CEDULA, FIDE_EMPLEADO_CEDULA, FIDE_FECHA_REGISTRO,
                      FIDE_DIAGNOSTICO, FIDE_TRATAMIENTO, FIDE_OBSERVACIONES
                  ) VALUES (
                      :paciente_cedula, :empleado_cedula, SYSTIMESTAMP,
                      :diagnostico, :tratamiento, :observaciones
                  )", [
                      'paciente_cedula' => $historial['paciente_cedula'],
                      'empleado_cedula' => $historial['empleado_cedula'],
                      'diagnostico' => $historial['diagnostico'],
                      'tratamiento' => $historial['tratamiento'],
                      'observaciones' => $historial['observaciones']
                  ]);
        
        return [
            'success' => true,
            'message' => 'Historial médico registrado correctamente.'
        ];
    } catch (Exception $e) {
        logError("Error en registrarHistorialMedico: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al registrar el historial médico: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtiene los pacientes con deuda
 * 
 * @param float $deudaMinima Deuda mínima para filtrar
 * @return array Lista de pacientes con deuda
 */
function obtenerPacientesConDeuda($deudaMinima = 0) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        $query = "SELECT p.FIDE_PACIENTE_CEDULA, p.FIDE_NOMBRE_PACIENTE, p.FIDE_APELLIDOS_PACIENTE, 
                         p.FIDE_TELEFONO_PACIENTE, p.FIDE_CORREO_PACIENTE, p.FIDE_DEUDA_PACIENTE,
                         e.FIDE_DESCRIPCION_ESTADO_PACIENTE
                  FROM FIDE_PACIENTES_TB p
                  JOIN FIDE_ESTADOS_PACIENTES_TB e ON p.FIDE_ESTADO_PACIENTE_ID = e.FIDE_ESTADO_PACIENTE_ID
                  WHERE p.FIDE_DEUDA_PACIENTE > :deuda_minima
                  ORDER BY p.FIDE_DEUDA_PACIENTE DESC";
        
        return $db->query($query, ['deuda_minima' => $deudaMinima]);
    } catch (Exception $e) {
        logError("Error en obtenerPacientesConDeuda: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los pacientes hospitalizados
 * 
 * @return array Lista de pacientes hospitalizados
 */
function obtenerPacientesHospitalizados() {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        $query = "SELECT h.FIDE_HOSPITALIZACION_ID, h.FIDE_FECHA_INGRESO, h.FIDE_MOTIVO_INGRESO,
                         h.FIDE_DIAGNOSTICO_INGRESO, h.FIDE_ESTADO,
                         p.FIDE_PACIENTE_CEDULA, p.FIDE_NOMBRE_PACIENTE, p.FIDE_APELLIDOS_PACIENTE, 
                         p.FIDE_TELEFONO_PACIENTE,
                         s.FIDE_SALA_ID,
                         e.FIDE_NOMBRE_EMPLEADO || ' ' || e.FIDE_APELLIDOS_EMPLEADO AS NOMBRE_DOCTOR
                  FROM FIDE_HOSPITALIZACIONES_TB h
                  JOIN FIDE_PACIENTES_TB p ON h.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
                  JOIN FIDE_SALAS_TB s ON h.FIDE_SALA_ID = s.FIDE_SALA_ID
                  JOIN FIDE_EMPLEADOS_TB e ON h.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
                  WHERE h.FIDE_ESTADO = 'ACTIVO'
                  ORDER BY h.FIDE_FECHA_INGRESO DESC";
        
        return $db->query($query);
    } catch (Exception $e) {
        logError("Error en obtenerPacientesHospitalizados: " . $e->getMessage());
        return [];
    }
}

/**
 * Registra una nueva hospitalización
 * 
 * @param array $hospitalizacion Datos de la hospitalización
 * @return array Resultado de la operación
 */
function registrarHospitalizacion($hospitalizacion) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Verificar disponibilidad de la sala
        $salaDisponible = $db->queryValue("
            SELECT COUNT(*) FROM FIDE_SALAS_TB 
            WHERE FIDE_SALA_ID = :sala_id 
            AND FIDE_ESTADO_SALA_ID = (
                SELECT FIDE_ESTADO_SALA_ID FROM FIDE_ESTADOS_SALAS_TB 
                WHERE FIDE_DESCRIPCION_ESTADO_SALA = 'DISPONIBLE'
            )", ['sala_id' => $hospitalizacion['sala_id']]
        );
        
        if ($salaDisponible == 0) {
            return [
                'success' => false,
                'message' => 'La sala seleccionada no está disponible.'
            ];
        }
        
        // Iniciar transacción
        $db->beginTransaction();
        
        // Insertar hospitalización
        $db->query("INSERT INTO FIDE_HOSPITALIZACIONES_TB (
                      FIDE_PACIENTE_CEDULA, FIDE_SALA_ID, FIDE_EMPLEADO_CEDULA,
                      FIDE_FECHA_INGRESO, FIDE_MOTIVO_INGRESO, FIDE_DIAGNOSTICO_INGRESO,
                      FIDE_ESTADO
                  ) VALUES (
                      :paciente_cedula, :sala_id, :empleado_cedula,
                      SYSTIMESTAMP, :motivo_ingreso, :diagnostico_ingreso,
                      'ACTIVO'
                  )", [
                      'paciente_cedula' => $hospitalizacion['paciente_cedula'],
                      'sala_id' => $hospitalizacion['sala_id'],
                      'empleado_cedula' => $hospitalizacion['empleado_cedula'],
                      'motivo_ingreso' => $hospitalizacion['motivo_ingreso'],
                      'diagnostico_ingreso' => $hospitalizacion['diagnostico_ingreso']
                  ]);
        
        // Actualizar estado de la sala
        $estadoOcupado = $db->queryValue("
            SELECT FIDE_ESTADO_SALA_ID FROM FIDE_ESTADOS_SALAS_TB 
            WHERE FIDE_DESCRIPCION_ESTADO_SALA = 'OCUPADO'
        ");
        
        $db->query("UPDATE FIDE_SALAS_TB SET FIDE_ESTADO_SALA_ID = :estado_id WHERE FIDE_SALA_ID = :sala_id", [
            'estado_id' => $estadoOcupado,
            'sala_id' => $hospitalizacion['sala_id']
        ]);
        
        // Confirmar transacción
        $db->commit();
        
        return [
            'success' => true,
            'message' => 'Hospitalización registrada correctamente.'
        ];
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        $db->rollback();
        
        logError("Error en registrarHospitalizacion: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al registrar la hospitalización: ' . $e->getMessage()
        ];
    }
}

/**
 * Registra el alta de un paciente hospitalizado
 * 
 * @param int $hospitalizacionId ID de la hospitalización
 * @param string $observaciones Observaciones del alta
 * @return array Resultado de la operación
 */
function registrarAltaHospitalizacion($hospitalizacionId, $observaciones) {
    try {
        global $db_config;
        $db = Database::getInstance($db_config);
        
        // Obtener datos de la hospitalización
        $hospitalizacion = $db->queryOne("
            SELECT FIDE_SALA_ID, FIDE_PACIENTE_CEDULA, FIDE_ESTADO
            FROM FIDE_HOSPITALIZACIONES_TB
            WHERE FIDE_HOSPITALIZACION_ID = :id
        ", ['id' => $hospitalizacionId]);
        
        if (!$hospitalizacion) {
            return [
                'success' => false,
                'message' => 'No se encontró la hospitalización especificada.'
            ];
        }
        
        if ($hospitalizacion['FIDE_ESTADO'] !== 'ACTIVO') {
            return [
                'success' => false,
                'message' => 'La hospitalización ya ha sido dada de alta.'
            ];
        }
        
        // Iniciar transacción
        $db->beginTransaction();
        
        // Actualizar hospitalización
        $db->query("UPDATE FIDE_HOSPITALIZACIONES_TB SET
                      FIDE_FECHA_ALTA = SYSTIMESTAMP,
                      FIDE_ESTADO = 'FINALIZADO',
                      FIDE_OBSERVACIONES = :observaciones
                  WHERE FIDE_HOSPITALIZACION_ID = :id", 
                  [
                      'observaciones' => $observaciones,
                      'id' => $hospitalizacionId
                  ]);
        
        // Actualizar estado de la sala
        $estadoDisponible = $db->queryValue("
            SELECT FIDE_ESTADO_SALA_ID FROM FIDE_ESTADOS_SALAS_TB 
            WHERE FIDE_DESCRIPCION_ESTADO_SALA = 'DISPONIBLE'
        ");
        
        $db->query("UPDATE FIDE_SALAS_TB SET FIDE_ESTADO_SALA_ID = :estado_id WHERE FIDE_SALA_ID = :sala_id", [
            'estado_id' => $estadoDisponible,
            'sala_id' => $hospitalizacion['FIDE_SALA_ID']
        ]);
        
        // Confirmar transacción
        $db->commit();
        
        return [
            'success' => true,
            'message' => 'Alta registrada correctamente.'
        ];
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        $db->rollback();
        
        logError("Error en registrarAltaHospitalizacion: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al registrar el alta: ' . $e->getMessage()
        ];
    }
}