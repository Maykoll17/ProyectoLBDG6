<?php
/**
 * Página para listar pacientes
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

// Obtener la lista de pacientes
$pacientes = obtenerTodosPacientes();

// Incluir header
include INCLUDES_DIR . '/header.php';
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1>Listado de Pacientes</h1>
        </div>
        <div class="col-auto">
            <a href="<?= BASE_URL ?>/pages/pacientes/nuevo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Paciente
            </a>
        </div>
    </div>

    <?php
    // Mostrar mensajes de éxito, error o información
    $success = getMessage('success');
    $error = getMessage('error');
    $info = getMessage('info');

    if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($info): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $info ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($pacientes)): ?>
                <div class="alert alert-info">No hay pacientes registrados en el sistema.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Deuda</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pacientes as $paciente): ?>
                                <tr>
                                    <td><?= htmlspecialchars($paciente['FIDE_PACIENTE_CEDULA']) ?></td>
                                    <td><?= htmlspecialchars($paciente['FIDE_NOMBRE_PACIENTE']) ?></td>
                                   <td><?= htmlspecialchars($paciente['FIDE_APELLIDOS_PACIENTE']) ?></td>
                                   <td><?= htmlspecialchars($paciente['FIDE_TELEFONO_PACIENTE']) ?></td>
                                   <td>
                                       <span class="badge <?= $paciente['FIDE_DESCRIPCION_ESTADO_PACIENTE'] == 'ACTIVO' ? 'bg-success' : 'bg-secondary' ?>">
                                           <?= htmlspecialchars($paciente['FIDE_DESCRIPCION_ESTADO_PACIENTE']) ?>
                                       </span>
                                   </td>
                                   <td><?= formatCurrency($paciente['FIDE_DEUDA_PACIENTE'], '₡') ?></td>
                                   <td>
                                       <div class="btn-group btn-group-sm" role="group">
                                           <a href="<?= BASE_URL ?>/pages/pacientes/editar.php?cedula=<?= urlencode($paciente['FIDE_PACIENTE_CEDULA']) ?>" class="btn btn-primary" title="Editar">
                                               <i class="fas fa-edit"></i>
                                           </a>
                                           <a href="<?= BASE_URL ?>/pages/pacientes/historial.php?cedula=<?= urlencode($paciente['FIDE_PACIENTE_CEDULA']) ?>" class="btn btn-info" title="Historial">
                                               <i class="fas fa-file-medical"></i>
                                           </a>
                                           <button type="button" class="btn btn-danger btn-eliminar" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#modalEliminar" 
                                               data-cedula="<?= htmlspecialchars($paciente['FIDE_PACIENTE_CEDULA']) ?>"
                                               data-nombre="<?= htmlspecialchars($paciente['FIDE_NOMBRE_PACIENTE'] . ' ' . $paciente['FIDE_APELLIDOS_PACIENTE']) ?>"
                                               title="Eliminar">
                                               <i class="fas fa-trash"></i>
                                           </button>
                                       </div>
                                   </td>
                               </tr>
                           <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>
           <?php endif; ?>
       </div>
   </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="modalEliminarLabel">Confirmar Eliminación</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               ¿Está seguro que desea eliminar al paciente <strong id="nombrePaciente"></strong>?
               <p class="text-danger mt-3">Esta acción no se puede deshacer.</p>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
               <form id="formEliminar" action="<?= BASE_URL ?>/pages/pacientes/eliminar.php" method="POST">
                   <input type="hidden" id="cedulaEliminar" name="cedula" value="">
                   <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                   <button type="submit" class="btn btn-danger">Eliminar</button>
               </form>
           </div>
       </div>
   </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function() {
       // Configurar el modal de eliminación
       const modalEliminar = document.getElementById('modalEliminar');
       modalEliminar.addEventListener('show.bs.modal', function(event) {
           const button = event.relatedTarget;
           const cedula = button.getAttribute('data-cedula');
           const nombre = button.getAttribute('data-nombre');
           
           document.getElementById('nombrePaciente').textContent = nombre;
           document.getElementById('cedulaEliminar').value = cedula;
       });
   });
</script>

<?php
// Incluir footer
include INCLUDES_DIR . '/footer.php';
?>