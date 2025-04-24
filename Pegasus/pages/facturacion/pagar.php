<?php
/**
 * Registrar Pago de Factura
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/facturacion.php';

// Título de la página
$pageTitle = 'Registrar Pago de Factura';

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

// Verificar que la factura está pendiente
if ($factura['FIDE_ESTADO_FACTURA'] !== 'PENDIENTE') {
    showError("Solo se pueden pagar facturas pendientes");
    redirect('pages/facturacion/detalles.php?id=' . $factura_id);
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo_pago = sanitizeInput($_POST['metodo_pago']);
    $monto_pagado = (float)sanitizeInput($_POST['monto_pagado']);
    $referencia_pago = sanitizeInput($_POST['referencia_pago']);
    
    // Validaciones
    $errors = [];
    
    if (empty($metodo_pago)) {
        $errors[] = "El método de pago es obligatorio";
    }
    
    if (empty($monto_pagado) || $monto_pagado <= 0) {
        $errors[] = "El monto pagado debe ser mayor que cero";
    } elseif ($monto_pagado < $factura['FIDE_TOTAL_FACTURA']) {
        $errors[] = "El monto pagado debe ser al menos igual al total de la factura";
    }
    
    // Si es transferencia o tarjeta, la referencia es obligatoria
    if (($metodo_pago === 'TRANSFERENCIA' || $metodo_pago === 'TARJETA') && empty($referencia_pago)) {
        $errors[] = "La referencia del pago es obligatoria para transferencias y pagos con tarjeta";
    }
    
    // Si no hay errores, registrar el pago
    if (empty($errors)) {
        $resultado = pagarFactura($factura_id, $metodo_pago, $monto_pagado, $referencia_pago);
        
        if ($resultado) {
            showSuccess("Pago registrado correctamente");
            redirect('pages/facturacion/detalles.php?id=' . $factura_id);
        }
    } else {
        // Mostrar errores
        foreach ($errors as $error) {
            showError($error);
        }
    }
}

// Incluir el encabezado
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?php echo $pageTitle; ?> #<?php echo $factura_id; ?></h1>
        <a href="detalles.php?id=<?php echo $factura_id; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Detalles
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
    ?>

    <!-- Información de la Factura -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Información de la Factura</h5>
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
                        <span class="badge bg-warning text-dark">Pendiente</span>
                    </p>
                    
                    <?php if ($factura['FIDE_PORCENTAJE_APLICADO'] > 0): ?>
                        <p><strong>Descuento aplicado:</strong> <?php echo $factura['FIDE_PORCENTAJE_APLICADO']; ?>%</p>
                    <?php endif; ?>
                    
                    <p class="h4"><strong>Total a Pagar:</strong> <?php echo formatCurrency($factura['FIDE_TOTAL_FACTURA'], '₡'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Pago -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Registrar Pago</h5>
        </div>
        <div class="card-body">
            <form action="" method="post" class="row g-3">
                <!-- Método de Pago -->
                <div class="col-md-4">
                    <label for="metodo_pago" class="form-label">Método de Pago *</label>
                    <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                        <option value="">Seleccione un método</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="TARJETA">Tarjeta de Crédito/Débito</option>
                        <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                        <option value="OTRO">Otro</option>
                    </select>
                </div>
                
                <!-- Monto Pagado -->
                <div class="col-md-4">
                    <label for="monto_pagado" class="form-label">Monto Pagado *</label>
                    <div class="input-group">
                        <span class="input-group-text">₡</span>
                        <input type="number" step="0.01" min="<?php echo $factura['FIDE_TOTAL_FACTURA']; ?>" 
                               class="form-control" id="monto_pagado" name="monto_pagado" 
                               value="<?php echo $factura['FIDE_TOTAL_FACTURA']; ?>" required>
                    </div>
                    <div class="form-text">El monto debe ser al menos igual al total de la factura.</div>
                </div>
                
                <!-- Referencia de Pago -->
                <div class="col-md-4">
                    <label for="referencia_pago" class="form-label">Referencia de Pago</label>
                    <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" 
                           placeholder="# Autorización o transferencia">
                    <div class="form-text">Obligatorio para pagos con tarjeta o transferencia.</div>
                </div>
                
                <!-- Calculadora de Cambio -->
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">Calculadora de Cambio</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="total_factura" class="form-label">Total Factura</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₡</span>
                                        <input type="text" class="form-control" id="total_factura" 
                                               value="<?php echo $factura['FIDE_TOTAL_FACTURA']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="monto_recibido" class="form-label">Monto Recibido</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₡</span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="monto_recibido" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="cambio" class="form-label">Cambio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₡</span>
                                        <input type="text" class="form-control bg-light" id="cambio" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                    </button>
                    <a href="detalles.php?id=<?php echo $factura_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const metodoPago = document.getElementById('metodo_pago');
    const referenciaPago = document.getElementById('referencia_pago');
    const montoPagado = document.getElementById('monto_pagado');
    const totalFactura = document.getElementById('total_factura');
    const montoRecibido = document.getElementById('monto_recibido');
    const cambio = document.getElementById('cambio');
    
    // Actualizar valores de la calculadora cuando cambia el monto recibido
    montoRecibido.addEventListener('input', function() {
        const total = parseFloat(totalFactura.value) || 0;
        const recibido = parseFloat(this.value) || 0;
        const cambioDar = recibido > total ? recibido - total : 0;
        
        cambio.value = cambioDar.toFixed(2);
        
        // Si se usa la calculadora, también actualizamos el monto pagado
        montoPagado.value = recibido;
    });
    
    // Mostrar/ocultar campo de referencia según método de pago
    metodoPago.addEventListener('change', function() {
        if (this.value === 'TARJETA' || this.value === 'TRANSFERENCIA') {
            referenciaPago.parentElement.classList.add('required');
            referenciaPago.setAttribute('required', 'required');
        } else {
            referenciaPago.parentElement.classList.remove('required');
            referenciaPago.removeAttribute('required');
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>