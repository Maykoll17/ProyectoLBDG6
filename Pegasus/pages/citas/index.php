<?php
/**
 * Listado de Citas
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Definir la ruta base
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));

// Incluir archivos necesarios
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/Database.php';
require_once BASE_PATH . '/models/citas.php';
require_once BASE_PATH . '/models/empleados.php';

// Iniciar sesión y verificar permisos
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Título de la página
$pageTitle = 'Listado de Citas';

// Filtros
$estado = isset($_GET['estado']) ? $_GET['estado'] : 'ACTIVA';
$fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

// Obtener las citas según los filtros
if ($estado === 'HOY') {
    $citas = obtenerCitasHoy();
} else {
    // Convertir fechas a formato SQL si están presentes
    $fecha_sql_desde = !empty($fecha_desde) ? dateToSQL($fecha_desde) : null;
    $fecha_sql_hasta = !empty($fecha_hasta) ? dateToSQL($fecha_hasta) : null;
    
    // Obtener historial de citas
    $citas = obtenerHistorialCitas();
    
    // Filtrar según los criterios
    if ($estado !== 'TODAS') {
        $citas_filtradas = [];
        foreach ($citas as $cita) {
            if ($cita['FIDE_ESTADO_CITA'] === $estado) {
                $citas_filtradas[] = $cita;
            }
        }
        $citas = $citas_filtradas;
    }
    
    // Filtrar por fechas si están presentes
    if (!empty($fecha_sql_desde) || !empty($fecha_sql_hasta)) {
        $citas_filtradas = [];
        foreach ($citas as $cita) {
            $fecha_cita = date('Y-m-d', strtotime($cita['FIDE_FECHA_CITA']));
            
            $incluir = true;
            if (!empty($fecha_sql_desde) && $fecha_cita < $fecha_sql_desde) {
                $incluir = false;
            }
            if (!empty($fecha_sql_hasta) && $fecha_cita > $fecha_sql_hasta) {
                $incluir = false;
            }
            
            if ($incluir) {
                $citas_filtradas[] = $cita;
            }
        }
        $citas = $citas_filtradas;
    }
}

// Incluir el encabezado
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?php echo $pageTitle; ?></h1>
        <a href="nueva.php" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nueva Cita
        </a>
    </div>

    <?php
    // Mostrar mensajes
    if ($error_msg = getMessage('error')) {
        echo '<div class="alert alert-danger">' . $error_msg . '</div>';
    }
    if ($success_msg = getMessage('success')) {
        echo '<div class="alert alert-success">' . $success_msg . '</div>';
    }
    if ($info_msg = getMessage('info')) {
        echo '<div class="alert alert-info">' . $info_msg . '</div>';
    }
    ?>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="" method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="TODAS" <?php echo $estado === 'TODAS' ? 'selected' : ''; ?>>Todas</option>
                        <option value="ACTIVA" <?php echo $estado === 'ACTIVA' ? 'selected' : ''; ?>>Activas</option>
                        <option value="COMPLETADA" <?php echo $estado === 'COMPLETADA' ? 'selected' : ''; ?>>Completadas</option>
                        <option value="CANCELADA" <?php echo $estado === 'CANCELADA' ? 'selected' : ''; ?>>Canceladas</option>
                        <option value="HOY" <?php echo $estado === 'HOY' ? 'selected' : ''; ?>>Citas de Hoy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?php echo $fecha_desde; ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?php echo $fecha_hasta; ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Resetear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Citas -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Citas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($citas)): ?>
                <div class="alert alert-info">No se encontraron citas con los criterios seleccionados.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Fecha y Hora</th>
                                <th>Sala</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citas as $cita): ?>
                                <tr>
                                    <td><?php echo $cita['FIDE_CITA_ID']; ?></td>
                                    <td><?php echo $cita['FIDE_NOMBRE_PACIENTE'] . ' ' . $cita['FIDE_APELLIDOS_PACIENTE']; ?></td>
                                    <td><?php echo $cita['FIDE_NOMBRE_EMPLEADO'] . ' ' . $cita['FIDE_APELLIDOS_EMPLEADO']; ?></td>
                                    <td><?php echo formatDate($cita['FIDE_FECHA_CITA']); ?></td>
                                    <td><?php echo $cita['FIDE_DESCRIPCION_TIPO_SALA']; ?></td>
                                    <td><?php echo $cita['FIDE_MOTIVO_CITA']; ?></td>
                                    <td>
                                        <?php if ($cita['FIDE_ESTADO_CITA'] === 'ACTIVA'): ?>
                                            <span class="badge bg-success">Activa</span>
                                        <?php elseif ($cita['FIDE_ESTADO_CITA'] === 'COMPLETADA'): ?>
                                            <span class="badge bg-primary">Completada</span>
                                        <?php elseif ($cita['FIDE_ESTADO_CITA'] === 'CANCELADA'): ?>
                                            <span class="badge bg-danger">Cancelada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="editar.php?id=<?php echo $cita['FIDE_CITA_ID']; ?>" class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($cita['FIDE_ESTADO_CITA'] === 'ACTIVA'): ?>
                                                <a href="cancelar.php?id=<?php echo $cita['FIDE_CITA_ID']; ?>" class="btn btn-outline-danger" title="Cancelar" onclick="return confirm('¿Está seguro de cancelar esta cita?');">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>