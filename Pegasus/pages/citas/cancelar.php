<?php
/**
 * Cancelar Cita
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Iniciar sesión y verificar permisos
session_start();
if (!isLoggedIn()) {
    redirect('login.php');
}

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/citas.php';

// Verificar que existe el id de la cita
if (!isset($_GET['id']) || empty($_GET['id'])) {
    showError("ID de cita no especificado");
    redirect('pages/citas/index.php');
}

$cita_id = (int)$_GET['id'];

// Obtener la conexión a la base de datos
global $db_config;
$db = Database::getInstance($db_config);
$conn = $db->getConnection();

// Verificar si la cita existe y está activa
$query = "SELECT 
            c.FIDE_CITA_ID, 
            c.FIDE_ESTADO_CITA
        FROM 
            FIDE_CITAS_TB c
        WHERE 
            c.FIDE_CITA_ID = :cita_id";

$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ":cita_id", $cita_id);
oci_execute($stmt);
$cita = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

if (!$cita) {
    showError("Cita no encontrada");
    redirect('pages/citas/index.php');
}

if ($cita['FIDE_ESTADO_CITA'] !== 'ACTIVA') {
    showError("No se puede cancelar una cita que no está activa");
    redirect('pages/citas/index.php');
}

// Cancelar la cita
$resultado = cancelarCita($cita_id);

if ($resultado) {
    showSuccess("Cita cancelada correctamente");
} else {
    showError("No se pudo cancelar la cita");
}

// Redireccionar a la lista de citas
redirect('pages/citas/index.php');
?>