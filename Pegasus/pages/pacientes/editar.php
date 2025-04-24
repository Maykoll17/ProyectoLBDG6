<?php
/**
 * Página para editar paciente
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once INCLUDES_DIR . '/Database.php';
require_once INCLUDES_DIR . '/functions.php';
require_once MODELS_DIR . '/pacientes.php';

// Verificar si el usuario está logueado
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Verificar si se proporcionó una cédula
if (!isset($_GET['cedula']) || empty($_GET['cedula'])) {
    showError("Debe especificar una cédula de paciente");
    redirect('/pages/pacientes/index.php');
}

$cedula = sanitizeInput($_GET['cedula']);

// Obtener datos del paciente
$paciente = buscarPaciente($cedula);
if (!$paciente) {
    showError("No se encontró el paciente con cédula: $cedula");
    redirect('/pages/pacientes/index.php');
}

// Obtener estados de pacientes para el formulario
$estados = obtenerEstadosPacientes();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        showError("Error de seguridad: token inválido");
        redirect('/pages/pacientes/editar.php?cedula=' . urlencode($cedula));
    }
    
    // Sanitizar y validar datos
    $nombre = sanitizeInput($_POST['nombre']);
    $apellidos = sanitizeInput($_POST['apellidos']);
    $telefono = sanitizeInput($_POST['telefono']);
    $direccion = sanitizeInput($_POST['direccion']);
    $correo = sanitizeInput($_POST['correo']);
    $estado_id = (int)$_POST['estado_id'];
    $deuda = floatval($_POST['deuda']);
    
    // Actualizar el paciente
    $result = actualizarPaciente($cedula, $nombre, $apellidos, $telefono, $direccion, $correo, $estado_id, $deuda);
    
    if ($result) {
        // Redirigir a la lista de pacientes con mensaje de éxito
        redirect('/pages/pacientes/index.php');
    }
    // Si hay error, se mostrará con la función showError()
    
    // Recargar datos del paciente
    $paciente = buscarPaciente($cedula);
}

// Incluir header
include INCLUDES_DIR . '/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/pacientes/index.php">Pacientes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Paciente</li>
        </ol>
    </nav>

    <div class="row mb-3">
        <div class="col">
            <h1>Editar Paciente</h1>
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
            <form action="<?= BASE_URL ?>/pages/pacientes/editar.php?cedula=<?= urlencode($cedula) ?>" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" id="cedula" value="<?= htmlspecialchars($paciente['FIDE_PACIENTE_CEDULA']) ?>" readonly>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required value="<?= htmlspecialchars($paciente['FIDE_NOMBRE_PACIENTE']) ?>">
                        <div class="invalid-feedback">
                            El nombre es obligatorio
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required value="<?= htmlspecialchars($paciente['FIDE_APELLIDOS_PACIENTE']) ?>">
                        <div class="invalid-feedback">
                            Los apellidos son obligatorios
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($paciente['FIDE_TELEFONO_PACIENTE']) ?>">
                    </div>
                    <div class="col-md-8">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($paciente['FIDE_DIRECCION_PACIENTE']) ?>">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($paciente['FIDE_CORREO_PACIENTE']) ?>">
                        <div class="invalid-feedback">
                            Ingrese un correo electrónico válido
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="estado_id" class="form-label">Estado *</label>
                        <select class="form-select" id="estado_id" name="estado_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?= $estado['FIDE_ESTADO_PACIENTE_ID'] ?>" <?= ($estado['FIDE_ESTADO_PACIENTE_ID'] == $paciente['FIDE_ESTADO_PACIENTE_ID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($estado['FIDE_DESCRIPCION_ESTADO_PACIENTE']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Seleccione un estado
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="deuda" class="form-label">Deuda</label>
                        <div class="input-group">
                            <span class="input-group-text">₡</span>
                            <input type="number" class="form-control" id="deuda" name="deuda" value="<?= $paciente['FIDE_DEUDA_PACIENTE'] ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/pages/pacientes/index.php'">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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