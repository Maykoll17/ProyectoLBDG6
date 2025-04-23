<?php
/**
 * Header del sistema
 * Sistema de Gestión Hospitalaria Pegasus
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="<?php echo CSS_URL; ?>/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="fas fa-hospital"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <?php if (isLoggedIn()): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActiveMenu('/dashboard') ? 'active' : ''; ?>" 
                           href="<?php echo BASE_URL; ?>/pages/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActiveMenu('/pacientes') ? 'active' : ''; ?>" 
                           href="#" id="pacientesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-injured"></i> Pacientes
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="pacientesDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/pacientes/index.php">Listar Pacientes</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/pacientes/nuevo.php">Nuevo Paciente</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/pacientes/hospitalizados.php">Hospitalizados</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/pacientes/deudas.php">Pacientes con Deuda</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActiveMenu('/citas') ? 'active' : ''; ?>" 
                           href="#" id="citasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar-alt"></i> Citas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="citasDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/citas/index.php">Listar Citas</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/citas/nueva.php">Nueva Cita</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/citas/historial.php">Historial</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActiveMenu('/medicamentos') ? 'active' : ''; ?>" 
                           href="#" id="medicamentosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-pills"></i> Medicamentos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="medicamentosDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/medicamentos/index.php">Listar Medicamentos</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/medicamentos/nuevo.php">Nuevo Medicamento</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/medicamentos/stock.php">Control de Stock</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActiveMenu('/salas') ? 'active' : ''; ?>" 
                           href="#" id="salasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-door-open"></i> Salas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="salasDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/salas/index.php">Listar Salas</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/salas/nueva.php">Nueva Sala</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/salas/disponibles.php">Disponibles</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/salas/alquileres.php">Alquileres</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActiveMenu('/facturacion') ? 'active' : ''; ?>" 
                           href="#" id="facturacionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-invoice-dollar"></i> Facturación
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="facturacionDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/facturacion/index.php">Listar Facturas</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/facturacion/nueva.php">Nueva Factura</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/facturacion/recibos.php">Recibos</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActiveMenu('/reportes') ? 'active' : ''; ?>" 
                           href="#" id="reportesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="reportesDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/reportes/pacientes.php">Pacientes</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/reportes/citas.php">Citas</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/reportes/facturacion.php">Facturación</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/reportes/medicamentos.php">Medicamentos</a></li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username'] ?? 'Usuario'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/perfil/index.php">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/perfil/cambiar-clave.php">Cambiar Contraseña</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-4">
        <!-- Mensajes de alerta -->
        <?php if ($error = getMessage('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($success = getMessage('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($info = getMessage('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i> <?php echo $info; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>