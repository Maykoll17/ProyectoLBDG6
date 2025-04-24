<?php
/**
 * Página para registrar nuevo medicamento
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Verificar sesión
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/medicamentos.php';

// Comprobar si el usuario está logueado
if (!isLoggedIn()) {
    redirect('pages/login.php');
}

// Verificar permisos (solo administradores y farmacéuticos pueden acceder)
if (!hasRole(['ADMINISTRADOR', 'FARMACEUTICO'])) {
    $_SESSION['error_message'] = "No tiene permisos para acceder a esta página";
    redirect('index.php');
}

// Obtener categorías
$categorias = obtenerCategoriasMedicamentos();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        showError("Error de seguridad: token inválido");
    } else {
        // Obtener y validar datos
        $codigo = sanitizeInput($_POST['codigo']);
        $nombre = sanitizeInput($_POST['nombre']);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $stock_inicial = intval($_POST['stock_inicial']);
        $categoria_id = intval($_POST['categoria_id']);
        $fabricante = sanitizeInput($_POST['fabricante']);
        $fecha_vencimiento = sanitizeInput($_POST['fecha_vencimiento']);
        
        // Convertir fecha de formato dd/mm/yyyy a yyyy-mm-dd para Oracle
        $fecha_vencimiento = dateToSQL($fecha_vencimiento);
        
        // Registrar medicamento
        if (registrarMedicamento($codigo, $nombre, $descripcion, $precio, $stock_inicial, $categoria_id, $fabricante, $fecha_vencimiento)) {
            // Redirigir a la lista de medicamentos
            redirect('pages/medicamentos/index.php');
        }
    }
}

// Incluir header
$pageTitle = "Nuevo Medicamento";
include_once '../../includes/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../../index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Medicamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Medicamento</li>
        </ol>
    </nav>
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><i class="fas fa-plus-circle"></i> Registrar Nuevo Medicamento</h5>
        </div>
        
        <div class="card-body">
            <?php 
            // Mostrar mensajes
            if ($error = getMessage('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" action="" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="codigo" class="form-label">Código *</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" required 
                               maxlength="20" placeholder="Ej: MED001">
                        <div class="form-text">Código único del medicamento</div>
                    </div>
                    
                    <div class="col-md-8">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                               maxlength="100" placeholder="Ej: Paracetamol 500mg">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                              placeholder="Descripción detallada del medicamento"></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="precio" class="form-label">Precio Unitario *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="precio" name="precio" required 
                                   min="0.01" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="stock_inicial" class="form-label">Stock Inicial *</label>
                        <input type="number" class="form-control" id="stock_inicial" name="stock_inicial" required 
                               min="0" step="1" value="0">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="categoria_id" class="form-label">Categoría *</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['FIDE_CATEGORIA_ID']; ?>">
                                <?php echo htmlspecialchars($categoria['FIDE_NOMBRE_CATEGORIA']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="mt-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#nuevaCategoriaModal">
                                <i class="fas fa-plus-circle"></i> Nueva categoría
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fabricante" class="form-label">Fabricante</label>
                        <input type="text" class="form-control" id="fabricante" name="fabricante" 
                               maxlength="100" placeholder="Ej: Laboratorios XYZ">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento *</label>
                        <input type="text" class="form-control datepicker" id="fecha_vencimiento" name="fecha_vencimiento" required 
                               placeholder="dd/mm/aaaa" autocomplete="off">
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Medicamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para crear nueva categoría -->
<div class="modal fade" id="nuevaCategoriaModal" tabindex="-1" aria-labelledby="nuevaCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaCategoriaModalLabel">Nueva Categoría de Medicamentos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaCategoria">
                    <div class="mb-3">
                        <label for="nombreCategoria" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombreCategoria" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcionCategoria" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionCategoria" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCategoria">Guardar Categoría</button>
            </div>
        </div>
    </div>
</div>

<!-- Script para validación y datepicker -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
        language: 'es',
        startDate: 'today'
    });
    
    // Validación de formulario
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Manejar nueva categoría
    document.getElementById('btnGuardarCategoria').addEventListener('click', function() {
        const nombre = document.getElementById('nombreCategoria').value;
        const descripcion = document.getElementById('descripcionCategoria').value;
        
        if (!nombre) {
            alert('El nombre de la categoría es obligatorio');
            return;
        }
        
        // Enviar petición AJAX para crear categoría
        $.ajax({
            url: '../../ajax/crear_categoria.php',
            type: 'POST',
            data: {
                nombre: nombre,
                descripcion: descripcion,
                csrf_token: '<?php echo generateCSRFToken(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Agregar nueva categoría al select
                    const select = document.getElementById('categoria_id');
                    const option = document.createElement('option');
                    option.value = response.categoria_id;
                    option.text = nombre;
                    select.add(option);
                    select.value = response.categoria_id;
                    
                    // Cerrar modal
                    $('#nuevaCategoriaModal').modal('hide');
                    
                    // Limpiar formulario
                    document.getElementById('nombreCategoria').value = '';
                    document.getElementById('descripcionCategoria').value = '';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            }
        });
    });
});
</script>

<?php
// Incluir footer
include_once '../../includes/footer.php';
?>