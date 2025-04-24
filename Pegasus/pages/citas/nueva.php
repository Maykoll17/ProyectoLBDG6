<?php
/**
 * Registrar Nueva Cita
 * Sistema de Gestión Hospitalaria Pegasus
 */


// Definir la ruta base
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));


require_once '../../includes/config.php';
require_once INCLUDES_DIR . '/Database.php';
require_once INCLUDES_DIR . '/functions.php';
require_once MODELS_DIR . '/pacientes.php';

// Verificar si el usuario está logueado
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Incluir modelos
require_once BASE_PATH . '/models/citas.php';
require_once BASE_PATH . '/models/pacientes.php';
require_once BASE_PATH . '/models/empleados.php';
require_once BASE_PATH . '/models/salas.php';

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/citas.php';
require_once '../../models/pacientes.php';
require_once '../../models/empleados.php';
require_once '../../models/salas.php';

// Título de la página
$pageTitle = 'Registrar Nueva Cita';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_cedula = sanitizeInput($_POST['paciente_cedula']);
    $empleado_cedula = sanitizeInput($_POST['empleado_cedula']);
    $fecha = sanitizeInput($_POST['fecha']);
    $hora = sanitizeInput($_POST['hora']);
    $fecha_cita = $fecha . ' ' . $hora . ':00'; // Formato YYYY-MM-DD HH:MI:SS
    $sala_id = (int)sanitizeInput($_POST['sala_id']);
    $motivo_cita = sanitizeInput($_POST['motivo_cita']);
    
    // Validaciones
    $errors = [];
    
    if (empty($paciente_cedula)) {
        $errors[] = "El paciente es obligatorio";
    }
    
    if (empty($empleado_cedula)) {
        $errors[] = "El médico es obligatorio";
    }
    
    if (empty($fecha) || empty($hora)) {
        $errors[] = "La fecha y hora son obligatorias";
    }
    
    if ($sala_id <= 0) {
        $errors[] = "Debe seleccionar una sala";
    }
    
    if (empty($motivo_cita)) {
        $errors[] = "El motivo de la cita es obligatorio";
    }
    
    // Verificar disponibilidad de la sala
    if (empty($errors)) {
        $disponible = verificarDisponibilidadSala($sala_id, $fecha_cita);
        if (!$disponible) {
            $errors[] = "La sala seleccionada no está disponible en la fecha y hora indicadas";
        }
    }
    
    // Si no hay errores, registrar la cita
    if (empty($errors)) {
        $resultado = agendarCita($paciente_cedula, $empleado_cedula, $fecha_cita, $sala_id, $motivo_cita);
        
        if ($resultado) {
            // Redirigir a la lista de citas
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

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Datos de la Cita</h5>
        </div>
        <div class="card-body">
            <form action="" method="post" class="row g-3">
                <!-- Paciente -->
                <div class="col-md-6">
                    <label for="paciente_cedula" class="form-label">Paciente *</label>
                    <select name="paciente_cedula" id="paciente_cedula" class="form-select" required>
                        <option value="">Seleccione un paciente</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?php echo $paciente['FIDE_PACIENTE_CEDULA']; ?>">
                                <?php echo $paciente['FIDE_PACIENTE_CEDULA'] . ' - ' . $paciente['FIDE_NOMBRE_PACIENTE'] . ' ' . $paciente['FIDE_APELLIDOS_PACIENTE']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Médico -->
                <div class="col-md-6">
                    <label for="empleado_cedula" class="form-label">Médico *</label>
                    <select name="empleado_cedula" id="empleado_cedula" class="form-select" required>
                        <option value="">Seleccione un médico</option>
                        <?php foreach ($empleados as $empleado): ?>
                            <option value="<?php echo $empleado['FIDE_EMPLEADO_CEDULA']; ?>">
                                <?php echo $empleado['FIDE_EMPLEADO_CEDULA'] . ' - ' . $empleado['FIDE_NOMBRE_EMPLEADO'] . ' ' . $empleado['FIDE_APELLIDOS_EMPLEADO']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Fecha y Hora -->
                <div class="col-md-3">
                    <label for="fecha" class="form-label">Fecha *</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="hora" class="form-label">Hora *</label>
                    <input type="time" class="form-control" id="hora" name="hora" required>
                </div>
                
                <!-- Sala -->
                <div class="col-md-6">
                    <label for="sala_id" class="form-label">Sala *</label>
                    <select name="sala_id" id="sala_id" class="form-select" required>
                        <option value="">Seleccione una sala</option>
                        <?php foreach ($salas as $sala): ?>
                            <option value="<?php echo $sala['FIDE_SALA_ID']; ?>">
                                <?php echo $sala['FIDE_SALA_ID'] . ' - ' . $sala['FIDE_DESCRIPCION_TIPO_SALA']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Motivo -->
                <div class="col-md-12">
                    <label for="motivo_cita" class="form-label">Motivo de la Cita *</label>
                    <textarea class="form-control" id="motivo_cita" name="motivo_cita" rows="3" required></textarea>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cita
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
    // Cuando cambian fecha/hora/sala, verificar disponibilidad
    const fecha = document.getElementById('fecha');
    const hora = document.getElementById('hora');
    const sala = document.getElementById('sala_id');
    
    function verificarDisponibilidad() {
        const fechaVal = fecha.value;
        const horaVal = hora.value;
        const salaVal = sala.value;
        
        if (fechaVal && horaVal && salaVal) {
            // Aquí se podría agregar una verificación AJAX de la disponibilidad
            // mostrando un mensaje al usuario sin necesidad de enviar el formulario
            console.log('Verificando disponibilidad para:', fechaVal, horaVal, salaVal);
        }
    }
    
    fecha.addEventListener('change', verificarDisponibilidad);
    hora.addEventListener('change', verificarDisponibilidad);
    sala.addEventListener('change', verificarDisponibilidad);
});
</script>

<?php include '../../includes/footer.php'; ?>