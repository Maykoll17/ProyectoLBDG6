
drop database pegasusbd;

create database pegasusbd;
USE pegasusbd;

create table usuarios(
	nbusuario varchar(50) PRIMARY KEY NOT NULL,
    contra varchar(50) NOT NULL,
    permisos varchar(50) NOT NULL
);



create table empleados(
	cedula varchar2(15) primary KEY not null,
    nombre varchar2(50) not null,
    apellidos varchar2(100) not null,
    telefono varchar2(15),
    direccion varchar2(200),
    correo varchar2(200),
    tipoEmpleado VARCHAR2(70) NOT NULL CHECK (tipoEmpleado in ('ADMINISTRADOR', 'ASISTENTE', 'CAJERO', 'DOCTOR', 'EMPLEADO', 'ENFERMERO', 'FARMACEUTICO', 'RECEPCIONISTA')),
    estado VARCHAR2(10) CHECK (estado IN ('ACTIVO', 'INACTIVO')),
    salarioPorHora NUMBER(12, 2)
);

create table pagos_empleados(
    cedula varchar2(15) not null,
    Remuneracion VARCHAR2(10) CHECK (Remuneracion IN ('Aplicado', 'Denegado')),
    constraint fk_empleados_cedula foreign key (cedula) references empleados (cedula)
);

create table pacientes(
	cedula varchar(15) primary KEY not null,
    nombre varchar(50) not null,
    apellidos varchar(100) not null,
    telefono varchar(15),
    direccion varchar(200),
    correo varchar(200),
    estado varchar(25) NOT NULL CHECK(estado in ('AMBULATORIO', 'HOSPITALIZADO', 'OBSERVACION', 'URGENCIAS','FALLECIDO')),
    deuda decimal(12, 2) not null
);

create table citas_pacientes_empleados(
    codigo int generated as identity primary key not null,
    cedulaPaciente varchar(15) not null,
    cedulaEmpleado varchar(15) not null,
    fecha date,
    sala int,
    asunto varchar(30) NOT NULL CHECK(asunto IN ('CIRUJIA', 'CONSULTA', 'EXAMEN', 'CONTROL', 'REHABILITACION', 'VACUNACION')),
    constraint fk_p_cedula FOREIGN KEY (cedulaPaciente) REFERENCES pacientes(cedula),
    constraint fk_e_cedula FOREIGN KEY (cedulaEmpleado) REFERENCES empleados(cedula)
);
CREATE TABLE Salas (
    codigo INT GENERATED AS IDENTITY PRIMARY KEY,
    capacidad VARCHAR(200) NOT NULL,
    tipoSala VARCHAR(30) NOT NULL CHECK (tipoSala IN ('Cuidados', 'Cuidados_intensivos', 'Espera', 'Operacion')),
    estado VARCHAR(30) NOT NULL CHECK (estado IN ('Disponible', 'Alquilada', 'Mantenimiento')),
    precioPorHora DECIMAL(10, 2) NOT NULL
);
CREATE TABLE Alquileres (
    codigo INT GENERATED AS IDENTITY PRIMARY KEY, 
    sala_codigo INT NOT NULL, 
    doctor VARCHAR(200) NOT NULL,
    fechaInicio DATE NOT NULL, 
    fechaFin DATE, 
    total DECIMAL(10, 2),
    FOREIGN KEY (sala_codigo) REFERENCES Salas(codigo)
);


CREATE TABLE medicamentos (
    codigo INT GENERATED AS IDENTITY PRIMARY KEY,
    nombre VARCHAR2(50) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL,
    informacion VARCHAR2(257) NOT NULL 
);
create table medsReservadosPacientes(
	codigo int generated as identity primary key,
    cedulaPaciente varchar(15) not null,
    codigoMed int not null,
    cantidad int not null,  
    constraint fk_cedulapaciente foreign key (cedulaPaciente) references pacientes(cedula),
    constraint  fk_codigomed foreign key (codigoMed) references medicamentos(codigo)
);

CREATE TABLE facturas (
    codigo INT GENERATED AS IDENTITY NOT NULL PRIMARY KEY, -- Reemplazo de AUTO_INCREMENT
    detalle VARCHAR2(30) NOT NULL CHECK (detalle IN ('MEDICAMENTO', 'CIRUJIA', 'CONSULTA', 'EXAMEN', 'CONTROL', 'REHABILITACION', 'VACUNACION')), -- Reemplazo de ENUM
    monto DECIMAL(12,2) NOT NULL,
    cedulaPaciente VARCHAR2(15) NOT NULL,
    estado VARCHAR2(15) NOT NULL CHECK (estado IN ('COBRADO', 'PENDIENTE')), -- Reemplazo de ENUM
    codigoMedReserva INT NOT NULL,
    CONSTRAINT fk_cedula_Paciente FOREIGN KEY (cedulaPaciente) REFERENCES pacientes(cedula),
    CONSTRAINT fk_codigoMedReserva FOREIGN KEY (codigoMedReserva) REFERENCES medsReservadosPacientes(codigo)
);/* Los insert solo funciona el Mysql

INSERT INTO empleados (cedula, nombre, apellidos, telefono, direccion, correo, tipoEmpleado, estado, salarioPorHora) VALUES
('123456789', 'Juan', 'Pérez González', '60123456', 'San José, Avenida Central', 'juan.perez@example.com', 'ADMINISTRADOR', 'ACTIVO', 2500.00),
('987654321', 'María', 'López Fernández', '60789012', 'Alajuela, Calle 2', 'maria.lopez@example.com', 'DOCTOR', 'ACTIVO', 3000.00),
('456123789', 'Carlos', 'Jiménez Rodríguez', '60567890', 'Cartago, Barrio El Carmen', 'carlos.jimenez@example.com', 'ENFERMERO', 'ACTIVO', 2200.00);

INSERT INTO pacientes (cedula, nombre, apellidos, telefono, direccion, correo, estado, deuda) VALUES
('321654987', 'Ana', 'Sánchez Morales', '61234567', 'Heredia, Plaza de la Paz', 'ana.sanchez@example.com', 'AMBULATORIO', 50.00),
('654987321', 'Luis', 'García Díaz', '61876543', 'Guanacaste, Playa Tamarindo', 'luis.garcia@example.com', 'HOSPITALIZADO', 200.00),
('789123456', 'Elena', 'Cruz Castillo', '61567890', 'Puntarenas, Paseo de los Turistas', 'elena.cruz@example.com', 'URGENCIAS', 100.00);

INSERT INTO citas_pacientes_empleados (cedulaPaciente, cedulaEmpleado, fecha, sala) VALUES
('321654987', '987654321', '2024-08-10 09:00:00', 1), -- Ana Sánchez con María López
('654987321', '456123789', '2024-08-11 10:30:00', 2), -- Luis García con Carlos Jiménez
('789123456', '123456789', '2024-08-12 14:00:00', 3); -- Elena Cruz con Juan Pérez

INSERT INTO citas_pacientes_empleados (cedulaPaciente, cedulaEmpleado, fecha, sala) VALUES
('321654987', '123456789', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 9 HOUR), 1), -- Cita 1 a las 09:00 AM
('321654987', '123456789', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 10 HOUR), 2), -- Cita 2 a las 10:00 AM
('321654987', '123456789', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 11 HOUR), 3), -- Cita 3 a las 11:00 AM
('321654987', '123456789', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 12 HOUR), 4); -- Cita 4 a las 12:00 PM

INSERT INTO citas_pacientes_empleados (cedulaPaciente, cedulaEmpleado, fecha, sala) VALUES
('321654987', '123456789', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 9 HOUR), 1), -- Cita 1 a las 09:00 AM
('321654987', '123456789', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 10 HOUR), 2); -- Cita 2 a las 10:00 AM

INSERT INTO Salas (capacidad, tipoSala, estado, precioPorHora) VALUES
('10', 'Espera', 'Mantenimiento', '3500'),
('15', 'Operacion', 'Disponible', '12000'),
('20', 'Cuidados', 'Alquilada', '5000'),
('6', 'Espera', 'Disponible', '2500'),
('12', 'Cuidados_intensivos', 'Mantenimiento', '9000'),
('8', 'Operacion', 'Alquilada', '10000'),
('7', 'Cuidados', 'Mantenimiento', '4000'),
('10', 'Operacion', 'Disponible', '11000');

INSERT INTO Alquileres (sala_codigo, doctor, fechaInicio, fechaFin, total)
VALUES (1, 'Dr. Juan Pérez', '2024-08-18 09:00:00', '2024-08-18 11:00:00', 150.00);

INSERT INTO medicamentos (nombre, precio, cantidad) VALUES
('N/A', 0, 0),
('Paracetamol', 5.50, 100),
('Ibuprofeno', 7.75, 50),
('Amoxicilina', 12.00, 75),
('Ciproflaxina', 15.25, 30),
('Omeprazol', 10.50, 60);

INSERT INTO medsReservadosPacientes (cedulaPaciente, codigoMed, cantidad) VALUES
('321654987', 1, 3),  -- Ana Sánchez Morales reserva 3 unidades de Paracetamol
('654987321', 2, 5),  -- Luis García Díaz reserva 5 unidades de Ibuprofeno
('789123456', 3, 2),  -- Elena Cruz Castillo reserva 2 unidades de Amoxicilina
('321654987', 4, 1),  -- Ana Sánchez Morales reserva 1 unidad de Ciproflaxina
('654987321', 5, 4);  -- Luis García Díaz reserva 4 unidades de Omeprazol


INSERT INTO facturas (detalle, monto, cedulaPaciente, estado, codigoMedReserva) VALUES
('MEDICAMENTO', 150.00, '321654987', 'COBRADO', 1),
('CONSULTA', 200.00, '654987321', 'PENDIENTE', 2),
('EXAMEN', 80.00, '789123456', 'COBRADO', 2),
('REHABILITACION', 120.00, '321654987', 'PENDIENTE', 2),
('VACUNACION', 90.00, '654987321', 'COBRADO', 1);



INSERT INTO usuarios (nbusuario, contra, permisos) VALUES ("a", "a", "TTTTTTTT");

select * from citas_pacientes_empleados;
DELETE FROM citas_pacientes_empleados WHERE cedulaPaciente = '321654987';

-- select * from medicamentos;
-- select * from medsReservadosPacientes;
INSERT INTO usuarios (nbusuario, contra, permisos) VALUES ("root", "SGHPegasus", "TTTTTTTT");
select * from usuarios;
-- select * from empleados;
select * from pacientes;

delete from pacientes where cedula = '321654987';

SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_NAME = 'pacientes' AND TABLE_SCHEMA = 'pegasusbd';

SELECT * FROM 
 WHERE cedulaPaciente = '321654987';*/




------------------
CRUD para la tabla Empleados
------------------

CREATE OR REPLACE PROCEDURE sp_agregar_empleado(
    cedula IN VARCHAR2, 
    nombre IN VARCHAR2, 
    apellidos IN VARCHAR2, 
    telefono IN VARCHAR2, 
    direccion IN VARCHAR2, 
    correo IN VARCHAR2, 
    tipo_empleado IN VARCHAR2, 
    estado IN VARCHAR2, 
    salarioPorHora IN NUMBER)
IS
BEGIN
    INSERT INTO empleados (cedula, nombre, apellidos, telefono, direccion, 
    correo, tipoEmpleado, estado, salarioPorHora)
    VALUES (cedula, nombre, apellidos, telefono, direccion, correo, 
    tipo_empleado, estado, salarioPorHora);
END;


CREATE OR REPLACE PROCEDURE sp_modificar_empleado(
    cedula IN VARCHAR2, 
    nombre IN VARCHAR2, 
    apellidos IN VARCHAR2, 
    telefono IN VARCHAR2, 
    direccion IN VARCHAR2, 
    correo IN VARCHAR2, 
    tipo_empleado IN VARCHAR2, 
    estado IN VARCHAR2, 
    salarioPorHora IN NUMBER)
IS
BEGIN
    UPDATE empleados
    SET nombre = nombre, 
        apellidos = apellidos, 
        telefono = telefono, 
        direccion = direccion, 
        correo = correo, 
        tipoEmpleado = tipo_empleado, 
        estado = estado, 
        salarioPorHora = salarioPorHora
    WHERE cedula = cedula;
END;


CREATE OR REPLACE PROCEDURE sp_eliminar_empleado(
    cedula IN VARCHAR2)
IS
BEGIN
    DELETE FROM empleados WHERE cedula = cedula;
END;


CREATE OR REPLACE PROCEDURE sp_consultar_empleados
IS
BEGIN
    FOR rec IN (SELECT * FROM empleados) LOOP
        
        DBMS_OUTPUT.PUT_LINE('Cédula: ' || rec.cedula || ', Nombre: ' || rec.nombre);
    END LOOP;
END;


------------------
CRUD para la tabla Pacientes
------------------

CREATE OR REPLACE PROCEDURE sp_agregar_paciente(
    cedula IN VARCHAR2, 
    nombre IN VARCHAR2, 
    apellidos IN VARCHAR2, 
    telefono IN VARCHAR2, 
    direccion IN VARCHAR2, 
    correo IN VARCHAR2, 
    estado IN VARCHAR2)
IS
BEGIN
    INSERT INTO pacientes (cedula, nombre, apellidos, telefono, direccion, 
    correo, estado)
    VALUES (cedula, nombre, apellidos, telefono, direccion, correo, estado);
END;


CREATE OR REPLACE PROCEDURE sp_modificar_paciente(
    cedula IN VARCHAR2, 
    nombre IN VARCHAR2, 
    apellidos IN VARCHAR2, 
    telefono IN VARCHAR2, 
    direccion IN VARCHAR2, 
    correo IN VARCHAR2, 
    estado IN VARCHAR2)
IS
BEGIN
    UPDATE pacientes
    SET nombre = nombre, 
        apellidos = apellidos, 
        telefono = telefono, 
        direccion = direccion, 
        correo = correo, 
        estado = estado
    WHERE cedula = cedula;
END;


CREATE OR REPLACE PROCEDURE sp_eliminar_paciente(
    cedula IN VARCHAR2)
IS
BEGIN
    DELETE FROM pacientes WHERE cedula = cedula;
END;


CREATE OR REPLACE PROCEDURE sp_consultar_pacientes
IS
BEGIN
    FOR rec IN (SELECT * FROM pacientes) LOOP
        
        DBMS_OUTPUT.PUT_LINE('Cédula: ' || rec.cedula || ', Nombre: ' || rec.nombre);
    END LOOP;
END;


------------------
Vistas de empleados y pacientes
------------------
CREATE OR REPLACE VIEW vista_empleados_activos AS
SELECT * FROM empleados WHERE estado = 'ACTIVO';

CREATE OR REPLACE VIEW vista_pacientes_con_deuda AS
SELECT * FROM pacientes WHERE deuda > 0;

------------------
Función para calcular la deuda total de un paciente
------------------
CREATE OR REPLACE FUNCTION fn_calcular_deuda(cedula IN VARCHAR2) 
RETURN NUMBER
IS
    total_deuda NUMBER(10,2);
BEGIN
    SELECT SUM(monto) INTO total_deuda 
    FROM facturas 
    WHERE cedulaPaciente = cedula AND estado = 'PENDIENTE';
    
    RETURN NVL(total_deuda, 0);
END;


------------------


