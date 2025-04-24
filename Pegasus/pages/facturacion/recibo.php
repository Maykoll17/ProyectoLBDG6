<?php
/**
 * Recibo de Pago
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

// Título de la página
$pageTitle = 'Recibo de Pago';

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

// Verificar que la factura está cobrada
if ($factura['FIDE_ESTADO_FACTURA'] !== 'COBRADO' || empty($recibos)) {
    showError("No hay recibos disponibles para esta factura");
    redirect('pages/facturacion/detalles.php?id=' . $factura_id);
}

// Incluir el encabezado
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?php echo $pageTitle; ?></h1>
        <div>
            <button onclick="window.print();" class="btn btn-primary me-2">
                <i class="fas fa-print"></i> Imprimir Recibo
            </button>
            <a href="detalles.php?id=<?php echo $factura_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <?php
    // Mostrar mensajes
    if ($error_msg = getMessage('error')) {
        echo '<div class="alert alert-danger no-print">' . $error_msg . '</div>';
    }
    if ($success_msg = getMessage('success')) {
        echo '<div class="alert alert-success no-print">' . $success_msg . '</div>';
    }
    ?>

    <!-- Recibo para Imprimir -->
    <div class="card recibo-impresion">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <img src="../../assets/img/logo.png" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    <h4>Hospital Pegasus</h4>
                    <p>Servicio de Salud de Calidad</p>
                    <p>Teléfono: (506) 2222-3333</p>
                    <p>Email: info@hospitalpegasus.com</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h2 class="text-primary">RECIBO DE PAGO</h2>
                    <p><strong>Factura #:</strong> <?php echo $factura_id; ?></p>
                    <p><strong>Fecha:</strong> <?php echo formatDate($recibos[0]['FIDE_FECHA_PAGO']); ?></p>
                    <p><strong>Estado:</strong> <span class="badge bg-success">PAGADO</span></p>
                </div>
            </div>

            <hr>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Información del Paciente</h5>
                    <p><strong>Nombre:</strong> <?php echo $factura['FIDE_NOMBRE_PACIENTE'] . ' ' . $factura['FIDE_APELLIDOS_PACIENTE']; ?></p>
                    <p><strong>Cédula:</strong> <?php echo $factura['FIDE_PACIENTE_CEDULA']; ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Información de Pago</h5>
                    <p><strong>Método de Pago:</strong> <?php echo $recibos[0]['FIDE_METODO_PAGO']; ?></p>
                    <?php if (!empty($recibos[0]['FIDE_REFERENCIA_PAGO'])): ?>
                        <p><strong>Referencia:</strong> <?php echo $recibos[0]['FIDE_REFERENCIA_PAGO']; ?></p>
                    <?php endif; ?>
                    <p><strong>Monto Pagado:</strong> <?php echo formatCurrency($recibos[0]['FIDE_MONTO_PAGADO'], '₡'); ?></p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
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
                        <tr>
                            <td colspan="2" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end"><?php echo formatCurrency($subtotal, '₡'); ?></td>
                        </tr>
                        
                        <!-- Descuento si aplica -->
                        <?php if ($factura['FIDE_PORCENTAJE_APLICADO'] > 0): 
                            $descuento = $subtotal * ($factura['FIDE_PORCENTAJE_APLICADO'] / 100);
                        ?>
                            <tr>
                                <td colspan="2" class="text-end"><strong>Descuento (<?php echo $factura['FIDE_PORCENTAJE_APLICADO']; ?>%):</strong></td>
                                <td class="text-end">-<?php echo formatCurrency($descuento, '₡'); ?></td>
                            </tr>
                        <?php endif; ?>
                        
                        <!-- Total Final -->
                        <tr>
                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end"><strong><?php echo formatCurrency($factura['FIDE_TOTAL_FACTURA'], '₡'); ?></strong></td>
                        </tr>
                        
                        <!-- Pago -->
                        <tr>
                            <td colspan="2" class="text-end"><strong>Pago Recibido:</strong></td>
                            <td class="text-end"><?php echo formatCurrency($recibos[0]['FIDE_MONTO_PAGADO'], '₡'); ?></td>
                        </tr>
                        
                        <!-- Cambio si aplica -->
                        <?php if ($recibos[0]['FIDE_MONTO_PAGADO'] > $factura['FIDE_TOTAL_FACTURA']): 
                            $cambio = $recibos[0]['FIDE_MONTO_PAGADO'] - $factura['FIDE_TOTAL_FACTURA'];
                        ?>
                            <tr>
                                <td colspan="2" class="text-end"><strong>Cambio:</strong></td>
                                <td class="text-end"><?php echo formatCurrency($cambio, '₡'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h5>Términos y Condiciones</h5>
                    <p>Este recibo es prueba de pago por los servicios prestados. Gracias por su preferencia.</p>
                </div>
                <div class="col-md-4 text-center">
                    <hr class="mt-5">
                    <p>Firma Autorizada</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background-color: #fff;
        font-size: 12pt;
    }
    
    .container {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
    }
    
    .no-print, .no-print * {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .recibo-impresion {
        padding: 0;
    }
    
    header, footer, .btn, nav {
        display: none !important;
    }
    
    .badge {
        border: 1px solid #000;
        color: #000 !important;
        background-color: transparent !important;
    }
    
    .text-primary {
        color: #000 !important;
    }
    
    @page {
        margin: 1cm;
    }
}
</style>

<?php include '../../includes/footer.php'; ?>