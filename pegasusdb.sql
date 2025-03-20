-- Tabla de Roles
CREATE TABLE roles (
    id NUMBER PRIMARY KEY,
    nombre VARCHAR2(50) UNIQUE NOT NULL
);

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id NUMBER PRIMARY KEY,
    nombre_usuario VARCHAR2(50) UNIQUE NOT NULL,
    contrasena VARCHAR2(100) NOT NULL,
    rol_id NUMBER NOT NULL,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de Tipos de Empleados
CREATE TABLE tipos_empleados (
    id NUMBER PRIMARY KEY,
    tipo VARCHAR2(50) UNIQUE NOT NULL
);

-- Tabla de Empleados
CREATE TABLE empleados (
    cedula VARCHAR2(15) PRIMARY KEY,
    nombre VARCHAR2(50) NOT NULL,
    apellidos VARCHAR2(100) NOT NULL,
    telefono VARCHAR2(15),
    direccion VARCHAR2(200),
    correo VARCHAR2(200),
    tipo_empleado_id NUMBER NOT NULL,
    estado VARCHAR2(20) DEFAULT 'ACTIVO',
    salario_por_hora NUMBER(12,2),
    CONSTRAINT fk_empleado_tipo FOREIGN KEY (tipo_empleado_id) REFERENCES tipos_empleados(id)
);

-- Tabla de Estados de Pacientes
CREATE TABLE estados_pacientes (
    id NUMBER PRIMARY KEY,
    estado VARCHAR2(50) UNIQUE NOT NULL
);

-- Tabla de Pacientes
CREATE TABLE pacientes (
    cedula VARCHAR2(15) PRIMARY KEY,
    nombre VARCHAR2(50) NOT NULL,
    apellidos VARCHAR2(100) NOT NULL,
    telefono VARCHAR2(15),
    direccion VARCHAR2(200),
    correo VARCHAR2(200),
    estado_paciente_id NUMBER NOT NULL,
    deuda NUMBER(12,2) NOT NULL,
    CONSTRAINT fk_paciente_estado FOREIGN KEY (estado_paciente_id) REFERENCES estados_pacientes(id)
);

-- Secuencia y trigger para citas
CREATE SEQUENCE seq_citas START WITH 1 INCREMENT BY 1;

CREATE TABLE citas (
    id NUMBER PRIMARY KEY,
    cedula_paciente VARCHAR2(15) NOT NULL,
    cedula_empleado VARCHAR2(15) NOT NULL,
    fecha TIMESTAMP NOT NULL,
    sala_id NUMBER NOT NULL,
    motivo VARCHAR2(50) NOT NULL,
    CONSTRAINT fk_cita_paciente FOREIGN KEY (cedula_paciente) REFERENCES pacientes(cedula),
    CONSTRAINT fk_cita_empleado FOREIGN KEY (cedula_empleado) REFERENCES empleados(cedula),
    CONSTRAINT fk_cita_sala FOREIGN KEY (sala_id) REFERENCES salas(id)
);
CREATE OR REPLACE TRIGGER trg_citas
BEFORE INSERT ON citas
FOR EACH ROW
BEGIN
    SELECT seq_citas.NEXTVAL INTO :NEW.id FROM DUAL;
END;
/

-- Tabla de Tipos de Salas
CREATE TABLE tipos_salas (
    id NUMBER PRIMARY KEY,
    tipo VARCHAR2(50) UNIQUE NOT NULL
);

-- Tabla de Estados de Salas
CREATE TABLE estados_salas (
    id NUMBER PRIMARY KEY,
    estado VARCHAR2(50) UNIQUE NOT NULL
);

-- Tabla de Salas
CREATE TABLE salas (
    id NUMBER GENERATED AS IDENTITY PRIMARY KEY,
    capacidad NUMBER NOT NULL,
    tipo_sala_id NUMBER NOT NULL,
    estado_sala_id NUMBER NOT NULL,
    precio_por_hora NUMBER(10,2) NOT NULL,
    CONSTRAINT fk_sala_tipo FOREIGN KEY (tipo_sala_id) REFERENCES tipos_salas(id),
    CONSTRAINT fk_sala_estado FOREIGN KEY (estado_sala_id) REFERENCES estados_salas(id)
);

-- Tabla de Alquileres
CREATE TABLE alquileres (
    id NUMBER GENERATED AS IDENTITY PRIMARY KEY,
    sala_id NUMBER NOT NULL,
    doctor VARCHAR2(200) NOT NULL,
    fecha_inicio TIMESTAMP NOT NULL,
    fecha_fin TIMESTAMP,
    total NUMBER(10,2),
    CONSTRAINT fk_alquiler_sala FOREIGN KEY (sala_id) REFERENCES salas(id)
);

-- Tabla de Medicamentos
CREATE TABLE medicamentos (
    id NUMBER GENERATED AS IDENTITY PRIMARY KEY,
    nombre VARCHAR2(50) NOT NULL,
    precio NUMBER(10,2) NOT NULL,
    cantidad NUMBER NOT NULL,
    informacion VARCHAR2(257) NOT NULL 
);

-- Tabla de Medicamentos Reservados
CREATE TABLE medicamentos_reservados (
    id NUMBER GENERATED AS IDENTITY PRIMARY KEY,
    cedula_paciente VARCHAR2(15) NOT NULL,
    medicamento_id NUMBER NOT NULL,
    cantidad NUMBER NOT NULL,
    CONSTRAINT fk_meds_reservados_paciente FOREIGN KEY (cedula_paciente) REFERENCES pacientes(cedula),
    CONSTRAINT fk_meds_reservados_med FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
);

-- Tabla de Facturas
CREATE TABLE facturas (
    id NUMBER GENERATED AS IDENTITY PRIMARY KEY,
    cedula_paciente VARCHAR2(15) NOT NULL,
    estado VARCHAR2(15) NOT NULL CHECK (estado IN ('COBRADO', 'PENDIENTE')),
    total NUMBER(12,2) NOT NULL,
    CONSTRAINT fk_factura_paciente FOREIGN KEY (cedula_paciente) REFERENCES pacientes(cedula)
);

-- Tabla de Detalles de Facturas
CREATE TABLE detalles_facturas (
    id NUMBER GENERATED AS IDENTITY PRIMARY KEY,
    factura_id NUMBER NOT NULL,
    descripcion VARCHAR2(200) NOT NULL,
    monto NUMBER(12,2) NOT NULL,
    CONSTRAINT fk_detalles_factura FOREIGN KEY (factura_id) REFERENCES facturas(id)
);
