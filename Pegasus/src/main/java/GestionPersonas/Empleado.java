package GestionPersonas;

import Conex_base_datos.Conexion;
import java.sql.CallableStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

public class Empleado extends Persona {

    private Tipo_empleado tipo_empleado;
    private double salarioPorHora;
    private EstadoEmpleado estado;

    public Empleado() {
    }

    public Empleado(String cedula) {
        super(Empleado.formatear_cedula(cedula));
    }

    public Empleado(Tipo_empleado tipo_empleado, double salarioPorHora, 
            EstadoEmpleado estado, String cedula, String nombre, 
            String apellidos, String telefono, String direccion, String correo){
        super(cedula, nombre, apellidos, telefono, direccion, correo);
        this.tipo_empleado = tipo_empleado;
        this.salarioPorHora = salarioPorHora;
        this.estado = estado;
    }

    public static DefaultTableModel consultar_tabla_empleados() {
        DefaultTableModel model = new DefaultTableModel();

        model.addColumn("Cedula");
        model.addColumn("Nombre");
        model.addColumn("Apellidos");
        model.addColumn("Telefono");
        model.addColumn("Direccion");
        model.addColumn("Correo");
        model.addColumn("Tipo Empleado");
        model.addColumn("Estado");
        model.addColumn("Salario por Hora");

        String[] datos = new String[9];
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM empleados");

            while (rs.next()) {
                datos[0] = rs.getString("cedula");
                datos[1] = rs.getString("nombre");
                datos[2] = rs.getString("apellidos");
                datos[3] = rs.getString("telefono");
                datos[4] = rs.getString("direccion");
                datos[5] = rs.getString("correo");
                datos[6] = rs.getString("tipoEmpleado");
                datos[7] = rs.getString("estado");
                datos[8] = String.valueOf(rs.getDouble("salarioPorHora"));

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

    public static ArrayList<Empleado> consultarEmpleados() {
        ArrayList<Empleado> listaEmpleados = new ArrayList<>();
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM empleados");

            while (rs.next()) {
                Empleado empleado = new Empleado();
                empleado.setCedula(rs.getString("cedula"));
                empleado.setNombre(rs.getString("nombre"));
                empleado.setApellidos(rs.getString("apellidos"));
                empleado.setTelefono(rs.getString("telefono"));
                empleado.setDireccion(rs.getString("direccion"));
                empleado.setCorreo(rs.getString("correo"));
                empleado.setTipo_empleado(Tipo_empleado.valueOf(rs.getString
        ("tipoEmpleado")));
                empleado.setEstado(EstadoEmpleado.valueOf(rs.getString
        ("estado")));
                empleado.setSalarioPorHora(rs.getDouble("salarioPorHora"));
                listaEmpleados.add(empleado);
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

        return listaEmpleados;
    }

    @Override
    public void agregar() {

        Conexion conexion = new Conexion();

        String sql = "INSERT INTO empleados "
                + "(cedula, nombre, apellidos, telefono, direccion, "
                + "correo, tipoEmpleado, estado, salarioPorHora) VALUES "
                + "(?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.cedula);
            cs.setString(2, this.nombre);
            cs.setString(3, this.apellidos);
            cs.setString(4, this.telefono);
            cs.setString(5, this.direccion);
            cs.setString(6, this.correo);
            cs.setString(7, this.tipo_empleado.toString());
            cs.setString(8, this.estado.toString());
            cs.setDouble(9, this.salarioPorHora);

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

        String sql = "UPDATE empleados SET nombre = ?, apellidos = ?, "
                + "telefono = ?, direccion = ?, correo = ?, tipoEmpleado = ?, "
                + "estado = ?, salarioPorHora = ? WHERE cedula = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.nombre);
            cs.setString(2, this.apellidos);
            cs.setString(3, this.telefono);
            cs.setString(4, this.direccion);
            cs.setString(5, this.correo);
            cs.setString(6, this.tipo_empleado.toString());
            cs.setString(7, this.estado.toString());
            cs.setDouble(8, this.salarioPorHora);
            
            cs.setString(9, this.cedula);

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
            System.out.println("Error: sql " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    @Override
    public void borrar() {

        Conexion conexion = new Conexion();

        String sql = "DELETE FROM empleados WHERE cedula = ?";

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

    public Tipo_empleado getTipo_empleado() {
        return tipo_empleado;
    }

    public void setTipo_empleado(Tipo_empleado tipo_empleado) {
        this.tipo_empleado = tipo_empleado;
    }

    public double getSalarioPorHora() {
        return salarioPorHora;
    }

    public void setSalarioPorHora(double salarioPorHora) {
        this.salarioPorHora = salarioPorHora;
    }

    public EstadoEmpleado getEstado() {
        return estado;
    }

    public void setEstado(EstadoEmpleado estado) {
        this.estado = estado;
    }

}
