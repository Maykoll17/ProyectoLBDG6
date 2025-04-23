-- Creación de Vistas
-- Vista de empleados activos
CREATE OR REPLACE VIEW vista_empleados_activos AS
SELECT * FROM empleados WHERE estado = 'ACTIVO';

-- Vista de pacientes con deudas pendientes
CREATE OR REPLACE VIEW vista_pacientes_con_deuda AS
SELECT p.*, ep.estado FROM pacientes p
JOIN estados_pacientes ep ON p.estado_paciente_id = ep.id
WHERE p.deuda > 0;

-- Vista de historial de citas
CREATE OR REPLACE VIEW vista_historial_citas AS
SELECT 
    c.id, 
    c.cedula_paciente, 
    c.cedula_empleado, 
    c.fecha, 
    c.motivo,
    p.nombre AS nombre_paciente, 
    e.nombre AS nombre_empleado
FROM citas c
JOIN pacientes p ON c.cedula_paciente = p.cedula
JOIN empleados e ON c.cedula_empleado = e.cedula;

-- Vista de disponibilidad de salas
CREATE OR REPLACE VIEW vista_disponibilidad_salas AS
SELECT s.id, s.capacidad, ts.tipo, es.estado
FROM salas s
JOIN tipos_salas ts ON s.tipo_sala_id = ts.id
JOIN estados_salas es ON s.estado_sala_id = es.id
WHERE es.estado = 'Disponible';

-- Vista de estado de facturación de pacientes
CREATE OR REPLACE VIEW vista_estado_facturacion AS
SELECT f.id, p.nombre, f.total, f.estado
FROM facturas f
JOIN pacientes p ON f.cedula_paciente = p.cedula;

--CURSORES (5 VAN DE LA MANO CON LAS VISTAS, Y OTROS 3 PARA COMPLETAR EL AVANCE, LUEGO IMPLEMENTAMOS EL RESTO CON LAS VISTAS
--FALTANTES O TOTALMENTE INDEPENDIENTES DE LAS VISTAS, DEPENDIENDO DE LO QUE DIGA LA PROFE)

--empleados activos (vista_empleados_activos)
DECLARE 
    CURSOR cur_empleados_activos IS 
        SELECT cedula, nombre, apellidos, estado FROM vista_empleados_activos;
    
    v_cedula empleados.cedula%TYPE;
    v_nombre empleados.nombre%TYPE;
    v_apellidos empleados.apellidos%TYPE;
    v_estado empleados.estado%TYPE;

BEGIN
    OPEN cur_empleados_activos;

    LOOP
        FETCH cur_empleados_activos INTO v_cedula, v_nombre, v_apellidos, v_estado;
        EXIT WHEN cur_empleados_activos%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Empleado: ' || v_nombre || ' ' || v_apellidos || ' | Estado: ' || v_estado);
    END LOOP;

    CLOSE cur_empleados_activos;
END;
/


--pacientes con deuda (vista_pacientes_con_deuda)
DECLARE
    CURSOR cur_pacientes_con_deuda IS 
        SELECT * FROM vista_pacientes_con_deuda;

    v_cedula pacientes.cedula%TYPE;
    v_nombre pacientes.nombre%TYPE;
    v_estado pacientes.estado_paciente_id%TYPE;
    v_deuda pacientes.deuda%TYPE;

BEGIN
    OPEN cur_pacientes_con_deuda;

    LOOP
        FETCH cur_pacientes_con_deuda INTO v_cedula, v_nombre, v_estado, v_deuda;
        EXIT WHEN cur_pacientes_con_deuda%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Paciente: ' || v_nombre || ' | Estado: ' || v_estado || ' | Deuda: ' || v_deuda);
    END LOOP;

    CLOSE cur_pacientes_con_deuda;
END;
/



--historial de citas (vista_historial_citas)
DECLARE
    CURSOR cur_historial_citas IS 
        SELECT * FROM vista_historial_citas;

    v_id citas.id%TYPE;
    v_paciente citas.cedula_paciente%TYPE;
    v_empleado citas.cedula_empleado%TYPE;
    v_fecha citas.fecha%TYPE;
    v_motivo citas.motivo%TYPE;
    v_nombre_paciente pacientes.nombre%TYPE;
    v_nombre_empleado empleados.nombre%TYPE;


BEGIN
    OPEN cur_historial_citas;

    LOOP
        FETCH cur_historial_citas INTO v_id, v_paciente, v_empleado, v_fecha, v_motivo, v_nombre_paciente, v_nombre_empleado;

        EXIT WHEN cur_historial_citas%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Cita ID: ' || v_id || ' | Paciente: ' || v_paciente || ' | Empleado: ' || v_empleado || ' | Fecha: ' || v_fecha || ' | Motivo: ' || v_motivo);
    END LOOP;

    CLOSE cur_historial_citas;
END;
/


-- salas disponibles (vista_disponibilidad_salas)

DECLARE
    CURSOR cur_salas_disponibles IS 
        SELECT * FROM vista_disponibilidad_salas;

    v_id salas.id%TYPE;
    v_capacidad salas.capacidad%TYPE;
    v_tipo salas.tipo_sala_id%TYPE;
    v_estado salas.estado_sala_id%TYPE;

BEGIN
    OPEN cur_salas_disponibles;

    LOOP
        FETCH cur_salas_disponibles INTO v_id, v_capacidad, v_tipo, v_estado;
        EXIT WHEN cur_salas_disponibles%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Sala ID: ' || v_id || ' | Capacidad: ' || v_capacidad || ' | Tipo: ' || v_tipo || ' | Estado: ' || v_estado);
    END LOOP;

    CLOSE cur_salas_disponibles;
END;
/

--estado de facturación (vista_estado_facturacion)

DECLARE
    CURSOR cur_estado_facturacion IS 
        SELECT * FROM vista_estado_facturacion;

    v_id facturas.id%TYPE;
    v_paciente facturas.cedula_paciente%TYPE;
    v_total facturas.total%TYPE;
    v_estado facturas.estado%TYPE;

BEGIN
    OPEN cur_estado_facturacion;

    LOOP
        FETCH cur_estado_facturacion INTO v_id, v_paciente, v_total, v_estado;
        EXIT WHEN cur_estado_facturacion%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Factura ID: ' || v_id || ' | Paciente: ' || v_paciente || ' | Total: ' || v_total || ' | Estado: ' || v_estado);
    END LOOP;

    CLOSE cur_estado_facturacion;
END;
/


--medicamentos disponibles

DECLARE
    CURSOR cur_medicamentos_disponibles IS 
        SELECT id, nombre, cantidad FROM medicamentos WHERE cantidad > 0;

    v_id medicamentos.id%TYPE;
    v_nombre medicamentos.nombre%TYPE;
    v_cantidad medicamentos.cantidad%TYPE;

BEGIN
    OPEN cur_medicamentos_disponibles;

    LOOP
        FETCH cur_medicamentos_disponibles INTO v_id, v_nombre, v_cantidad;
        EXIT WHEN cur_medicamentos_disponibles%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Medicamento: ' || v_nombre || ' | Cantidad disponible: ' || v_cantidad);
    END LOOP;

    CLOSE cur_medicamentos_disponibles;
END;
/

--alquileres de salas

DECLARE
    CURSOR cur_alquileres IS 
        SELECT id, sala_id, doctor, fecha_inicio, fecha_fin FROM alquileres;

    v_id alquileres.id%TYPE;
    v_sala alquileres.sala_id%TYPE;
    v_doctor alquileres.doctor%TYPE;
    v_inicio alquileres.fecha_inicio%TYPE;
    v_fin alquileres.fecha_fin%TYPE;

BEGIN
    OPEN cur_alquileres;

    LOOP
        FETCH cur_alquileres INTO v_id, v_sala, v_doctor, v_inicio, v_fin;
        EXIT WHEN cur_alquileres%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Alquiler ID: ' || v_id || ' | Sala: ' || v_sala || ' | Doctor: ' || v_doctor || ' | Inicio: ' || v_inicio || ' | Fin: ' || v_fin);
    END LOOP;

    CLOSE cur_alquileres;
END;
/

-- pacientes hospitalizados

DECLARE
    CURSOR cur_pacientes_hospitalizados IS 
        SELECT p.cedula, p.nombre, ep.estado 
        FROM pacientes p
        JOIN estados_pacientes ep ON p.estado_paciente_id = ep.id
        WHERE ep.estado = 'HOSPITALIZADO';

    v_cedula pacientes.cedula%TYPE;
    v_nombre pacientes.nombre%TYPE;
    v_estado estados_pacientes.estado%TYPE;

BEGIN
    OPEN cur_pacientes_hospitalizados;

    LOOP
        FETCH cur_pacientes_hospitalizados INTO v_cedula, v_nombre, v_estado;
        EXIT WHEN cur_pacientes_hospitalizados%NOTFOUND;

        DBMS_OUTPUT.PUT_LINE('Paciente: ' || v_nombre || ' | Estado: ' || v_estado);
    END LOOP;

    CLOSE cur_pacientes_hospitalizados;
END;
/



-- FUNCIONES 
CREATE OR REPLACE FUNCTION fn_calcular_deuda(cedula_paciente IN VARCHAR2) RETURN NUMBER IS
    total_deuda NUMBER(12,2);
BEGIN
    SELECT SUM(total) INTO total_deuda FROM facturas WHERE cedula_paciente = cedula_paciente AND estado = 'PENDIENTE';
    RETURN NVL(total_deuda, 0);
END;
/

CREATE OR REPLACE FUNCTION fn_salas_disponibles RETURN NUMBER IS
    total_salas NUMBER;
BEGIN
    SELECT COUNT(*) INTO total_salas FROM salas WHERE estado_sala_id = (SELECT id FROM estados_salas WHERE estado = 'Disponible');
    RETURN total_salas;
END;
/

CREATE OR REPLACE FUNCTION fn_total_citas(cedula_empleado IN VARCHAR2) RETURN NUMBER IS
    total NUMBER;
BEGIN
    SELECT COUNT(*) INTO total FROM citas WHERE cedula_empleado = cedula_empleado;
    RETURN total;
END;
/

CREATE OR REPLACE FUNCTION fn_total_medicamentos RETURN NUMBER IS
    total NUMBER;
BEGIN
    SELECT COUNT(*) INTO total FROM medicamentos;
    RETURN total;
END;
/

CREATE OR REPLACE FUNCTION fn_disponibilidad_medicamento(id_medicamento IN NUMBER) RETURN NUMBER IS
    cantidad_disponible NUMBER;
BEGIN
    SELECT cantidad INTO cantidad_disponible FROM medicamentos WHERE id = id_medicamento;
    RETURN cantidad_disponible;
END;
/

CREATE OR REPLACE FUNCTION fn_costo_total_alquileres RETURN NUMBER IS
    total NUMBER;
BEGIN
    SELECT SUM(total) INTO total FROM alquileres;
    RETURN NVL(total, 0);
END;
/

CREATE OR REPLACE FUNCTION fn_verificar_paciente(cedula_paciente IN VARCHAR2) RETURN NUMBER IS
    existe NUMBER;
BEGIN
    SELECT COUNT(*) INTO existe FROM pacientes WHERE cedula = cedula_paciente;
    RETURN existe;
END;
/

---Paquete empleados---

CREATE OR REPLACE PACKAGE paquete_empleados AS
    PROCEDURE registrar_empleado(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_tipo_empleado_id IN NUMBER,
        p_estado IN VARCHAR2,
        p_salario_por_hora IN NUMBER
    );

    PROCEDURE actualizar_empleado(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_salario_por_hora IN NUMBER
    );

    PROCEDURE eliminar_empleado(
        p_cedula IN VARCHAR2
    );
END paquete_empleados;
/

CREATE OR REPLACE PACKAGE BODY paquete_empleados AS

    PROCEDURE registrar_empleado(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_tipo_empleado_id IN NUMBER,
        p_estado IN VARCHAR2 DEFAULT 'ACTIVO',
        p_salario_por_hora IN NUMBER
    ) AS
    BEGIN
        INSERT INTO empleados (cedula, nombre, apellidos, telefono, direccion, correo, tipo_empleado_id, estado, salario_por_hora)
        VALUES (p_cedula, p_nombre, p_apellidos, p_telefono, p_direccion, p_correo, p_tipo_empleado_id, p_estado, p_salario_por_hora);
    END registrar_empleado;

    PROCEDURE actualizar_empleado(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_salario_por_hora IN NUMBER
    ) AS
    BEGIN
        UPDATE empleados
        SET nombre = p_nombre, apellidos = p_apellidos, telefono = p_telefono, direccion = p_direccion, correo = p_correo, salario_por_hora = p_salario_por_hora
        WHERE cedula = p_cedula;
    END actualizar_empleado;

    PROCEDURE eliminar_empleado(p_cedula IN VARCHAR2) AS
    BEGIN
        DELETE FROM empleados
        WHERE cedula = p_cedula;
    END eliminar_empleado;

END paquete_empleados;
/


---Paquete de Pacientes
CREATE OR REPLACE PACKAGE paquete_pacientes AS
    PROCEDURE registrar_paciente(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_estado_paciente_id IN NUMBER,
        p_deuda IN NUMBER
    );

    PROCEDURE actualizar_paciente(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_estado_paciente_id IN NUMBER,
        p_deuda IN NUMBER
    );

    PROCEDURE eliminar_paciente(
        p_cedula IN VARCHAR2
    );
END paquete_pacientes;
/

CREATE OR REPLACE PACKAGE BODY paquete_pacientes AS

    PROCEDURE registrar_paciente(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_estado_paciente_id IN NUMBER,
        p_deuda IN NUMBER
    ) AS
    BEGIN
        INSERT INTO pacientes (cedula, nombre, apellidos, telefono, direccion, correo, estado_paciente_id, deuda)
        VALUES (p_cedula, p_nombre, p_apellidos, p_telefono, p_direccion, p_correo, p_estado_paciente_id, p_deuda);
    END registrar_paciente;

    PROCEDURE actualizar_paciente(
        p_cedula IN VARCHAR2,
        p_nombre IN VARCHAR2,
        p_apellidos IN VARCHAR2,
        p_telefono IN VARCHAR2,
        p_direccion IN VARCHAR2,
        p_correo IN VARCHAR2,
        p_estado_paciente_id IN NUMBER,
        p_deuda IN NUMBER
    ) AS
    BEGIN
        UPDATE pacientes
        SET nombre = p_nombre, apellidos = p_apellidos, telefono = p_telefono, direccion = p_direccion, correo = p_correo, estado_paciente_id = p_estado_paciente_id, deuda = p_deuda
        WHERE cedula = p_cedula;
    END actualizar_paciente;

    PROCEDURE eliminar_paciente(p_cedula IN VARCHAR2) AS
    BEGIN
        DELETE FROM pacientes
        WHERE cedula = p_cedula;
    END eliminar_paciente;

END paquete_pacientes;
/

--- Paquete Medicamentos

CREATE OR REPLACE PACKAGE paquete_medicamentos AS
    PROCEDURE registrar_medicamento(
        p_nombre IN VARCHAR2,
        p_precio IN NUMBER,
        p_cantidad IN NUMBER,
        p_informacion IN VARCHAR2
    );

    PROCEDURE actualizar_medicamento(
        p_id IN NUMBER,
        p_nombre IN VARCHAR2,
        p_precio IN NUMBER,
        p_cantidad IN NUMBER,
        p_informacion IN VARCHAR2
    );

    PROCEDURE eliminar_medicamento(
        p_id IN NUMBER
    );
END paquete_medicamentos;
/

CREATE OR REPLACE PACKAGE BODY paquete_medicamentos AS

    PROCEDURE registrar_medicamento(
        p_nombre IN VARCHAR2,
        p_precio IN NUMBER,
        p_cantidad IN NUMBER,
        p_informacion IN VARCHAR2
    ) AS
    BEGIN
        INSERT INTO medicamentos (nombre, precio, cantidad, informacion)
        VALUES (p_nombre, p_precio, p_cantidad, p_informacion);
    END registrar_medicamento;

    PROCEDURE actualizar_medicamento(
        p_id IN NUMBER,
        p_nombre IN VARCHAR2,
        p_precio IN NUMBER,
        p_cantidad IN NUMBER,
        p_informacion IN VARCHAR2
    ) AS
    BEGIN
        UPDATE medicamentos
        SET nombre = p_nombre, precio = p_precio, cantidad = p_cantidad, informacion = p_informacion
        WHERE id = p_id;
    END actualizar_medicamento;

    PROCEDURE eliminar_medicamento(p_id IN NUMBER) AS
    BEGIN
        DELETE FROM medicamentos
        WHERE id = p_id;
    END eliminar_medicamento;

END paquete_medicamentos;
/

---Paquete de Salas

CREATE OR REPLACE PACKAGE paquete_salas AS
    PROCEDURE registrar_sala(
        p_capacidad IN NUMBER,
        p_tipo_sala_id IN NUMBER,
        p_estado_sala_id IN NUMBER,
        p_precio_por_hora IN NUMBER
    );

    PROCEDURE actualizar_sala(
        p_id IN NUMBER,
        p_capacidad IN NUMBER,
        p_tipo_sala_id IN NUMBER,
        p_estado_sala_id IN NUMBER,
        p_precio_por_hora IN NUMBER
    );

    PROCEDURE eliminar_sala(
        p_id IN NUMBER
    );
END paquete_salas;
/




