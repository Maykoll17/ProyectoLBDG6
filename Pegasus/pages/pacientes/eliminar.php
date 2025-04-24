<?php
/**
 * Página para eliminar paciente
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

// Verificar si se envió un formulario POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/pacientes/index.php');
}

// Verificar token CSRF
if (!verifyCSRFToken($_POST['csrf_token'])) {
    showError("Error de seguridad: token inválido");
    redirect('/pages/pacientes/index.php');
}

// Verificar si se proporcionó una cédula
if (!isset($_POST['cedula']) || empty($_POST['cedula'])) {
    showError("Debe especificar una cédula de paciente");
    redirect('/pages/pacientes/index.php');
}

$cedula = sanitizeInput($_POST['cedula']);

// Eliminar el paciente
$result = eliminarPaciente($cedula);

// Redirigir a la lista de pacientes
redirect('/pages/pacientes/index.php');