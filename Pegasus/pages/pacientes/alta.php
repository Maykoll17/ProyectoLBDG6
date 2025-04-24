<?php
/**
 * Página para dar de alta a un paciente hospitalizado
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

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    showError("Debe especificar un ID de hospitalización");
    redirect('/pages/pacientes/hospitalizados.php');
}

$id = (int)$_GET['id'];

// Obtener datos de la hospitalización
$db = Database::getInstance($db_config);
$query = "SELECT 
            h.FIDE_HOSPITALIZACION_ID,
            h.FIDE_PACIENTE_CEDULA,
            p.FIDE_NOMBRE_PACIENTE,
            p.FIDE_APELLIDOS_PACIENTE,
            h.FIDE_SALA_ID,
            h.FIDE_EMPLEADO_CEDULA,
            e.FIDE_NOMBRE_EMPLEADO,
            e.FIDE_APELLIDOS_EMPLEADO,
            h.FIDE_FECHA_INGRESO,
            h.FIDE_MOTIVO_INGRESO,
            h.FIDE_DIAGNOSTICO_INGRESO,
            ts.FIDE_DESCRIPCION_TIPO_SALA,
            ROUND(
                EXTRACT(DAY FROM (SYSTIMESTAMP - h.FIDE_FECHA_INGRESO)) + 
                EXTRACT(HOUR FROM (SYSTIMESTAMP - h.FIDE_FECHA_INGRESO))/24 +
                EXTRACT(MINUTE FROM (SYSTIMESTAMP - h.FIDE_FECHA_INGRESO))/(24*60), 
                1
            ) AS DIAS_HOSPITALIZACION
          FROM FIDE_HOSPITALIZACIONES_TB h
          JOIN FIDE_PACIENTES_TB p ON h.FIDE_PACIENTE_CEDULA = p.FIDE_PACIENTE_CEDULA
          JOIN FIDE_EMPLEADOS_TB e ON h.FIDE_EMPLEADO_CEDULA = e.FIDE_EMPLEADO_CEDULA
          JOIN FIDE_SALAS_TB s ON h.FIDE_SALA_ID = s.FIDE_SALA_ID
          JOIN FIDE_TIPOS_SALAS_TB ts ON s.FIDE_TIPO_SALA_ID = ts.FIDE_TIPO_SALA_ID
          WHERE h.FIDE_HOSPITALIZACION_ID = :id
          AND h.FIDE_FECHA_ALTA IS NULL
          AND h.FIDE_ESTADO = 'ACTIVO'";

$hospitalizacion = $db->queryOne($query, ['id' => $id]);

if (!$hospitalizacion) {
    showError("La hospitalización no existe o ya ha sido dada de alta");
    redirect('/pages/pacientes/hospitalizados.php');
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        showError("Error de seguridad: token inválido");
        redirect('/pages/pacientes/alta.php?id=' . $id);
    }
    
    // Sanitizar datos
    $notas_alta = sanitizeInput($_POST['notas_alta']);
    
    // Dar de alta al paciente
    $result = darAltaPaciente($id, $notas_alta);
    
    if ($result) {
        // Redirigir a la lista de pacientes hospitalizados
        redirect('/pages/pacientes/hospitalizados.php');
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
            <li class="breadcrumb-item active" aria-current="page">Alta Médica</li>
        </ol>
    </nav>

    <div class="row mb-3">
        <div class="col">
            <h1>Alta Médica</h1>
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

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Información del Paciente</h5>
                </div>
                <div class="card-body">
                <h5><?= htmlspecialchars($hospitalizacion['FIDE_NOMBRE_PACIENTE'] . ' ' . $hospitalizacion['FIDE_APELLIDOS_PACIENTE']) ?></h5>
                   <p class="mb-1"><strong>Cédula:</strong> <?= htmlspecialchars($hospitalizacion['FIDE_PACIENTE_CEDULA']) ?></p>
                   <p class="mb-1"><strong>Médico:</strong> <?= htmlspecialchars($hospitalizacion['FIDE_NOMBRE_EMPLEADO'] . ' ' . $hospitalizacion['FIDE_APELLIDOS_EMPLEADO']) ?></p>
                   <p class="mb-1"><strong>Ingreso:</strong> <?= formatDate($hospitalizacion['FIDE_FECHA_INGRESO'], 'd/m/Y H:i') ?></p>
                   <p class="mb-1"><strong>Días hospitalizado:</strong> <?= $hospitalizacion['DIAS_HOSPITALIZACION'] ?></p>
                   <p class="mb-1"><strong>Sala:</strong> <?= htmlspecialchars($hospitalizacion['FIDE_DESCRIPCION_TIPO_SALA']) ?> (Sala #<?= $hospitalizacion['FIDE_SALA_ID'] ?>)</p>
               </div>
           </div>
           
           <div class="card">
               <div class="card-header bg-info text-white">
                   <h5 class="card-title mb-0">Información de Ingreso</h5>
               </div>
               <div class="card-body">
                   <div class="mb-3">
                       <h6>Motivo de Ingreso:</h6>
                       <p><?= nl2br(htmlspecialchars($hospitalizacion['FIDE_MOTIVO_INGRESO'])) ?></p>
                   </div>
                   
                   <div>
                       <h6>Diagnóstico Inicial:</h6>
                       <p><?= nl2br(htmlspecialchars($hospitalizacion['FIDE_DIAGNOSTICO_INGRESO'])) ?></p>
                   </div>
               </div>
           </div>
       </div>
       
       <div class="col-md-8">
           <div class="card">
               <div class="card-header bg-success text-white">
                   <h5 class="card-title mb-0">Registrar Alta Médica</h5>
               </div>
               <div class="card-body">
                   <form action="<?= BASE_URL ?>/pages/pacientes/alta.php?id=<?= $id ?>" method="POST">
                       <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                       
                       <div class="alert alert-warning">
                           <i class="fas fa-exclamation-triangle"></i> Al dar de alta al paciente, se liberará la sala y finalizará el registro de hospitalización.
                       </div>
                       
                       <div class="mb-3">
                           <label for="notas_alta" class="form-label">Notas de Alta Médica:</label>
                           <textarea class="form-control" id="notas_alta" name="notas_alta" rows="6" placeholder="Ingrese el diagnóstico final, recomendaciones, medicamentos recetados y próximos pasos..."></textarea>
                       </div>
                       
                       <div class="form-check mb-3">
                           <input class="form-check-input" type="checkbox" id="confirmar_alta" required>
                           <label class="form-check-label" for="confirmar_alta">
                               Confirmo que el paciente puede ser dado de alta médica
                           </label>
                       </div>
                       
                       <div class="text-end">
                           <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= BASE_URL ?>/pages/pacientes/hospitalizados.php'">Cancelar</button>
                           <button type="submit" class="btn btn-success" id="btn_alta">
                               <i class="fas fa-hospital-user"></i> Registrar Alta Médica
                           </button>
                       </div>
                   </form>
               </div>
           </div>
       </div>
   </div>
</div>

<script>
   // Confirmar que se marcó el checkbox antes de enviar
   document.addEventListener('DOMContentLoaded', function() {
       const btnAlta = document.getElementById('btn_alta');
       const chkConfirmar = document.getElementById('confirmar_alta');
       
       btnAlta.addEventListener('click', function(e) {
           if (!chkConfirmar.checked) {
               e.preventDefault();
               alert('Debe confirmar que el paciente puede ser dado de alta médica.');
           }
       });
   });
</script>

<?php
// Incluir footer
include INCLUDES_DIR . '/footer.php';
?>