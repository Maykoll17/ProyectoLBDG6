# Pegasus - Sistema de Gestión Hospitalaria

Sistema web para la gestión hospitalaria desarrollado con PHP y Oracle. Este sistema permite administrar pacientes, citas, medicamentos, salas, facturación y generación de reportes.

## Características

- Gestión de pacientes y su historial médico
- Agendamiento y seguimiento de citas médicas
- Control de inventario de medicamentos
- Administración de salas y quirófanos
- Sistema de facturación y pagos
- Generación de reportes
- Control de acceso basado en roles

## Requisitos del sistema

- Servidor web (probar en dos)
- PHP 8.2 NO ANTERIOR
- Oracle Database
- Extensión OCI8 para PHP (NO NECESARIA CON EL XAMPP 8.2)

## Instalación

1. Clonar o descargar el repositorio en la carpeta de su servidor web (htdocs)
2. Crear la base de datos Oracle utilizando los scripts proporcionados en `/database/scripts`
3. Configurar los parámetros de conexión en `/includes/config.php`
4. Asegurar que la carpeta `/logs` tenga permisos de escritura
5. Acceder al sistema a través de un navegador web

## Estructura del proyecto

```
/project-root
│
├── /assets
│   ├── /css
│   ├── /js
│   └── /img
│
├── /includes
│   ├── config.php
│   ├── functions.php
│   ├── Database.php
│   ├── header.php
│   └── footer.php
│
├── /models
│   ├── auth.php
│   ├── pacientes.php
│   ├── citas.php
│   └── ...
│
├── /pages
│   ├── login.php
│   ├── dashboard.php
│   └── ...
│
├── /logs
│   └── error.log
│
├── index.php
└── README.md
```

## Configuración

Para configurar el sistema, edite el archivo `/includes/config.php` y ajuste los parámetros según sea necesario:

```php
$db_config = [
    'username' => 'su_usuario',
    'password' => 'su_contraseña',
    'connection_string' => 'localhost/XE'
];
```

## Paquetes PL/SQL utilizados

El sistema interactúa con los siguientes paquetes PL/SQL en Oracle:

- FIDE_AUTENTICACION_PKG: Manejo de autenticación de usuarios
- FIDE_PACIENTES_PKG: Gestión de pacientes
- FIDE_CITAS_PKG: Agendamiento de citas
- FIDE_MEDICAMENTOS_PKG: Control de medicamentos
- FIDE_SALAS_PKG: Administración de salas
- FIDE_FACTURACION_PKG: Sistema de facturación
- FIDE_REPORTES_PKG: Generación de reportes

## Usuarios por defecto

- Usuario: admin
- Contraseña: admin123

