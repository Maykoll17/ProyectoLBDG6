<?php
/**
 * Listado de Facturas
 * Sistema de Gestión Hospitalaria Pegasus
 */
// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once INCLUDES_DIR . '/Database.php';
require_once INCLUDES_DIR . '/functions.php';
require_once MODELS_DIR . '/pacientes.php';

// Iniciar sesión
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/facturacion.php';
require_once '../../models/pacientes.php';

// Título de la página
$pageTitle = 'Gestión de Facturación';

// Filtros
$estado = isset($_GET['estado']) ? sanitizeInput($_GET['estado']) : 'TODAS';
$paciente_cedula = isset($_GET['paciente']) ? sanitizeInput($_GET['paciente']) : '';

// Obtener el listado de facturas
$facturas = [];
if (!empty($paciente_cedula)) {
    $facturas = obtenerFacturasPaciente($paciente_cedula);
} else {
    $facturas = obtenerEstadoFacturacion();
    
    // Filtrar por estado si no es 'TODAS'
    if ($estado !== 'TODAS') {
        $facturas_filtradas = [];
        foreach ($facturas as $factura) {
            if ($factura['FIDE_ESTADO_FACTURA'] === $estado) {
                $facturas_filtradas[] = $factura;
            }
        }
        $facturas = $facturas_filtradas;
    }
}

// Incluir el encabezado
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?php echo $pageTitle; ?></h1>
        <a href="nueva.php" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nueva Factura
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
                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="TODAS" <?php echo $estado === 'TODAS' ? 'selected' : ''; ?>>Todas</option>
                        <option value="PENDIENTE" <?php echo $estado === 'PENDIENTE' ? 'selected' : ''; ?>>Pendientes</option>
                        <option value="COBRADO" <?php echo $estado === 'COBRADO' ? 'selected' : ''; ?>>Cobradas</option>
                        <option value="ANULADA" <?php echo $estado === 'ANULADA' ? 'selected' : ''; ?>>Anuladas</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="paciente" class="form-label">Paciente</label>
                    <select name="paciente" id="paciente" class="form-select">
                        <option value="">Todos los pacientes</option>
                        <?php 
                        $pacientes = obtenerTodosPacientes();
                        foreach ($pacientes as $paciente): 
                        ?>
                            <option value="<?php echo $paciente['FIDE_PACIENTE_CEDULA']; ?>" <?php echo ($paciente_cedula == $paciente['FIDE_PACIENTE_CEDULA']) ? 'selected' : ''; ?>>
                                <?php echo $paciente['FIDE_PACIENTE_CEDULA'] . ' - ' . $paciente['FIDE_NOMBRE_PACIENTE'] . ' ' . $paciente['FIDE_APELLIDOS_PACIENTE']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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

    <!-- Listado de Facturas -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Facturas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($facturas)): ?>
                <div class="alert alert-info">No se encontraron facturas con los criterios seleccionados.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Detalle</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($facturas as $factura): ?>
                                <tr>
                                    <td><?php echo $factura['FIDE_FACTURA_ID']; ?></td>
                                    <td>
                                        <?php if (isset($factura['FIDE_NOMBRE_PACIENTE'])): ?>
                                            <?php echo $factura['FIDE_NOMBRE_PACIENTE'] . ' ' . $factura['FIDE_APELLIDOS_PACIENTE']; ?>
                                        <?php else: ?>
                                            <em>Paciente ID: <?php echo $factura['FIDE_PACIENTE_CEDULA']; ?></em>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatCurrency($factura['FIDE_TOTAL_FACTURA'], '₡'); ?></td>
                                    <td>
                                        <?php if ($factura['FIDE_ESTADO_FACTURA'] === 'PENDIENTE'): ?>
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php elseif ($factura['FIDE_ESTADO_FACTURA'] === 'COBRADO'): ?>
                                            <span class="badge bg-success">Cobrada</span>
                                        <?php elseif ($factura['FIDE_ESTADO_FACTURA'] === 'ANULADA'): ?>
                                            <span class="badge bg-danger">Anulada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if (isset($factura['NUM_DETALLES'])): ?>
                                                <i class="fas fa-list"></i> <?php echo $factura['NUM_DETALLES']; ?> ítem(s)
                                            <?php endif; ?>
                                            
                                            <?php if (isset($factura['RECIBOS_PAGOS']) && $factura['RECIBOS_PAGOS'] > 0): ?>
                                                <br><i class="fas fa-receipt"></i> <?php echo $factura['RECIBOS_PAGOS']; ?> recibo(s)
                                            <?php endif; ?>
                                            
                                            <?php if (isset($factura['FIDE_PORCENTAJE_APLICADO']) && $factura['FIDE_PORCENTAJE_APLICADO'] > 0): ?>
                                                <br><i class="fas fa-percent"></i> <?php echo $factura['FIDE_PORCENTAJE_APLICADO']; ?>% descuento
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="detalles.php?id=<?php echo $factura['FIDE_FACTURA_ID']; ?>" class="btn btn-outline-primary" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($factura['FIDE_ESTADO_FACTURA'] === 'PENDIENTE'): ?>
                                                <a href="pagar.php?id=<?php echo $factura['FIDE_FACTURA_ID']; ?>" class="btn btn-outline-success" title="Registrar pago">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </a>
                                                
                                                <a href="anular.php?id=<?php echo $factura['FIDE_FACTURA_ID']; ?>" class="btn btn-outline-danger" title="Anular factura" onclick="return confirm('¿Está seguro de anular esta factura?');">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($factura['FIDE_ESTADO_FACTURA'] === 'COBRADO'): ?>
                                                <a href="recibo.php?id=<?php echo $factura['FIDE_FACTURA_ID']; ?>" class="btn btn-outline-info" title="Ver recibo">
                                                    <i class="fas fa-file-invoice"></i>
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
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>