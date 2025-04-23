<?php
/**
 * Página principal del sistema
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Incluir archivos necesarios
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Verificar si el usuario está autenticado
if (!isLoggedIn()) {
    // Si no está autenticado, redirigir al login
    redirect('/pages/login.php');
    exit();
}

// Si está autenticado, redirigir al dashboard
redirect('/pages/dashboard.php');