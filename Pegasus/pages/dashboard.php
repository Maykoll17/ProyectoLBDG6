<?php
/**
 * Página de dashboard
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Incluir archivos necesarios
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/Database.php';

// Comprobar si el usuario está autenticado
if (!isLoggedIn()) {
    redirect('/pages/login.php');
    exit();
}

// Obtener estadísticas para el dashboard
try {
    global $db_config;
    $db = Database::getInstance($db_config);
    
    // Total de pacientes
    $totalPacientes = $db->queryValue("SELECT COUNT(*) FROM FIDE_PACIENTES_TB");
    
    // Pacientes hospitalizados
    $pacientesHospitalizados = $db->queryValue("
        SELECT COUNT(*) FROM FIDE_HOSPITALIZACIONES_TB 
        WHERE FIDE_ESTADO = 'ACTIVO'
    ");
    
    // Citas para hoy
    $citasHoy = $db->queryValue("
        SELECT COUNT(*) FROM FIDE_CITAS_TB 
        WHERE TRUNC(FIDE_FECHA_CITA) = TRUNC(SYSDATE)
    ");
    
    // Medicamentos con stock bajo
    $medicamentosBajoStock = $db->queryValue("
        SELECT COUNT(*) FROM FIDE_MEDICAMENTOS_TB 
        WHERE FIDE_CANTIDAD_MEDICAMENTO < 10
    ");
    
    // Facturas pendientes
    $facturasPendientes = $db->queryValue("
        SELECT COUNT(*) FROM FIDE_FACTURAS_TB 
        WHERE FIDE_ESTADO_FACTURA = 'PENDIENTE'
    ");
    
    // Salas disponibles
    $salasDisponibles = $db->queryValue("
        SELECT COUNT(*) FROM FIDE_SALAS_TB 
        WHERE FIDE_ESTADO_SALA_ID = (
            SELECT FIDE_ESTADO_SALA_ID FROM FIDE_ESTADOS_SALAS_TB 
            WHERE FIDE_DESCRIPCION_ESTADO_SALA = 'DISPONIBLE'
        )
    ");
    
    // Citas próximas
    $citasProximas = $db->query("
        SELECT c.FIDE_CITA_ID, c.FIDE_FECHA_CITA, 
               p.FIDE_NOMBRE_PACIENTE || ' ' || p.FIDE_APELLIDOS_PACIENTE AS NOMBRE_PACIENTE,
               e.FIDE_NOMBRE_EMPLEADO || ' ' || e.FIDE_APELLIDOS_EMPLEADO AS NOMBRE_DOCTOR,
               c.FIDE_MOTIVO_CITA
        FROM FIDE_CITAS_TB c
        JOIN FIDE_PACIENTES_TB p ON c.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
        JOIN FIDE_EMPLEADOS_TB e ON c.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
        WHERE c.FIDE_FECHA_CITA > SYSDATE
        ORDER BY c.FIDE_FECHA_CITA ASC
        FETCH FIRST 5 ROWS ONLY
    ");
    
    // Últimas facturas
    $ultimasFacturas = $db->query("
        SELECT f.FIDE_FACTURA_ID, f.FIDE_TOTAL_FACTURA, f.FIDE_ESTADO_FACTURA,
               p.FIDE_NOMBRE_PACIENTE || ' ' || p.FIDE_APELLIDOS_PACIENTE AS NOMBRE_PACIENTE
        FROM FIDE_FACTURAS_TB f
        JOIN FIDE_PACIENTES_TB p ON f.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
        ORDER BY f.FIDE_FACTURA_ID DESC
        FETCH FIRST 5 ROWS ONLY
    ");
    
} catch (Exception $e) {
    logError("Error en dashboard: " . $e->getMessage());
    showError("Error al cargar los datos del dashboard. Por favor, contacte al administrador.");
}

// Título de la página
$pageTitle = 'Dashboard';

// Incluir el header
include_once dirname(__DIR__) . '/includes/header.php';
?>

<h1 class="mb-4">
    <i class="fas fa-tachometer-alt"></i> Dashboard
    <small class="text-muted fs-6">Bienvenido, <?php echo $_SESSION['username']; ?></small>
</h1>

<!-- Tarjetas de estadísticas -->
<div class="row mb-4">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Total de Pacientes</h6>
                        <h2 class="mb-0"><?php echo $totalPacientes; ?></h2>
                    </div>
                    <div class="dashboard-icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/pages/pacientes/index.php" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Pacientes Hospitalizados</h6>
                        <h2 class="mb-0"><?php echo $pacientesHospitalizados; ?></h2>
                    </div>
                    <div class="dashboard-icon text-warning">
                        <i class="fas fa-procedures"></i>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/pages/pacientes/hospitalizados.php" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Citas Hoy</h6>
                        <h2 class="mb-0"><?php echo $citasHoy; ?></h2>
                    </div>
                    <div class="dashboard-icon text-success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/pages/citas/index.php" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Medicamentos Bajo Stock</h6>
                        <h2 class="mb-0"><?php echo $medicamentosBajoStock; ?></h2>
                    </div>
                    <div class="dashboard-icon text-danger">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/pages/medicamentos/stock.php" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Facturas Pendientes</h6>
                        <h2 class="mb-0"><?php echo $facturasPendientes; ?></h2>
                    </div>
                    <div class="dashboard-icon text-info">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/pages/facturacion/index.php" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Salas Disponibles</h6>
                        <h2 class="mb-0"><?php echo $salasDisponibles; ?></h2>
                    </div>
                    <div class="dashboard-icon text-secondary">
                        <i class="fas fa-door-open"></i>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/pages/salas/disponibles.php" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<!-- Citas próximas y facturas recientes -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt"></i> Próximas Citas
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($citasProximas)): ?>
                <p class="text-muted">No hay citas próximas programadas.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Doctor</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citasProximas as $cita): ?>
                            <tr>
                                <td><?php echo formatDate($cita['FIDE_FECHA_CITA'], 'd/m/Y H:i'); ?></td>
                                <td><?php echo $cita['NOMBRE_PACIENTE']; ?></td>
                                <td><?php echo $cita['NOMBRE_DOCTOR']; ?></td>
                                <td><?php echo $cita['FIDE_MOTIVO_CITA']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <div class="text-end">
                    <a href="<?php echo BASE_URL; ?>/pages/citas/index.php" class="btn btn-sm btn-outline-primary">
                        Ver todas las citas <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice"></i> Últimas Facturas
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($ultimasFacturas)): ?>
                <p class="text-muted">No hay facturas registradas.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasFacturas as $factura): ?>
                            <tr>
                                <td><?php echo $factura['FIDE_FACTURA_ID']; ?></td>
                                <td><?php echo $factura['NOMBRE_PACIENTE']; ?></td>
                                <td><?php echo formatCurrency($factura['FIDE_TOTAL_FACTURA']); ?></td>
                                <td>
                                    <?php if ($factura['FIDE_ESTADO_FACTURA'] === 'PENDIENTE'): ?>
                                    <span class="badge bg-warning">Pendiente</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">Cobrada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <div class="text-end">
                    <a href="<?php echo BASE_URL; ?>/pages/facturacion/index.php" class="btn btn-sm btn-outline-info">
                        Ver todas las facturas <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include_once dirname(__DIR__) . '/includes/footer.php';
?>