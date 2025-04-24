<?php
/**
 * Detalles de Factura
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
$pageTitle = 'Detalles de Factura';

// Verificar que existe el id de la factura
if (!isset($_GET['id']) || empty($_GET['id'])) {
    showError("ID de factura no especificado");
    redirect('pages/facturacion/index.php');
}

$factura_id = (int)$_GET['id'];

// Obtener detalles de la factura
$detalles = obtenerDetallesFactura($factura_id);

if (empty($detalles) || empty($detalles['factura'])) {
    showError("Factura no encontrada");
    redirect('pages/facturacion/index.php');
}

// Extraer datos para facilitar el acceso
$factura = $detalles['factura'];
$items = $detalles['detalles'];
$recibos = $detalles['recibos'];

// Incluir el encabezado
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?php echo $pageTitle; ?> #<?php echo $factura_id; ?></h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            
            <?php if ($factura['FIDE_ESTADO_FACTURA'] === 'PENDIENTE'): ?>
                <a href="pagar.php?id=<?php echo $factura_id; ?>" class="btn btn-success me-2">
                    <i class="fas fa-money-bill-wave"></i> Registrar Pago
                </a>
                
                <a href="anular.php?id=<?php echo $factura_id; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de anular esta factura?');">
                    <i class="fas fa-ban"></i> Anular Factura
                </a>
            <?php elseif ($factura['FIDE_ESTADO_FACTURA'] === 'COBRADO'): ?>
                <a href="recibo.php?id=<?php echo $factura_id; ?>" class="btn btn-primary">
                    <i class="fas fa-file-invoice"></i> Ver Recibo
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Mostrar mensajes
    if ($error_msg = getMessage('error')) {
        echo '<div class="alert alert-danger">' . $error_msg . '</div>';
    }
    if ($success_msg = getMessage('success')) {
        echo '<div class="alert alert-success">' . $success_msg . '</div>';
    }
    ?>

    <!-- Información de la Factura -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Información General</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Factura #:</strong> <?php echo $factura_id; ?></p>
                    <p><strong>Paciente:</strong> <?php echo $factura['FIDE_NOMBRE_PACIENTE'] . ' ' . $factura['FIDE_APELLIDOS_PACIENTE']; ?></p>
                    <p><strong>Cédula:</strong> <?php echo $factura['FIDE_PACIENTE_CEDULA']; ?></p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Estado:</strong> 
                        <?php if ($factura['FIDE_ESTADO_FACTURA'] === 'PENDIENTE'): ?>
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        <?php elseif ($factura['FIDE_ESTADO_FACTURA'] === 'COBRADO'): ?>
                            <span class="badge bg-success">Cobrada</span>
                        <?php elseif ($factura['FIDE_ESTADO_FACTURA'] === 'ANULADA'): ?>
                            <span class="badge bg-danger">Anulada</span>
                        <?php endif; ?>
                    </p>
                    
                    <?php if ($factura['FIDE_PORCENTAJE_APLICADO'] > 0): ?>
                        <p><strong>Descuento aplicado:</strong> <?php echo $factura['FIDE_PORCENTAJE_APLICADO']; ?>%</p>
                    <?php endif; ?>
                    
                    <p class="h4"><strong>Total:</strong> <?php echo formatCurrency($factura['FIDE_TOTAL_FACTURA'], '₡'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Factura -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Detalle de Ítems</h5>
        </div>
        <div class="card-body">
            <?php if (empty($items)): ?>
                <div class="alert alert-info">No hay ítems registrados en esta factura.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Descripción</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotal = 0;
                            foreach ($items as $index => $item): 
                                $subtotal += $item['FIDE_MONTO_FACTURA'];
                            ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $item['FIDE_DESCRIPCION_FACTURA']; ?></td>
                                    <td class="text-end"><?php echo formatCurrency($item['FIDE_MONTO_FACTURA'], '₡'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <!-- Subtotal -->
                            <tr class="table-light">
                                <td colspan="2" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end"><?php echo formatCurrency($subtotal, '₡'); ?></td>
                            </tr>
                            
                            <!-- Descuento si aplica -->
                            <?php if ($factura['FIDE_PORCENTAJE_APLICADO'] > 0): 
                                $descuento = $subtotal * ($factura['FIDE_PORCENTAJE_APLICADO'] / 100);
                            ?>
                                <tr class="table-light">
                                    <td colspan="2" class="text-end"><strong>Descuento (<?php echo $factura['FIDE_PORCENTAJE_APLICADO']; ?>%):</strong></td>
                                    <td class="text-end">-<?php echo formatCurrency($descuento, '₡'); ?></td>
                                </tr>
                            <?php endif; ?>
                            
                            <!-- Total Final -->
                            <tr class="table-primary">
                                <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong><?php echo formatCurrency($factura['FIDE_TOTAL_FACTURA'], '₡'); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recibos de Pago -->
    <?php if (!empty($recibos)): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Recibos de Pago</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Método de Pago</th>
                                <th>Referencia</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_pagado = 0;
                            foreach ($recibos as $index => $recibo): 
                                $total_pagado += $recibo['FIDE_MONTO_PAGADO'];
                            ?>
                                <tr>
                                    <td><?php echo $recibo['FIDE_RECIBO_ID']; ?></td>
                                    <td><?php echo formatDate($recibo['FIDE_FECHA_PAGO']); ?></td>
                                    <td><?php echo $recibo['FIDE_METODO_PAGO']; ?></td>
                                    <td><?php echo $recibo['FIDE_REFERENCIA_PAGO'] ? $recibo['FIDE_REFERENCIA_PAGO'] : 'N/A'; ?></td>
                                    <td class="text-end"><?php echo formatCurrency($recibo['FIDE_MONTO_PAGADO'], '₡'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <!-- Total Pagado -->
                            <tr class="table-success">
                                <td colspan="4" class="text-end"><strong>Total Pagado:</strong></td>
                                <td class="text-end"><strong><?php echo formatCurrency($total_pagado, '₡'); ?></strong></td>
                            </tr>
                            
                            <!-- Cambio si aplica -->
                            <?php if ($total_pagado > $factura['FIDE_TOTAL_FACTURA']): 
                                $cambio = $total_pagado - $factura['FIDE_TOTAL_FACTURA'];
                            ?>
                                <tr class="table-light">
                                    <td colspan="4" class="text-end"><strong>Cambio:</strong></td>
                                    <td class="text-end"><?php echo formatCurrency($cambio, '₡'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>