<?php
/**
 * Editar/Reprogramar Cita
 * Sistema de Gestión Hospitalaria Pegasus
 */



// Incluir archivos necesarios

require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/citas.php';
require_once '../../models/pacientes.php';
require_once '../../models/empleados.php';
require_once '../../models/salas.php';


require_once INCLUDES_DIR . '/Database.php';
require_once INCLUDES_DIR . '/functions.php';
require_once MODELS_DIR . '/pacientes.php';

// Iniciar sesión y verificar permisos
session_start();
if (!isLoggedIn()) {
    redirect('login.php');
}
// Título de la página
$pageTitle = 'Editar Cita';

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

// Obtener datos de la cita
$query = "SELECT 
            c.FIDE_CITA_ID, 
            c.FIDE_PACIENTE_CEDULA, 
            c.FIDE_EMPLEADO_CEDULA, 
            c.FIDE_FECHA_CITA, 
            c.FIDE_MOTIVO_CITA,
            c.FIDE_ESTADO_CITA,
            c.FIDE_SALA_ID,
            p.FIDE_NOMBRE_PACIENTE,
            p.FIDE_APELLIDOS_PACIENTE,
            e.FIDE_NOMBRE_EMPLEADO,
            e.FIDE_APELLIDOS_EMPLEADO
        FROM 
            FIDE_CITAS_TB c
        JOIN 
            FIDE_PACIENTES_TB p ON c.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
        JOIN 
            FIDE_EMPLEADOS_TB e ON c.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
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

// Si la cita está cancelada o completada, no se puede editar
if ($cita['FIDE_ESTADO_CITA'] !== 'ACTIVA') {
    showError("No se puede editar una cita que no está activa");
    redirect('pages/citas/index.php');
}

// Preparar fecha y hora para el formulario
$fecha_hora = date_create_from_format('Y-m-d H:i:s', $cita['FIDE_FECHA_CITA']);
$fecha = $fecha_hora ? $fecha_hora->format('Y-m-d') : date('Y-m-d');
$hora = $fecha_hora ? $fecha_hora->format('H:i') : date('H:i');

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_fecha = sanitizeInput($_POST['fecha']);
    $nueva_hora = sanitizeInput($_POST['hora']);
    $fecha_cita = $nueva_fecha . ' ' . $nueva_hora . ':00'; // Formato YYYY-MM-DD HH:MI:SS
    
    // Validaciones
    $errors = [];
    
    if (empty($nueva_fecha) || empty($nueva_hora)) {
        $errors[] = "La fecha y hora son obligatorias";
    }
    
    // Verificar disponibilidad de la sala (si cambió la fecha/hora)
    if (empty($errors) && ($nueva_fecha . ' ' . $nueva_hora) != (date('Y-m-d', strtotime($cita['FIDE_FECHA_CITA'])) . ' ' . date('H:i', strtotime($cita['FIDE_FECHA_CITA'])))) {
        $disponible = verificarDisponibilidadSala($cita['FIDE_SALA_ID'], $fecha_cita);
        if (!$disponible) {
            $errors[] = "La sala seleccionada no está disponible en la fecha y hora indicadas";
        }
    }
    
    // Si no hay errores, reprogramar la cita
    if (empty($errors)) {
        $resultado = reprogramarCita($cita_id, $fecha_cita);
        
        if ($resultado) {
            showSuccess("Cita reprogramada correctamente");
            redirect('pages/citas/index.php');
        }
    } else {
        // Mostrar errores
        foreach ($errors as $error) {
            showError($error);
        }
    }
}

// Obtener listas para los selects
$pacientes = obtenerTodosPacientes();
$empleados = obtenerEmpleadosActivos();
$salas = obtenerSalasDisponibles();

// Incluir el encabezado
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?php echo $pageTitle; ?></h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
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

    <!-- Información de la cita -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Información de la Cita</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Paciente:</strong> <?php echo $cita['FIDE_NOMBRE_PACIENTE'] . ' ' . $cita['FIDE_APELLIDOS_PACIENTE']; ?></p>
                    <p><strong>Médico:</strong> <?php echo $cita['FIDE_NOMBRE_EMPLEADO'] . ' ' . $cita['FIDE_APELLIDOS_EMPLEADO']; ?></p>
                    <p><strong>Motivo:</strong> <?php echo $cita['FIDE_MOTIVO_CITA']; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha actual:</strong> <?php echo formatDate($cita['FIDE_FECHA_CITA']); ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="badge bg-success">Activa</span>
                    </p>
                    <p><strong>ID Cita:</strong> <?php echo $cita['FIDE_CITA_ID']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de reprogramación -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Reprogramar Cita</h5>
        </div>
        <div class="card-body">
            <form action="" method="post" class="row g-3">
                <!-- Fecha y Hora -->
                <div class="col-md-6">
                    <label for="fecha" class="form-label">Nueva Fecha *</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo $fecha; ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="hora" class="form-label">Nueva Hora *</label>
                    <input type="time" class="form-control" id="hora" name="hora" required value="<?php echo $hora; ?>">
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Reprogramar Cita
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cuando cambian fecha/hora, verificar disponibilidad
    const fecha = document.getElementById('fecha');
    const hora = document.getElementById('hora');
    
    function verificarDisponibilidad() {
        const fechaVal = fecha.value;
        const horaVal = hora.value;
        const salaId = <?php echo $cita['FIDE_SALA_ID']; ?>;
        
        if (fechaVal && horaVal) {
            // Aquí se podría agregar una verificación AJAX de la disponibilidad
            // mostrando un mensaje al usuario sin necesidad de enviar el formulario
            console.log('Verificando disponibilidad para:', fechaVal, horaVal, salaId);
        }
    }
    
    fecha.addEventListener('change', verificarDisponibilidad);
    hora.addEventListener('change', verificarDisponibilidad);
});
</script>

<?php include '../../includes/footer.php'; ?>