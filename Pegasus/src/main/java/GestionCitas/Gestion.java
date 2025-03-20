package GestionCitas;

import GestionPersonas.Empleado;
import GestionPersonas.Paciente;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;

public class Gestion {

    ArrayList<Cita> citas;
    ArrayList<LocalDateTime> fechas_disponibles;
    ArrayList<String> fechas_ocupadas;

    public Gestion() {
        citas = Cita.consultarCitas();
        fechas_disponibles = new ArrayList();
        fechas_ocupadas = new ArrayList();
    }

    public void actualizar_fechas_ocupadas(String cedula_empleado, 
            String cedula_paciente) {
        citas = Cita.consultarCitas();
        fechas_ocupadas.clear();
        for (Cita cita : citas) {
            if (cita.getCedulaEmpleado().equals(cedula_empleado) && 
                    cita.getCedulaPaciente().equals(cedula_paciente)) {
                fechas_ocupadas.add(cita.getFecha().toString().replace
        ("T", " "));
            }
        }
    }

    public void actualizar_fechas_disponibles(String cedula_empleado, 
            String cedula_paciente) {
        actualizar_fechas_ocupadas(cedula_empleado, cedula_paciente);
        LocalDateTime fecha_hoy_completa = LocalDateTime.now();

        // Se redondea por bloques de 15
        int minutosActuales = fecha_hoy_completa.getMinute();
        int minutosFaltantes = (15 - (minutosActuales % 15)) % 15;
        fecha_hoy_completa = fecha_hoy_completa.plusMinutes(minutosFaltantes);

        // El sistema solo aceptara reservas de una semana en adelante
        //para no sobrecargar la maquina el dia del examen
        int minutosTotales = 7 * 24 * 60;
        fechas_disponibles.clear();
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern
        ("yyyy-MM-dd HH:mm");
        for (int min = 0; min < minutosTotales; min += 15) {
            LocalDateTime fecha_actual = fecha_hoy_completa.plusMinutes(min);
            if (!fechas_ocupadas.contains(fecha_actual.format(formatter))) {
                fechas_disponibles.add(fecha_actual);
            }
        }
    }

    //1 si es empleado y 2 si es paciente
    public static boolean verificar_cedula(String cedula, int tipo) {
        cedula = Empleado.formatear_cedula(cedula);
        if (!cedula.equals("")) {
            if (tipo == 1) {

                ArrayList<Empleado> empleados = Empleado.consultarEmpleados();
                for (Empleado empleado : empleados) {
                    if (cedula.equals(empleado.getCedula())) {
                        return true;
                    }
                }
            }
            if (tipo == 2) {

                ArrayList<Paciente> pacientes = Paciente.consultarPacientes();
                for (Paciente paciente : pacientes) {
                    if (cedula.equals(paciente.getCedula())) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public void mostrar() {
        for (LocalDateTime fecha : fechas_disponibles) {
            System.out.println(fecha);
        }
        for (String fecha : fechas_ocupadas) {
            System.out.println(fecha);
        }
    }

}
