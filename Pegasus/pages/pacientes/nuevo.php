<?php
/**
 * Página para registrar un nuevo paciente
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

// Obtener estados de pacientes para el formulario
$estados = obtenerEstadosPacientes();

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        showError("Error de seguridad: token inválido");
        redirect('/pages/pacientes/nuevo.php');
    }
    
    // Sanitizar y validar datos
    $cedula = sanitizeInput($_POST['cedula']);
    $nombre = sanitizeInput($_POST['nombre']);
    $apellidos = sanitizeInput($_POST['apellidos']);
    $telefono = sanitizeInput($_POST['telefono']);
    $direccion = sanitizeInput($_POST['direccion']);
    $correo = sanitizeInput($_POST['correo']);
    $estado_id = (int)$_POST['estado_id'];
    $deuda = floatval($_POST['deuda']);
    
    // Registrar el paciente
    $result = registrarPaciente($cedula, $nombre, $apellidos, $telefono, $direccion, $correo, $estado_id, $deuda);
    
    if ($result) {
        // Redirigir a la lista de pacientes con mensaje de éxito
        redirect('/pages/pacientes/index.php');
    }
    // Si hay error, se mostrará con la función showError()
}

// Incluir header
include INCLUDES_DIR . '/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/pacientes/index.php">Pacientes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Paciente</li>
        </ol>
    </nav>

    <div class="row mb-3">
        <div class="col">
            <h1>Nuevo Paciente</h1>
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
            <form action="<?= BASE_URL ?>/pages/pacientes/nuevo.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="cedula" class="form-label">Cédula *</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                        <div class="invalid-feedback">
                            La cédula es obligatoria
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                        <div class="invalid-feedback">
                            El nombre es obligatorio
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                        <div class="invalid-feedback">
                            Los apellidos son obligatorios
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="col-md-8">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                        <div class="invalid-feedback">
                            Ingrese un correo electrónico válido
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="estado_id" class="form-label">Estado *</label>
                        <select class="form-select" id="estado_id" name="estado_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?= $estado['FIDE_ESTADO_PACIENTE_ID'] ?>">
                                    <?= htmlspecialchars($estado['FIDE_DESCRIPCION_ESTADO_PACIENTE']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Seleccione un estado
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="deuda" class="form-label">Deuda Inicial</label>
                        <div class="input-group">
                            <span class="input-group-text">₡</span>
                            <input type="number" class="form-control" id="deuda" name="deuda" value="0" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/pages/pacientes/index.php'">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
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