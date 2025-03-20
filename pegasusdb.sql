drop database pegasusbd;

create database pegasusbd;
USE pegasusbd;

create table usuarios(
	nbusuario varchar(50) PRIMARY KEY NOT NULL,
    contra varchar(50) NOT NULL,
    permisos varchar(50) NOT NULL
);



create table empleados(
	cedula varchar(15) primary KEY not null,
    nombre varchar(50) not null,
    apellidos varchar(100) not null,
    telefono varchar(15),
    direccion varchar(200),
    correo varchar(200),
    tipoEmpleado enum("ADMINISTRADOR", "ASISTENTE", "CAJERO", "DOCTOR", "EMPLEADO", "ENFERMERO", "FARMACEUTICO", "RECEPCIONISTA") not null,
    estado enum("ACTIVO", "INACTIVO"),
    salarioPorHora decimal(12, 2)
);

create table pacientes(
	cedula varchar(15) primary KEY not null,
    nombre varchar(50) not null,
    apellidos varchar(100) not null,
    telefono varchar(15),
    direccion varchar(200),
    correo varchar(200),
    estado enum("AMBULATORIO", "HOSPITALIZADO", "OBSERVACION", "URGENCIAS"),
    deuda decimal(12, 2) not null
);

create table citas_pacientes_empleados(
	codigo int auto_increment primary KEY not null,
	cedulaPaciente varchar(15) not null,
    cedulaEmpleado varchar(15) not null,
    fecha datetime,
    sala int,
    asunto enum("CIRUJIA", "CONSULTA", "EXAMEN", "CONTROL", "REHABILITACION", "VACUNACION"),
    FOREIGN KEY (cedulaPaciente) REFERENCES pacientes(cedula),
    FOREIGN KEY (cedulaEmpleado) REFERENCES empleados(cedula)
);

CREATE TABLE Salas (
	codigo INT AUTO_INCREMENT PRIMARY KEY,
    capacidad VARCHAR(200) NOT NULL,
    tipoSala ENUM("Cuidados", "Cuidados_intensivos", "Espera", "Operacion") NOT NULL,
    estado ENUM("Disponible", "Alquilada", "Mantenimiento") NOT NULL,
    precioPorHora DECIMAL(10, 2) NOT NULL
);

CREATE TABLE Alquileres (
	codigo INT AUTO_INCREMENT PRIMARY KEY, -- Para cada alqiuiler, es auto incrementable
    sala_codigo INT NOT NULL, -- Codigo de la bicicleta que se alquila
    doctor VARCHAR(200) NOT NULL,
    fechaInicio DATETIME NOT NULL,
	fechaFin DATETIME,
    total DECIMAL(10, 2),
	FOREIGN KEY (sala_codigo) REFERENCES Salas(codigo)
);





create table medicamentos(
	codigo int auto_increment primary key,
    nombre varchar(50) not null, 
    precio decimal(10,2) not null,
    cantidad int not null
);

create table medsReservadosPacientes(
	codigo int auto_increment primary key,
    cedulaPaciente varchar(15) not null,
    codigoMed int not null,
    cantidad int not null,  
    foreign key (cedulaPaciente) references pacientes(cedula),
    foreign key (codigoMed) references medicamentos(codigo)
);


create table facturas(
	codigo int auto_increment not null primary key,
    detalle ENUM('MEDICAMENTO', 'CIRUJIA', 'CONSULTA', 'EXAMEN', 'CONTROL', 'REHABILITACION', 'VACUNACION') NOT NULL,
    monto decimal(12,2) not null,
    cedulaPaciente varchar(15) not null,
    estado ENUM("COBRADO", "PENDIENTE") not null,
    codigoMedReserva int not null,
    foreign key (cedulaPaciente) references pacientes(cedula),
    foreign key (codigoMedReserva) references medsReservadosPacientes(codigo)
);

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
 WHERE cedulaPaciente = '321654987';


------------------
CRUD para la tabla Empleados
------------------
DELIMITER//

CREATE PROCEDURE sp_agregar_empleado(IN cedula VARCHAR(20), IN nombre 
VARCHAR(50), IN apellidos VARCHAR(50), IN telefono VARCHAR(15), IN direccion 
VARCHAR(100), IN correo VARCHAR(50), IN tipo_empleado VARCHAR(20), IN estado 
VARCHAR(20), IN salarioPorHora DECIMAL(10,2))
BEGIN
    INSERT INTO empleados (cedula, nombre, apellidos, telefono, direccion, 
    correo, tipoEmpleado, estado, salarioPorHora)
    VALUES (cedula, nombre, apellidos, telefono, direccion, correo, 
    tipo_empleado, estado, salarioPorHora);
END //

CREATE PROCEDURE sp_modificar_empleado(IN cedula VARCHAR(20), IN nombre 
VARCHAR(50), IN apellidos VARCHAR(50), IN telefono VARCHAR(15), IN direccion 
VARCHAR(100), IN correo VARCHAR(50), IN tipo_empleado VARCHAR(20), IN estado 
VARCHAR(20), IN salarioPorHora DECIMAL(10,2))
BEGIN
    UPDATE empleados
    SET nombre = nombre, apellidos = apellidos, telefono = telefono, 
    direccion = direccion, correo = correo, tipoEmpleado = tipo_empleado, 
    estado = estado, salarioPorHora = salarioPorHora
    WHERE cedula = cedula;
END //

CREATE PROCEDURE sp_eliminar_empleado(IN cedula VARCHAR(20))
BEGIN
    DELETE FROM empleados WHERE cedula = cedula;
END //

CREATE PROCEDURE sp_consultar_empleados()
BEGIN
    SELECT * FROM empleados;
END //

DELIMITER ;

------------------
CRUD para la tabla Pacientes
------------------
DELIMITER //

CREATE PROCEDURE sp_agregar_paciente(IN cedula VARCHAR(20), 
IN nombre VARCHAR(50), IN apellidos VARCHAR(50), IN telefono VARCHAR(15), 
IN direccion VARCHAR(100), IN correo VARCHAR(50), IN estado VARCHAR(20))
BEGIN
    INSERT INTO pacientes (cedula, nombre, apellidos, telefono, direccion, 
    correo, estado)
    VALUES (cedula, nombre, apellidos, telefono, direccion, correo, estado);
END //

CREATE PROCEDURE sp_modificar_paciente(IN cedula VARCHAR(20), 
IN nombre VARCHAR(50), IN apellidos VARCHAR(50), IN telefono VARCHAR(15), 
IN direccion VARCHAR(100), IN correo VARCHAR(50), IN estado VARCHAR(20))
BEGIN
    UPDATE pacientes
    SET nombre = nombre, apellidos = apellidos, telefono = telefono, 
    direccion = direccion, correo = correo, estado = estado
    WHERE cedula = cedula;
END //

CREATE PROCEDURE sp_eliminar_paciente(IN cedula VARCHAR(20))
BEGIN
    DELETE FROM pacientes WHERE cedula = cedula;
END //

CREATE PROCEDURE sp_consultar_pacientes()
BEGIN
    SELECT * FROM pacientes;
END //

DELIMITER ;

------------------
Vistas de empleados y pacientes
------------------
CREATE VIEW vista_empleados_activos AS
SELECT * FROM empleados WHERE estado = 'ACTIVO';

CREATE VIEW vista_pacientes_con_deuda AS
SELECT * FROM pacientes WHERE deuda > 0;

------------------
Funcion para calcular la deuda total de un paciente
------------------
CREATE FUNCTION fn_calcular_deuda(cedula VARCHAR(20)) RETURNS DECIMAL(10,2)
BEGIN
    DECLARE total_deuda DECIMAL(10,2);
    SELECT SUM(monto) INTO total_deuda FROM facturas WHERE cedulaPaciente = 
    cedula AND estado = 'PENDIENTE';
    RETURN total_deuda;
END;

------------------
Trigger para actualizar el estado del paciente
------------------
CREATE TRIGGER trigger_actualizar_estado_empleado
BEFORE DELETE ON empleados
FOR EACH ROW
BEGIN
    SET OLD.estado = 'INACTIVO';
END;


