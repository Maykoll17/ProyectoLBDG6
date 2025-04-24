<?php
/**
 * Página de listado de medicamentos
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Verificar sesión

require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/medicamentos.php';
// Incluir archivos necesarios
require_once '../../includes/config.php';
require_once INCLUDES_DIR . '/Database.php';
require_once INCLUDES_DIR . '/functions.php';
require_once MODELS_DIR . '/medicamentos.php';

// Iniciar sesión
if (!isLoggedIn()) {
    redirect('/login.php');
}


// Verificar permisos (asumiendo que solo administradores y farmacéuticos pueden acceder)
if (!hasRole(['ADMINISTRADOR', 'FARMACEUTICO', 'MEDICO'])) {
    $_SESSION['error_message'] = "No tiene permisos para acceder a esta página";
    redirect('index.php');
}

// Obtener parámetros de filtrado
$filtro = isset($_GET['filtro']) ? sanitizeInput($_GET['filtro']) : '';
$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : null;
$estado = isset($_GET['estado']) ? sanitizeInput($_GET['estado']) : 'TODOS';

// Obtener lista de medicamentos
$medicamentos = obtenerTodosMedicamentos($filtro, $categoria_id, $estado);

// Obtener categorías para el filtro
$categorias = obtenerCategoriasMedicamentos();

// Incluir header
$pageTitle = "Gestión de Medicamentos";
include_once '../../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-pills"></i> Gestión de Medicamentos</h1>
        
        <?php if (hasRole(['ADMINISTRADOR', 'FARMACEUTICO'])): ?>
        <div>
            <a href="nuevo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Medicamento
            </a>
            <a href="stock.php" class="btn btn-success">
                <i class="fas fa-boxes"></i> Gestión de Stock
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php 
    // Mostrar mensajes
    if ($error = getMessage('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success = getMessage('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-filter"></i> Filtros de búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="get" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="filtro" class="form-label">Buscar por código o nombre</label>
                    <input type="text" class="form-control" id="filtro" name="filtro" value="<?php echo htmlspecialchars($filtro); ?>" placeholder="Ingrese código o nombre">
                </div>
                
                <div class="col-md-3">
                    <label for="categoria_id" class="form-label">Categoría</label>
                    <select class="form-select" id="categoria_id" name="categoria_id">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['FIDE_CATEGORIA_ID']; ?>" <?php echo ($categoria_id == $categoria['FIDE_CATEGORIA_ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['FIDE_NOMBRE_CATEGORIA']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="TODOS" <?php echo ($estado == 'TODOS') ? 'selected' : ''; ?>>Todos</option>
                        <option value="ACTIVO" <?php echo ($estado == 'ACTIVO') ? 'selected' : ''; ?>>Activos</option>
                        <option value="INACTIVO" <?php echo ($estado == 'INACTIVO') ? 'selected' : ''; ?>>Inactivos</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabla de medicamentos -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Medicamentos</h5>
            
            <div class="btn-group" role="group">
                <a href="stock-bajo.php" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                </a>
                <a href="por-vencer.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-calendar-times"></i> Por Vencer
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (count($medicamentos) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Stock</th>
                            <th>Vencimiento</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicamentos as $medicamento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medicamento['FIDE_CODIGO_MEDICAMENTO']); ?></td>
                            <td><?php echo htmlspecialchars($medicamento['FIDE_NOMBRE_MEDICAMENTO']); ?></td>
                            <td><?php echo htmlspecialchars($medicamento['FIDE_NOMBRE_CATEGORIA']); ?></td>
                            <td class="text-end"><?php echo formatCurrency($medicamento['FIDE_PRECIO_UNITARIO']); ?></td>
                            <td class="text-center">
                                <?php 
                                $stock_class = 'badge bg-success';
                                $stock_text = $medicamento['FIDE_STOCK_ACTUAL'];
                                
                                if ($medicamento['ESTADO_STOCK'] == 'AGOTADO') {
                                    $stock_class = 'badge bg-danger';
                                } elseif ($medicamento['ESTADO_STOCK'] == 'BAJO') {
                                    $stock_class = 'badge bg-warning text-dark';
                                }
                                
                                echo "<span class=\"$stock_class\">$stock_text</span>";
                                ?>
                            </td>
                            <td>
                                <?php 
                                $vencimiento_class = 'badge bg-success';
                                $vencimiento_text = formatDate($medicamento['FIDE_FECHA_VENCIMIENTO'], 'd/m/Y');
                                
                                if ($medicamento['ESTADO_VENCIMIENTO'] == 'VENCIDO') {
                                    $vencimiento_class = 'badge bg-danger';
                                } elseif ($medicamento['ESTADO_VENCIMIENTO'] == 'POR VENCER') {
                                    $vencimiento_class = 'badge bg-warning text-dark';
                                }
                                
                                echo "<span class=\"$vencimiento_class\">$vencimiento_text</span>";
                                ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $estado_class = $medicamento['FIDE_ESTADO'] == 'ACTIVO' ? 'badge bg-success' : 'badge bg-secondary';
                                echo "<span class=\"$estado_class\">" . htmlspecialchars($medicamento['FIDE_ESTADO']) . "</span>";
                                ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="detalles.php?codigo=<?php echo urlencode($medicamento['FIDE_CODIGO_MEDICAMENTO']); ?>" 
                                       class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    
                                    <?php if (hasRole(['ADMINISTRADOR', 'FARMACEUTICO'])): ?>
                                    <a href="editar.php?codigo=<?php echo urlencode($medicamento['FIDE_CODIGO_MEDICAMENTO']); ?>" 
                                       class="btn btn-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="stock.php?codigo=<?php echo urlencode($medicamento['FIDE_CODIGO_MEDICAMENTO']); ?>" 
                                       class="btn btn-success btn-sm" title="Gestionar stock">
                                        <i class="fas fa-boxes"></i>
                                    </a>
                                    
                                    <?php if ($medicamento['FIDE_STOCK_ACTUAL'] > 0): ?>
                                    <a href="reservar.php?codigo=<?php echo urlencode($medicamento['FIDE_CODIGO_MEDICAMENTO']); ?>" 
                                       class="btn btn-warning btn-sm" title="Reservar">
                                        <i class="fas fa-cart-plus"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No se encontraron medicamentos con los filtros seleccionados.
            </div>
            <?php endif; ?>
        </div>
        <?php if (count($medicamentos) > 0): ?>
        <div class="card-footer">
            <div class="text-muted">
                Total de medicamentos: <?php echo count($medicamentos); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Incluir footer
include_once '../../includes/footer.php';
?>