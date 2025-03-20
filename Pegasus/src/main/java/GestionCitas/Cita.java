package GestionCitas;

import Conex_base_datos.Conexion;
import static GestionCitas.Asunto.CIRUJIA;
import java.sql.CallableStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import javax.swing.JOptionPane;

public class Cita {

    private int codigo;
    private String cedulaEmpleado;
    private String cedulaPaciente;
    private LocalDateTime fecha;
    private Asunto asunto;

    public Cita() {
    }

    public Cita(int codigo) {
        this.codigo = codigo;
    }

    public Cita(String cedulaEmpleado, String cedulaPaciente, 
            LocalDateTime fecha) {
        this.cedulaEmpleado = cedulaEmpleado;
        this.cedulaPaciente = cedulaPaciente;
        this.fecha = fecha;
    }

    public static ArrayList<Cita> consultarCitas() {
        ArrayList<Cita> listaCitas = new ArrayList<>();
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * "
                    + "FROM citas_pacientes_empleados");

            while (rs.next()) {
                Cita cita = new Cita();
                cita.setCodigo(rs.getInt(1));
                cita.setCedulaPaciente(rs.getString(2));
                cita.setCedulaEmpleado(rs.getString(3));
                cita.setFecha(rs.getTimestamp(4).toLocalDateTime());
                listaCitas.add(cita);
            }

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al consultar los registros",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }

        return listaCitas;
    }

    public boolean agregar() {

        Conexion conexion = new Conexion();

        String sql = "INSERT INTO citas_pacientes_empleados (cedulaPaciente, "
                + "cedulaEmpleado, fecha) VALUES (?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.cedulaPaciente);
            cs.setString(2, this.cedulaEmpleado);
            cs.setTimestamp(3, Timestamp.valueOf(fecha));

            cs.execute();
            conexion.desconectar();
            return true;

        } catch (SQLException ex) {
            conexion.desconectar();
            System.out.println(ex.toString());
            return false;

        }
    }

    public static int devolver_duracion_asunto(Asunto asunto) {
        switch (asunto) {
            case CIRUJIA:
                return 8;
            case CONSULTA:
                return 2;
            case CONTROL:
                return 2;
            case EXAMEN:
                return 1;
            case REHABILITACION:
                return 3;
            case VACUNACION:
                return 1;
            default:
                return 0;
        }
    }

    public LocalDateTime getFecha() {
        return fecha;
    }

    public void setFecha(LocalDateTime fecha) {
        this.fecha = fecha;
    }

    public int getCodigo() {
        return codigo;
    }

    public void setCodigo(int codigo) {
        this.codigo = codigo;
    }

    public String getCedulaEmpleado() {
        return cedulaEmpleado;
    }

    public void setCedulaEmpleado(String cedulaEmpleado) {
        this.cedulaEmpleado = cedulaEmpleado;
    }

    public String getCedulaPaciente() {
        return cedulaPaciente;
    }

    public void setCedulaPaciente(String cedulaPaciente) {
        this.cedulaPaciente = cedulaPaciente;
    }

    public Asunto getAsunto() {
        return asunto;
    }

    public void setAsunto(Asunto asunto) {
        this.asunto = asunto;
    }

}
