<?php
/**
 * Página para registrar una nueva hospitalización
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once INCLUDES_DIR . '/Database.php';
require_once INCLUDES_DIR . '/functions.php';
require_once MODELS_DIR . '/pacientes.php';
require_once MODELS_DIR . '/salas.php';
require_once MODELS_DIR . '/empleados.php';

// Verificar si el usuario está logueado
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Obtener datos de pacientes, salas y médicos para los selectores
$pacientes = obtenerTodosPacientes();
$salas_disponibles = obtenerSalasDisponibles();
$medicos = obtenerMedicosActivos();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        showError("Error de seguridad: token inválido");
        redirect('/pages/pacientes/hospitalizar.php');
    }
    
    // Sanitizar y validar datos
    $cedula_paciente = sanitizeInput($_POST['paciente']);
    $sala_id = (int)$_POST['sala'];
    $cedula_medico = sanitizeInput($_POST['medico']);
    $motivo = sanitizeInput($_POST['motivo']);
    $diagnostico = sanitizeInput($_POST['diagnostico']);
    
    // Validar datos obligatorios
    if (empty($cedula_paciente) || empty($cedula_medico) || empty($motivo) || $sala_id <= 0) {
        showError("Todos los campos marcados con * son obligatorios");
    } else {
        // Registrar hospitalización
        $result = registrarHospitalizacion($cedula_paciente, $sala_id, $cedula_medico, $motivo, $diagnostico);
        
        if ($result) {
            // Redirigir a la lista de pacientes hospitalizados
            showSuccess("Hospitalización registrada correctamente");
            redirect('/pages/pacientes/hospitalizados.php');
        }
    }
}

// Incluir header
include INCLUDES_DIR . '/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/pacientes/index.php">Pacientes</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/pacientes/hospitalizados.php">Hospitalizados</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Ingreso</li>
        </ol>
    </nav>

    <div class="row mb-3">
        <div class="col">
            <h1>Registrar Nueva Hospitalización</h1>
        </div>
    </div>

    <?php
    // Mostrar mensajes de error
    $error = getMessage('error');
    if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= BASE_URL ?>/pages/pacientes/hospitalizar.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="paciente" class="form-label">Paciente *</label>
                        <select class="form-select" id="paciente" name="paciente" required>
                            <option value="">Seleccione un paciente...</option>
                            <?php foreach ($pacientes as $paciente): ?>
                                <option value="<?= htmlspecialchars($paciente['FIDE_PACIENTE_CEDULA']) ?>">
                                    <?= htmlspecialchars($paciente['FIDE_APELLIDOS_PACIENTE'] . ', ' . $paciente['FIDE_NOMBRE_PACIENTE'] . ' - ' . $paciente['FIDE_PACIENTE_CEDULA']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Seleccione un paciente
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="medico" class="form-label">Médico Responsable *</label>
                        <select class="form-select" id="medico" name="medico" required>
                            <option value="">Seleccione un médico...</option>
                            <?php foreach ($medicos as $medico): ?>
                                <option value="<?= htmlspecialchars($medico['FIDE_EMPLEADO_CEDULA']) ?>">
                                    <?= htmlspecialchars($medico['FIDE_APELLIDOS_EMPLEADO'] . ', ' . $medico['FIDE_NOMBRE_EMPLEADO'] . ' - ' . $medico['FIDE_ESPECIALIDAD']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Seleccione un médico responsable
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sala" class="form-label">Sala *</label>
                        <select class="form-select" id="sala" name="sala" required>
                            <option value="">Seleccione una sala disponible...</option>
                            <?php foreach ($salas_disponibles as $sala): ?>
                                <option value="<?= $sala['FIDE_SALA_ID'] ?>">
                                    <?= htmlspecialchars('Sala #' . $sala['FIDE_SALA_ID'] . ' - ' . $sala['FIDE_DESCRIPCION_TIPO_SALA'] . ' - Capacidad: ' . $sala['FIDE_CAPACIDAD_SALA']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Seleccione una sala
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="motivo" class="form-label">Motivo de Ingreso *</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="2" required></textarea>
                        <div class="invalid-feedback">
                            Ingrese el motivo de hospitalización
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="diagnostico" class="form-label">Diagnóstico Inicial</label>
                        <textarea class="form-control" id="diagnostico" name="diagnostico" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/pages/pacientes/hospitalizados.php'">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Hospitalización</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Validación del formulario con Bootstrap
    (function() {
        'use strict'
        
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')
        
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

<?php
// Incluir footer
include INCLUDES_DIR . '/footer.php';
?>