<?php
/**
 * Generar Nueva Factura
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
require_once '../../models/pacientes.php';
require_once '../../models/medicamentos.php';

// Título de la página
$pageTitle = 'Generar Nueva Factura';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_cedula = sanitizeInput($_POST['paciente_cedula']);
    
    // Validaciones
    if (empty($paciente_cedula)) {
        showError("Debe seleccionar un paciente");
    } else {
        // Generar la factura
        $resultado = generarFactura($paciente_cedula);
        
        if ($resultado) {
            showSuccess("Factura generada correctamente");
            redirect('pages/facturacion/index.php');
        }
    }
}

// Obtener lista de pacientes con deuda o pendientes
$pacientes = obtenerPacientesConDeuda();

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
            <h5 class="card-title mb-0">Seleccionar Paciente</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Al generar una factura, se incluirán automáticamente todos los medicamentos reservados
                por el paciente y las citas pendientes. Asegúrese de que toda la información esté correctamente registrada antes de continuar.
            </div>
            
            <form action="" method="post" class="row g-3">
                <div class="col-md-12">
                    <label for="paciente_cedula" class="form-label">Paciente *</label>
                    <select name="paciente_cedula" id="paciente_cedula" class="form-select" required>
                        <option value="">Seleccione un paciente</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?php echo $paciente['FIDE_PACIENTE_CEDULA']; ?>">
                                <?php echo $paciente['FIDE_PACIENTE_CEDULA'] . ' - ' . 
                                           $paciente['FIDE_NOMBRE_PACIENTE'] . ' ' . 
                                           $paciente['FIDE_APELLIDOS_PACIENTE'] . 
                                           ' (Deuda: ₡' . number_format($paciente['FIDE_DEUDA_PACIENTE'], 2) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-invoice"></i> Generar Factura
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Información de Paciente -->
    <div id="pacienteInfo" class="card mt-4 d-none">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Información del Paciente</h5>
        </div>
        <div class="card-body">
            <div class="row" id="pacienteDetalles">
                <!-- La información del paciente se cargará aquí vía JavaScript -->
            </div>
        </div>
    </div>
    
    <!-- Elementos a Facturar -->
    <div id="elementosFactura" class="card mt-4 d-none">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Elementos a Facturar</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Medicamentos Reservados</h6>
                    <div id="medicamentosReservados">
                        <!-- Los medicamentos reservados se cargarán aquí -->
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Citas Médicas</h6>
                    <div id="citasPendientes">
                        <!-- Las citas pendientes se cargarán aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pacienteSelect = document.getElementById('paciente_cedula');
    const pacienteInfo = document.getElementById('pacienteInfo');
    const pacienteDetalles = document.getElementById('pacienteDetalles');
    const elementosFactura = document.getElementById('elementosFactura');
    const medicamentosReservados = document.getElementById('medicamentosReservados');
    const citasPendientes = document.getElementById('citasPendientes');
    
    pacienteSelect.addEventListener('change', function() {
        const pacienteCedula = this.value;
        
        if (pacienteCedula) {
            // Aquí podrías hacer una petición AJAX para obtener los detalles del paciente
            // y mostrarlos en la página antes de generar la factura
            
            // Por ahora, simulamos que obtenemos datos:
            pacienteInfo.classList.remove('d-none');
            elementosFactura.classList.remove('d-none');
            
            // Mostrar información ficticia del paciente
            pacienteDetalles.innerHTML = `
                <div class="col-md-6">
                    <p><strong>Cédula:</strong> ${pacienteCedula}</p>
                    <p><strong>Teléfono:</strong> Consultar en sistema</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Estado:</strong> Con pendientes</p>
                </div>
            `;
            
            // Mostrar elementos a facturar ficticios
            medicamentosReservados.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-spinner fa-spin"></i> Consultando medicamentos reservados...
                </div>
            `;
            
            citasPendientes.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-spinner fa-spin"></i> Consultando citas pendientes...
                </div>
            `;
            
            // En una implementación real, aquí harías una petición AJAX
            // para obtener estos datos y mostrarlos
        } else {
            pacienteInfo.classList.add('d-none');
            elementosFactura.classList.add('d-none');
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>