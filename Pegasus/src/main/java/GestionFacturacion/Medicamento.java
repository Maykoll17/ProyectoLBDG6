package GestionFacturacion;

import Conex_base_datos.Conexion;
import java.sql.*;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

public class Medicamento {

    private int codigo;
    private String nombre;
    private int cantidad;
    private double precio;
    

    

    public Medicamento() {
    }

    public Medicamento(int codigo) {
        this.codigo = codigo;
    }

    public Medicamento(String nombre, double precio, int cantidad) {
        this.nombre = nombre;
        this.precio = precio;
        this.cantidad = cantidad;
    }

    public Medicamento(int codigo, String nombre, double precio, int cantidad) {
        this.codigo = codigo;
        this.nombre = nombre;
        this.precio = precio;
        this.cantidad = cantidad;
    }

    public static DefaultTableModel consultar_tabla_medicamentos() {
        DefaultTableModel model = new DefaultTableModel();

        // Agregar columnas al modelo de la tabla
        model.addColumn("Código");
        model.addColumn("Nombre");
        model.addColumn("Precio");
        model.addColumn("Cantidad");

        String[] datos = new String[4];
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM medicamentos");

            while (rs.next()) {
                datos[0] = String.valueOf(rs.getInt("codigo"));
                datos[1] = rs.getString("nombre");
                datos[2] = "₡"+String.valueOf(rs.getDouble("precio"));
                datos[3] = String.valueOf(rs.getInt("cantidad"));

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
    
    public static Medicamento retornar_medicamento(int codigo){
        ArrayList<Medicamento> listaMedicamentos = consultarMedicamentos();
        for (Medicamento medicamento : listaMedicamentos) {
            if(medicamento.getCodigo()==codigo){
                return medicamento;
            }
        }
        return null;
    }

    public static ArrayList<Medicamento> consultarMedicamentos() {
        ArrayList<Medicamento> listaMedicamentos = new ArrayList<>();
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM medicamentos");

            while (rs.next()) {
                Medicamento medicamento = new Medicamento();
                medicamento.setCodigo(rs.getInt("codigo"));
                medicamento.setNombre(rs.getString("nombre"));
                medicamento.setPrecio(rs.getDouble("precio"));
                medicamento.setCantidad(rs.getInt("cantidad"));
                listaMedicamentos.add(medicamento);
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

        return listaMedicamentos;
    }

    public void agregar() {
        Conexion conexion = new Conexion();

        String sql = "INSERT INTO medicamentos (nombre, precio, cantidad) "
                + "VALUES (?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.nombre);
            cs.setDouble(2, this.precio);
            cs.setInt(3, this.cantidad);

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

    public void modificar() {
        Conexion conexion = new Conexion();

        String sql = "UPDATE medicamentos SET nombre = ?, precio = ?, cantidad "
                + "= ? WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.nombre);
            cs.setDouble(2, this.precio);
            cs.setInt(3, this.cantidad);
            cs.setInt(4, this.codigo);

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

    public void borrar() {
        Conexion conexion = new Conexion();

        String sql = "DELETE FROM medicamentos WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);
            cs.setInt(1, this.codigo);
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

    public int getCodigo() {
        return codigo;
    }

    public void setCodigo(int codigo) {
        this.codigo = codigo;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public double getPrecio() {
        return precio;
    }

    public void setPrecio(double precio) {
        this.precio = precio;
    }

    public int getCantidad() {
        return cantidad;
    }

    public void setCantidad(int cantidad) {
        this.cantidad = cantidad;
    }

}
