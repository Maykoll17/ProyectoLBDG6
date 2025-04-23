/**
 * Archivo JavaScript principal
 * Sistema de Gestión Hospitalaria Pegasus
 */

// Cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Auto-cerrar alertas después de 5 segundos
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert.alert-success, .alert.alert-info');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Activar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Activar popovers de Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Prevenir reenvío de formularios al recargar la página
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
    // Verificar campos de contraseña
    const passwordFields = document.querySelectorAll('input[type="password"][data-match]');
    passwordFields.forEach(function(field) {
        const matchId = field.getAttribute('data-match');
        const matchField = document.getElementById(matchId);
        
        if (matchField) {
            field.addEventListener('keyup', function() {
                if (field.value === matchField.value) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                    matchField.classList.remove('is-invalid');
                    matchField.classList.add('is-valid');
                } else {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                    matchField.classList.remove('is-valid');
                    matchField.classList.add('is-invalid');
                }
            });
            
            matchField.addEventListener('keyup', function() {
                if (field.value === matchField.value) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                    matchField.classList.remove('is-invalid');
                    matchField.classList.add('is-valid');
                } else {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                    matchField.classList.remove('is-valid');
                    matchField.classList.add('is-invalid');
                }
            });
        }
    });
    
    // Habilitar datepickers
    const datepickers = document.querySelectorAll('.datepicker');
    datepickers.forEach(function(datepicker) {
        // Si se está usando DatePicker de Bootstrap, descomentar esta sección
        /*
        new bootstrap.Datepicker(datepicker, {
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });
        */
    });
    
    // Convertir inputs a mayúsculas
    const uppercaseInputs = document.querySelectorAll('input[data-uppercase]');
    uppercaseInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
    
    // Confirmar eliminación con bootstrap modal
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de que desea eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });
    
    // Añadir validación de formularios Bootstrap
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
});

// Función para formatear fechas
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}

// Función para formatear moneda
function formatCurrency(amount) {
    if (isNaN(amount)) return '$0.00';
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Función para mostrar una alerta de confirmación
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Función para validar formularios
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}