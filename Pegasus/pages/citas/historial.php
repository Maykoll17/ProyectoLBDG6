<?php
/**
 * Historial de Citas
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
require_once '../../models/pacientes.php';

// Título de la página
$pageTitle = 'Historial de Citas';

// Verificar si se ha especificado un paciente
$paciente_cedula = isset($_GET['paciente']) ? sanitizeInput($_GET['paciente']) : '';
$paciente = null;

// Obtener el historial de citas
if (!empty($paciente_cedula)) {
    $paciente = buscarPaciente($paciente_cedula);
    if ($paciente) {
        $citas = obtenerHistorialCitasPaciente($paciente_cedula);
    } else {
        showError("Paciente no encontrado");
        $citas = [];
    }
} else {
    // Si no se especifica paciente, mostrar historial general
    $citas = obtenerHistorialCitas();
}

// Incluir el encabezado
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?php echo $pageTitle; ?></h1>
        <div>
            <a href="index.php" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Volver a Citas
            </a>
            <a href="nueva.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nueva Cita
            </a>
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

    <!-- Formulario de búsqueda por paciente -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Buscar Historial por Paciente</h5>
        </div>
        <div class="card-body">
            <form action="" method="get" class="row g-3">
                <div class="col-md-10">
                    <select name="paciente" id="paciente" class="form-select">
                        <option value="">Seleccione un paciente</option>
                        <?php 
                        $pacientes = obtenerTodosPacientes();
                        foreach ($pacientes as $p): 
                        ?>
                            <option value="<?php echo $p['FIDE_PACIENTE_CEDULA']; ?>" <?php echo ($paciente_cedula == $p['FIDE_PACIENTE_CEDULA']) ? 'selected' : ''; ?>>
                                <?php echo $p['FIDE_PACIENTE_CEDULA'] . ' - ' . $p['FIDE_NOMBRE_PACIENTE'] . ' ' . $p['FIDE_APELLIDOS_PACIENTE']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($paciente): ?>
    <!-- Información del paciente -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Información del Paciente</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cédula:</strong> <?php echo $paciente['FIDE_PACIENTE_CEDULA']; ?></p>
                    <p><strong>Nombre:</strong> <?php echo $paciente['FIDE_NOMBRE_PACIENTE'] . ' ' . $paciente['FIDE_APELLIDOS_PACIENTE']; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Teléfono:</strong> <?php echo $paciente['FIDE_TELEFONO_PACIENTE']; ?></p>
                    <p><strong>Estado:</strong> <?php echo $paciente['FIDE_DESCRIPCION_ESTADO_PACIENTE']; ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historial de Citas -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <?php echo !empty($paciente_cedula) ? 'Historial de Citas del Paciente' : 'Historial General de Citas'; ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($citas)): ?>
                <div class="alert alert-info">No se encontraron citas en el historial.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <?php if (empty($paciente_cedula)): ?>
                                <th>Paciente</th>
                                <?php endif; ?>
                                <th>Médico</th>
                                <th>Fecha y Hora</th>
                                <th>Sala</th>
                                <th>Motivo</th>
                                <th>Diagnóstico</th>
                                <th>Tratamiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citas as $cita): ?>
                                <tr>
                                    <td><?php echo $cita['FIDE_CITA_ID']; ?></td>
                                    <?php if (empty($paciente_cedula)): ?>
                                    <td><?php echo $cita['FIDE_NOMBRE_PACIENTE'] . ' ' . $cita['FIDE_APELLIDOS_PACIENTE']; ?></td>
                                    <?php endif; ?>
                                    <td><?php echo $cita['FIDE_NOMBRE_EMPLEADO'] . ' ' . $cita['FIDE_APELLIDOS_EMPLEADO']; ?></td>
                                    <td><?php echo formatDate($cita['FIDE_FECHA_CITA']); ?></td>
                                    <td><?php echo $cita['FIDE_DESCRIPCION_TIPO_SALA']; ?></td>
                                    <td><?php echo $cita['FIDE_MOTIVO_CITA']; ?></td>
                                    <td><?php echo isset($cita['FIDE_DIAGNOSTICO']) ? $cita['FIDE_DIAGNOSTICO'] : 'N/A'; ?></td>
                                    <td><?php echo isset($cita['FIDE_TRATAMIENTO']) ? $cita['FIDE_TRATAMIENTO'] : 'N/A'; ?></td>
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