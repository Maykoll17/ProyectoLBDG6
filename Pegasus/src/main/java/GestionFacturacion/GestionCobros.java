package GestionFacturacion;

import Conex_base_datos.Conexion;
import GestionPersonas.Paciente;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

public class GestionCobros {

    public static String devolver_monto(String cedula) {
        double monto = 0;
        ArrayList<Factura> listaFacturas = Factura.consultarFacturas();
        for (Factura factura : listaFacturas) {
            if (factura.getCedulaPaciente().equals(cedula) && factura.getEstado
        () == EstadoFactura.PENDIENTE) {
                
                monto += factura.getMonto();
            }
        }
        return monto + "";
    }

    public static boolean cobrar_facturas(String cedula) {
        boolean cobradas = false;
        ArrayList<Factura> listaFacturas = Factura.consultarFacturas();
        for (Factura factura : listaFacturas) {
            if (factura.getCedulaPaciente().equals(cedula)) {
                factura.setEstado(EstadoFactura.COBRADO);
                factura.modificar();
                cobradas = true;
            }
        }
        return cobradas;
    }

    public static DefaultTableModel cargar_facturas(String cedula) {

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
                if (datos[3].equals(cedula)) {
                    model.addRow(datos);
                }
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

}
