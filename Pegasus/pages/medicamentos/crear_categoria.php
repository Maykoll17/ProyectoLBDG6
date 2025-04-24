<?php
/**
 * AJAX para crear una nueva categoría de medicamentos
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Verificar sesión
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/Database.php';
require_once '../models/medicamentos.php';

// Comprobar si el usuario está logueado
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Verificar permisos
if (!hasRole(['ADMINISTRADOR', 'FARMACEUTICO'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para esta acción']);
    exit;
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
    exit;
}

// Obtener datos
$nombre = isset($_POST['nombre']) ? sanitizeInput($_POST['nombre']) : '';
$descripcion = isset($_POST['descripcion']) ? sanitizeInput($_POST['descripcion']) : '';

// Validar datos
if (empty($nombre)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'El nombre de la categoría es obligatorio']);
    exit;
}

try {
    // Obtener la conexión a la base de datos
    global $db_config;
    $db = Database::getInstance($db_config);
    
    // Verificar si la categoría ya existe
    $query = "SELECT COUNT(*) AS existe FROM FIDE_CATEGORIAS_MEDICAMENTOS_TB WHERE UPPER(FIDE_NOMBRE_CATEGORIA) = UPPER(:nombre)";
    $resultado = $db->queryValue($query, ['nombre' => $nombre], 'EXISTE');
    
    if ($resultado > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ya existe una categoría con ese nombre']);
        exit;
    }
    
    // Insertar la nueva categoría
    $query = "INSERT INTO FIDE_CATEGORIAS_MEDICAMENTOS_TB (
                FIDE_CATEGORIA_ID, 
                FIDE_NOMBRE_CATEGORIA, 
                FIDE_DESCRIPCION_CATEGORIA
              ) VALUES (
                FIDE_CATEGORIAS_SEQ.NEXTVAL, 
                :nombre, 
                :descripcion
              ) RETURNING FIDE_CATEGORIA_ID INTO :categoria_id";
    
    // Crear statement manualmente para usar OUT parameter
    $stmt = oci_parse($db->getConnection(), $query);
    
    // Vincular variables
    oci_bind_by_name($stmt, ":nombre", $nombre);
    oci_bind_by_name($stmt, ":descripcion", $descripcion);
    
    // Variable para recuperar el ID generado
    $categoria_id = 0;
    oci_bind_by_name($stmt, ":categoria_id", $categoria_id, 32, SQLT_INT);
    
    // Ejecutar
    oci_execute($stmt);
    
    // Respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Categoría creada correctamente', 
        'categoria_id' => $categoria_id
    ]);
    
} catch (Exception $e) {
    // Log del error
    logError("Error al crear categoría: " . $e->getMessage());
    
    // Respuesta de error
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al crear la categoría: ' . $e->getMessage()]);
}