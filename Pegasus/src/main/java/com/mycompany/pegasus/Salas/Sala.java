package com.mycompany.pegasus.Salas;

import java.sql.CallableStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;
import Conex_base_datos.Conexion;

public class Sala {

    private int codigo;
    private int capacidad;
    private TipoSala tipoSala;
    private Estado estado;
    private double precioPorHora;

    //CREAR
    public Sala(int capacidad, TipoSala tipoSala, Estado estado, 
            double precioPorHora) {
        this.capacidad = capacidad;
        this.tipoSala = tipoSala;
        this.precioPorHora = precioPorHora;
        this.estado = estado;
    }

    //Modificar
    public Sala(int codigo, int capacidad, TipoSala tipoSala, Estado estado, 
            double precioPorHora) {
        this.codigo = codigo;
        this.capacidad = capacidad;
        this.tipoSala = tipoSala;
        this.precioPorHora = precioPorHora;
        this.estado = estado;
    }

    //Eliminar
    public Sala(int codigo) {
        this.codigo = codigo;
    }

    //CARGAR
    public Sala(ResultSet rs) throws SQLException {
        this.codigo = rs.getInt("codigo");
        this.capacidad = rs.getInt("capacidad");
        this.tipoSala = TipoSala.valueOf(rs.getString("tipoSala"));
        this.estado = Estado.valueOf(rs.getString("estado"));
        this.precioPorHora = rs.getDouble("precioPorHora");
    }

    public Sala() {
        this.codigo = -1;
    }

    //Metodos Crud 
    //Read para consultar las tablas de las salas 
    public static DefaultTableModel consultar() {
        Conexion conexion = new Conexion();
        DefaultTableModel model = new DefaultTableModel();

        model.addColumn("Codigo");
        model.addColumn("Capacidad");
        model.addColumn("Tipo de sala");
        model.addColumn("Estado de sala");
        model.addColumn("Precio por hora");

        String datos[] = new String[5];

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM Salas");

            while (rs.next()) {
                datos[0] = String.valueOf(rs.getInt("codigo"));
                datos[1] = "Personas: " + String.valueOf(rs.getInt("capacidad"));
                datos[2] = rs.getString("tipoSala");
                datos[3] = rs.getString("estado");
                datos[4] = "₡" + rs.getDouble("precioPorHora");

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

    public static ArrayList<Sala> cargarSalas() {
        Conexion conexion = new Conexion();
        ArrayList<Sala> salas = new ArrayList();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM Salas");

            while (rs.next()) {
                salas.add(new Sala(rs));
            }

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al cargar los registros",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
        return salas;
    }

    // Metodo (CREATE) para guardar salas
    public void agregar() {
        Conexion conexion = new Conexion();

        String sql = "INSERT INTO Salas "
                + "(capacidad, tipoSala, estado, precioPorHora) VALUES "
                + "(?, ?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setInt(1, this.capacidad);
            cs.setString(2, this.tipoSala.toString());
            cs.setString(3, this.estado.toString());
            cs.setDouble(4, this.precioPorHora);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Registro agregado con exito.",
                    "Exito",
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

    // Metodo (UPDATE) para modificar las salas
    public void modificar() {
        Conexion conexion = new Conexion();

        String sql = "UPDATE Salas "
                + "SET capacidad = ?,"
                + "	 tipoSala = ?,"
                + "    estado = ?,"
                + "    precioPorHora = ? "
                + "WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setInt(1, this.capacidad);
            cs.setString(2, this.tipoSala.toString());
            cs.setString(3, this.estado.toString());
            cs.setDouble(4, this.precioPorHora);
            cs.setInt(5, this.codigo);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Registro modificado con exito.",
                    "Exito",
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

    // Metodo (DELETE) para eliminar salas
    public void eliminar() {
        Conexion conexion = new Conexion();

        String sql = "DELETE FROM Salas WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);
            cs.setInt(1, this.codigo);
            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Registro eliminado con exito.",
                    "Exito",
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
    
    public double guardarInfoPrecio(){
        return precioPorHora;
    }

    public int getCodigo() {
        return codigo;
    }

    public void setCodigo(int codigo) {
        this.codigo = codigo;
    }

    public int getCapacidad() {
        return capacidad;
    }

    public void setCapacidad(int capacidad) {
        this.capacidad = capacidad;
    }

    public Estado getEstado() {
        return estado;
    }

    public void setEstado(Estado estado) {
        this.estado = estado;
    }

    public double getPrecioPorHora() {
        return precioPorHora;
    }

    public void setPrecioPorHora(double precioPorHora) {
        this.precioPorHora = precioPorHora;
    }

    public TipoSala getTipoSala() {
        return tipoSala;
    }

    public void setTipoSala(TipoSala tipoSala) {
        this.tipoSala = tipoSala;
    }
    
    @Override
    public String toString(){
    if (codigo == -1) {
            return "No hay salas disponibles.";
        } else {
            return codigo + "- " + tipoSala + " [$" + precioPorHora + ']';
        }
    }

}
