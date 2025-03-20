package GestionFacturacion;

import Conex_base_datos.Conexion;
import java.sql.*;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

public class MedsReservadosPacientes {

    private int codigo;
    private String cedulaPaciente;
    private int codigoMed;
    private int cantidad;

    public MedsReservadosPacientes() {

    }

    //editar
    public MedsReservadosPacientes(int codigo, String cedulaPaciente, 
            int codigoMed, int cantidad) {
        this.codigo = codigo;
        this.cedulaPaciente = cedulaPaciente;
        this.codigoMed = codigoMed;
        this.cantidad = cantidad;
    }

    //borrar
    public MedsReservadosPacientes(int codigo) {
        this.codigo = codigo;
    }

    //agregar
    public MedsReservadosPacientes(String cedulaPaciente, int codigoMed, 
            int cantidad) {
        this.cedulaPaciente = cedulaPaciente;
        this.codigoMed = codigoMed;
        this.cantidad = cantidad;
    }

    public static boolean verificar_cod_reserva_med(int cod) {
        ArrayList<MedsReservadosPacientes> listaReservas = 
                consultarMedsReservadosPacientes();
        for (MedsReservadosPacientes listaReserva : listaReservas) {
            if (listaReserva.getCodigo() == cod) {
                return true;
            }
        }
        return false;
    }

    public static int devolver_codigo_medicamento
        (int codigo_reserva_medicamento) {
        ArrayList<MedsReservadosPacientes> listaReservas = 
                consultarMedsReservadosPacientes();
        for (MedsReservadosPacientes reserva : listaReservas) {
            if (reserva.getCodigo() == codigo_reserva_medicamento) {
                return reserva.getCodigoMed();
            }

        }
        return 0;
    }
    
    public static int devolver_cantidad_medicamento
        (int codigo_reserva_medicamento) {
        ArrayList<MedsReservadosPacientes> listaReservas = 
                consultarMedsReservadosPacientes();
        for (MedsReservadosPacientes reserva : listaReservas) {
            if (reserva.getCodigo() == codigo_reserva_medicamento) {
                return reserva.getCantidad();
            }

        }
        return 0;
    }


    public static double devolver_monto_meds(int codigo_reserva_medicamento) {
        ArrayList<Medicamento> listaMedicamentos = 
                Medicamento.consultarMedicamentos();
        int codigo_medicamento = devolver_codigo_medicamento
        (codigo_reserva_medicamento);
        int cantidad = devolver_cantidad_medicamento(codigo_reserva_medicamento);
        if (codigo_medicamento == 1) {
            JOptionPane.showMessageDialog(null, "No hay medicamentos con ese "
                    + "codigo, el codigo 1 se reserva para no medicamentos");
        } else {
            for (Medicamento medicamento : listaMedicamentos) {
                if (medicamento.getCodigo() == codigo_medicamento) {
                    return medicamento.getPrecio() * cantidad;
                }
            }
        }
        return 0;

    }
    


    public static DefaultTableModel consultar_tabla_medsReservadosPacientes() {
        DefaultTableModel model = new DefaultTableModel();

        // Definir las columnas para el modelo
        model.addColumn("Código");
        model.addColumn("Cédula Paciente");
        model.addColumn("Código Medicamento");
        model.addColumn("Cantidad");

        String[] datos = new String[4];
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * "
                    + "FROM medsReservadosPacientes");

            while (rs.next()) {
                datos[0] = String.valueOf(rs.getInt("codigo"));
                datos[1] = rs.getString("cedulaPaciente");
                datos[2] = String.valueOf(rs.getInt("codigoMed"));
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

    public static ArrayList<MedsReservadosPacientes> 
        consultarMedsReservadosPacientes() {
        ArrayList<MedsReservadosPacientes> listaReservas = new ArrayList<>();
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * "
                    + "FROM medsReservadosPacientes");

            while (rs.next()) {
                MedsReservadosPacientes reserva = new MedsReservadosPacientes();
                reserva.setCodigo(rs.getInt("codigo"));
                reserva.setCedulaPaciente(rs.getString("cedulaPaciente"));
                reserva.setCodigoMed(rs.getInt("codigoMed"));
                reserva.setCantidad(rs.getInt("cantidad"));
                listaReservas.add(reserva);
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

        return listaReservas;
    }

    public void agregar() {
        Conexion conexion = new Conexion();

        String sql = "INSERT INTO medsReservadosPacientes (cedulaPaciente, "
                + "codigoMed, cantidad) VALUES (?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.cedulaPaciente);
            cs.setInt(2, this.codigoMed);
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

        String sql = "UPDATE medsReservadosPacientes SET cedulaPaciente = ?, "
                + "codigoMed = ?, cantidad = ? WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.cedulaPaciente);
            cs.setInt(2, this.codigoMed);
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

        String sql = "DELETE FROM medsReservadosPacientes WHERE codigo = ?";

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

    // Getters y Setters
    public int getCodigo() {
        return codigo;
    }

    public void setCodigo(int codigo) {
        this.codigo = codigo;
    }

    public String getCedulaPaciente() {
        return cedulaPaciente;
    }

    public void setCedulaPaciente(String cedulaPaciente) {
        this.cedulaPaciente = cedulaPaciente;
    }

    public int getCodigoMed() {
        return codigoMed;
    }

    public void setCodigoMed(int codigoMed) {
        this.codigoMed = codigoMed;
    }

    public int getCantidad() {
        return cantidad;
    }

    public void setCantidad(int cantidad) {
        this.cantidad = cantidad;
    }

}
