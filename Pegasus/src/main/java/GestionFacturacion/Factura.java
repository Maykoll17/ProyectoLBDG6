package GestionFacturacion;

import Conex_base_datos.Conexion;
import java.sql.Connection;
import java.sql.Statement;
import java.sql.ResultSet;
import java.sql.CallableStatement;
import java.sql.SQLException;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

public class Factura {

    private int codigo;
    private Detalle detalle;
    private double monto;
    private String cedulaPaciente;
    private EstadoFactura estado;
    private int codigoMedReserva;

    public Factura() {
    }

    public Factura(int codigo) {
        this.codigo = codigo;
    }

    public Factura(int codigo, Detalle detalle, double monto, 
            String cedulaPaciente, EstadoFactura estado, int codigoMedReserva) {
        this.codigo = codigo;
        this.detalle = detalle;
        this.monto = monto;
        this.cedulaPaciente = cedulaPaciente;
        this.estado = estado;
        this.codigoMedReserva = codigoMedReserva;
    }

    public Factura(Detalle detalle, double monto, String cedulaPaciente, 
            EstadoFactura estado, int codigoMedReserva) {
        this.detalle = detalle;
        this.monto = monto;
        this.cedulaPaciente = cedulaPaciente;
        this.estado = estado;
        this.codigoMedReserva = codigoMedReserva;
    }

    public static DefaultTableModel consultar_tabla_facturas() {
        DefaultTableModel model = new DefaultTableModel();

        model.addColumn("Código");
        model.addColumn("Detalle");
        model.addColumn("Monto");
        model.addColumn("Cédula Paciente");
        model.addColumn("Estado");
        model.addColumn("Código Med Reserva");

        String[] datos = new String[6];
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM facturas");

            while (rs.next()) {
                datos[0] = String.valueOf(rs.getInt("codigo"));
                datos[1] = rs.getString("detalle");
                datos[2] = String.valueOf(rs.getDouble("monto"));
                datos[3] = rs.getString("cedulaPaciente");
                datos[4] = rs.getString("estado");
                datos[5] = String.valueOf(rs.getInt("codigoMedReserva"));

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

    public static ArrayList<Factura> consultarFacturas() {
        ArrayList<Factura> listaFacturas = new ArrayList<>();
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM facturas");

            while (rs.next()) {
                Factura factura = new Factura();
                factura.setCodigo(rs.getInt("codigo"));
                factura.setDetalle(Detalle.valueOf(rs.getString("detalle")));
                factura.setMonto(rs.getDouble("monto"));
                factura.setCedulaPaciente(rs.getString("cedulaPaciente"));
                factura.setEstado(EstadoFactura.valueOf(rs.getString("estado")));
                factura.setCodigoMedReserva(rs.getInt("codigoMedReserva"));
                listaFacturas.add(factura);
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

        return listaFacturas;
    }

    public void agregar() {
        Conexion conexion = new Conexion();

        String sql = "INSERT INTO facturas (detalle, monto, cedulaPaciente, "
                + "estado, codigoMedReserva) VALUES (?, ?, ?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.detalle.name());
            cs.setDouble(2, this.monto);
            cs.setString(3, this.cedulaPaciente);
            cs.setString(4, this.estado.name());
            cs.setInt(5, this.codigoMedReserva);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Factura agregada con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al agregar la factura",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public void modificar() {
        Conexion conexion = new Conexion();

        String sql = "UPDATE facturas SET detalle = ?, monto = ?, "
                + "cedulaPaciente = ?, estado = ?, codigoMedReserva = "
                + "? WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.detalle.name());
            cs.setDouble(2, this.monto);
            cs.setString(3, this.cedulaPaciente);
            cs.setString(4, this.estado.name());
            cs.setInt(5, this.codigoMedReserva);
            cs.setInt(6, this.codigo);

            cs.execute();

//            JOptionPane.showMessageDialog(null,
//                    "Factura modificada con éxito.",
//                    "Éxito",
//                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al modificar la factura",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public void borrar() {
        Conexion conexion = new Conexion();

        String sql = "DELETE FROM facturas WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);
            cs.setInt(1, this.codigo);
            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Factura eliminada con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al eliminar la factura",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public int getCodigoMedReserva() {
        return codigoMedReserva;
    }

    public void setCodigoMedReserva(int codigoMedReserva) {
        this.codigoMedReserva = codigoMedReserva;
    }

    public int getCodigo() {
        return codigo;
    }

    public void setCodigo(int codigo) {
        this.codigo = codigo;
    }

    public Detalle getDetalle() {
        return detalle;
    }

    public void setDetalle(Detalle detalle) {
        this.detalle = detalle;
    }

    public double getMonto() {
        return monto;
    }

    public void setMonto(double monto) {
        this.monto = monto;
    }

    public String getCedulaPaciente() {
        return cedulaPaciente;
    }

    public void setCedulaPaciente(String cedulaPaciente) {
        this.cedulaPaciente = cedulaPaciente;
    }

    public EstadoFactura getEstado() {
        return estado;
    }

    public void setEstado(EstadoFactura estado) {
        this.estado = estado;
    }

}
