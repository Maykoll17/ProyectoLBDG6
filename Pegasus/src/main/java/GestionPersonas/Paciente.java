package GestionPersonas;

import Conex_base_datos.Conexion;
import java.sql.CallableStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

public class Paciente extends Persona {

    private EstadoPaciente estado;
    private double deuda;

    public Paciente() {
    }

    public Paciente(String cedula) {
        super(cedula);
    }

    public Paciente(EstadoPaciente estado, double deuda, String cedula, 
            String nombre, String apellidos, String telefono, String direccion, 
            String correo) {
        super(cedula, nombre, apellidos,telefono, direccion, correo);
        this.estado = estado;
        this.deuda = deuda;
    }

    public static DefaultTableModel consultarPacientes_devolvertabla() {
        Conexion conexion = new Conexion();
        DefaultTableModel model = new DefaultTableModel();

        
        model.addColumn("Cédula");
        model.addColumn("Nombre");
        model.addColumn("Apellidos");
        model.addColumn("Teléfono");
        model.addColumn("Dirección");
        model.addColumn("Correo");
        model.addColumn("Estado");
        model.addColumn("Deuda");

        String datos[] = new String[8];

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM pacientes");

            while (rs.next()) {
                datos[0] = rs.getString("cedula");
                datos[1] = rs.getString("nombre");
                datos[2] = rs.getString("apellidos");
                datos[3] = rs.getString("telefono");
                datos[4] = rs.getString("direccion");
                datos[5] = rs.getString("correo");
                datos[6] = rs.getString("estado");
                datos[7] = String.valueOf(rs.getDouble("deuda"));

                model.addRow(datos);
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

        return model;
    }

    public static ArrayList<Paciente> consultarPacientes() {
        ArrayList<Paciente> listaPacientes = new ArrayList<>();
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM pacientes");

            while (rs.next()) {
                Paciente paciente = new Paciente();
                paciente.setCedula(rs.getString("cedula"));
                paciente.setNombre(rs.getString("nombre"));
                paciente.setApellidos(rs.getString("apellidos"));
                paciente.setTelefono(rs.getString("telefono"));
                paciente.setDireccion(rs.getString("direccion"));
                paciente.setCorreo(rs.getString("correo"));
                paciente.setEstado(EstadoPaciente.valueOf(rs.getString
        ("estado")));
                paciente.setDeuda(rs.getDouble("deuda"));
                listaPacientes.add(paciente);
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

        return listaPacientes;
    }

    @Override
    public void agregar() {

        Conexion conexion = new Conexion();

        String sql = "INSERT INTO pacientes (cedula, nombre, apellidos, "
                + "telefono, direccion, correo, estado, deuda) VALUES "
                + "(?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.cedula);
            cs.setString(2, this.nombre);
            cs.setString(3, this.apellidos);
            cs.setString(4, this.telefono);
            cs.setString(5, this.direccion);
            cs.setString(6, this.correo);
            cs.setString(7, this.estado.toString());
            cs.setDouble(8, this.deuda);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Registro agregado con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al agregar el registro",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    @Override
    public void modificar() {

        Conexion conexion = new Conexion();

        String sql = "UPDATE pacientes SET nombre = ?, apellidos = ?, "
                + "telefono = ?, direccion = ?, correo = ?, estado = ?, "
                + "deuda = ? WHERE cedula = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.nombre);
            cs.setString(2, this.apellidos);
            cs.setString(3, this.telefono);
            cs.setString(4, this.direccion);
            cs.setString(5, this.correo);
            cs.setString(6, this.estado.toString());
            cs.setDouble(7, this.deuda);
            cs.setString(8, this.cedula);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Registro modificado con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al modificar el registro",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    @Override
    public void borrar() {
        Conexion conexion = new Conexion();

        String sql = "DELETE FROM pacientes WHERE cedula = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);
            cs.setString(1, this.cedula);
            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Registro eliminado con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al eliminar el registro",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public EstadoPaciente getEstado() {
        return estado;
    }

    public void setEstado(EstadoPaciente estado) {
        this.estado = estado;
    }

    public double getDeuda() {
        return deuda;
    }

    public void setDeuda(double deuda) {
        this.deuda = deuda;
    }

}
