<?php
/**
 * Página para gestionar pacientes hospitalizados
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

// Obtener lista de pacientes hospitalizados
$pacientes_hospitalizados = obtenerPacientesHospitalizados();

// Incluir header
include INCLUDES_DIR . '/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/pacientes/index.php">Pacientes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Hospitalizados</li>
        </ol>
    </nav>

    <div class="row mb-3">
        <div class="col">
            <h1>Pacientes Hospitalizados</h1>
        </div>
        <div class="col-auto">
            <a href="<?= BASE_URL ?>/pages/pacientes/hospitalizar.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Ingreso
            </a>
        </div>
    </div>

    <?php
    // Mostrar mensajes
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
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="hospitalizadosTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos" type="button" role="tab" aria-controls="activos" aria-selected="true">Pacientes Activos</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="hospitalizadosTabContent">
                <div class="tab-pane fade show active" id="activos" role="tabpanel" aria-labelledby="activos-tab">
                    <?php if (empty($pacientes_hospitalizados)): ?>
                        <div class="alert alert-info">
                            No hay pacientes hospitalizados actualmente.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Paciente</th>
                                        <th>Cédula</th>
                                        <th>Sala</th>
                                        <th>Médico Responsable</th>
                                        <th>Ingreso</th>
                                        <th>Días</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pacientes_hospitalizados as $index => $paciente): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($paciente['FIDE_NOMBRE_PACIENTE'] . ' ' . $paciente['FIDE_APELLIDOS_PACIENTE']) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($paciente['FIDE_PACIENTE_CEDULA']) ?></td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    <?= htmlspecialchars($paciente['FIDE_DESCRIPCION_TIPO_SALA']) ?>
                                                </span>
                                                <small class="d-block text-muted">Sala #<?= $paciente['FIDE_SALA_ID'] ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($paciente['FIDE_NOMBRE_EMPLEADO'] . ' ' . $paciente['FIDE_APELLIDOS_EMPLEADO']) ?></td>
                                            <td>
                                                <?= formatDate($paciente['FIDE_FECHA_INGRESO'], 'd/m/Y H:i') ?>
                                                <small class="d-block text-muted"><?= htmlspecialchars($paciente['FIDE_MOTIVO_INGRESO']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?= $paciente['DIAS_HOSPITALIZACION'] > 7 ? 'bg-danger' : 'bg-success' ?>">
                                                    <?= $paciente['DIAS_HOSPITALIZACION'] ?> días
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= BASE_URL ?>/pages/pacientes/detalles_hospitalizacion.php?id=<?= $paciente['FIDE_HOSPITALIZACION_ID'] ?>" class="btn btn-info" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>/pages/pacientes/alta.php?id=<?= $paciente['FIDE_HOSPITALIZACION_ID'] ?>" class="btn btn-success" title="Dar de alta">
                                                        <i class="fas fa-hospital-user"></i> Alta
                                                    </a>
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
    </div>

    <div class="mt-4">
        <h2>Estadísticas de Hospitalización</h2>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Hospitalizados</h5>
                        <p class="card-text display-4"><?= count($pacientes_hospitalizados) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Ocupación promedio</h5>
                        <p class="card-text display-4">
                            <?php
                            $total_dias = 0;
                            foreach ($pacientes_hospitalizados as $paciente) {
                                $total_dias += $paciente['DIAS_HOSPITALIZACION'];
                            }
                            $promedio = count($pacientes_hospitalizados) > 0 ? round($total_dias / count($pacientes_hospitalizados), 1) : 0;
                            echo $promedio . ' días';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Hospitalizaciones largas</h5>
                        <p class="card-text display-4">
                            <?php
                            $largas = 0;
                            foreach ($pacientes_hospitalizados as $paciente) {
                                if ($paciente['DIAS_HOSPITALIZACION'] > 7) {
                                    $largas++;
                                }
                            }
                            echo $largas;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir footer
include INCLUDES_DIR . '/footer.php';
?>