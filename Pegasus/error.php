<?php
/**
 * Página de error 404
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Incluir archivos necesarios
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Título de la página
$pageTitle = 'Página no encontrada';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="<?php echo CSS_URL; ?>/style.css" rel="stylesheet">
    <style>
        .error-container {
            text-align: center;
            padding: 80px 0;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #0d6efd;
            line-height: 1;
        }
        .error-message {
            font-size: 2rem;
            margin-bottom: 30px;
        }
        .error-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="error-code">404</div>
            <div class="error-message">Página no encontrada</div>
            <p class="lead">Lo sentimos, la página que está buscando no existe o ha sido movida.</p>
            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>