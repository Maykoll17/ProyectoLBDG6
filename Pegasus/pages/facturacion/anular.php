<?php
/**
 * Anular Factura
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
    showError("Solo se pueden anular facturas pendientes");
    redirect('pages/facturacion/detalles.php?id=' . $factura_id);
}

// Anular la factura
$resultado = anularFactura($factura_id);

if ($resultado) {
    showSuccess("Factura anulada correctamente");
} else {
    showError("No se pudo anular la factura");
}

// Redireccionar a la lista de facturas
redirect('pages/facturacion/index.php');
?>