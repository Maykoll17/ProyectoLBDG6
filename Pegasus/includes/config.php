<?php
/**
 * Archivo de configuración del sistema
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Configuración de la base de datos Oracle
$db_config = [
    'username' => 'AdminHospital',
    'password' => 'admin123',
    'connection_string' => 'localhost/XE' // Ajusta según tu configuración Oracle
];

// Configuración general del sistema
define('SITE_NAME', 'Pegasus - Sistema de Gestión Hospitalaria');
define('BASE_URL', '/pegasus'); // Ajusta según tu configuración del servidor web

// Configuración de sesión
session_start();

// Zona horaria
date_default_timezone_set('America/Mexico_City'); // Ajusta según tu ubicación

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Directorio raíz
define('ROOT_DIR', dirname(__DIR__));

// Directorios del sistema
define('INCLUDES_DIR', ROOT_DIR . '/includes');
define('MODELS_DIR', ROOT_DIR . '/models');
define('PAGES_DIR', ROOT_DIR . '/pages');
define('LOGS_DIR', ROOT_DIR . '/logs');
define('ASSETS_DIR', ROOT_DIR . '/assets');

// Rutas para assets
define('CSS_URL', BASE_URL . '/assets/css');
define('JS_URL', BASE_URL . '/assets/js');
define('IMG_URL', BASE_URL . '/assets/img');

// Verificar que exista el directorio de logs
if (!is_dir(LOGS_DIR)) {
    mkdir(LOGS_DIR, 0755, true);
}