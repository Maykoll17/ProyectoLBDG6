<?php
/**
 * Página para cambiar contraseña
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Verificar sesión
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
require_once '../../models/perfil.php';

// Comprobar si el usuario está logueado
if (!isLoggedIn()) {
    redirect('pages/login.php');
}

// Obtener ID de usuario
$usuario_id = $_SESSION['user_id'];

// Procesar formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        showError("Error de seguridad: token inválido");
    } else {
        // Obtener y sanitizar datos
        $clave_actual = $_POST['clave_actual'];
        $clave_nueva = $_POST['clave_nueva'];
        $clave_confirmacion = $_POST['clave_confirmacion'];
        
        // Cambiar contraseña
        if (cambiarClaveUsuario($usuario_id, $clave_actual, $clave_nueva, $clave_confirmacion)) {
            // Registrar actividad
            registrarActividad($usuario_id, 'PERFIL', 'Cambio de contraseña');
            
            // Redirigir a la página de perfil
            redirect('pages/perfil/index.php');
        }
    }
}

// Incluir header
$pageTitle = "Cambiar Contraseña";
include_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-key"></i> Cambiar Contraseña</h5>
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
                    
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="clave_actual" class="form-label">Contraseña Actual</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="clave_actual" name="clave_actual" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="clave_actual">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="clave_nueva" class="form-label">Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="clave_nueva" name="clave_nueva" required 
                                       minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="clave_nueva">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="clave_confirmacion" class="form-label">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="clave_confirmacion" name="clave_confirmacion" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="clave_confirmacion">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cambiar Contraseña
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al Perfil
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para mostrar/ocultar contraseña -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                inputField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Verificar que las contraseñas coincidan
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        const nuevaClave = document.getElementById('clave_nueva').value;
        const confirmacionClave = document.getElementById('clave_confirmacion').value;
        
        if (nuevaClave !== confirmacionClave) {
            event.preventDefault();
            alert('Las contraseñas no coinciden');
        }
    });
});
</script>

<?php
// Incluir footer
include_once '../../includes/footer.php';
?>