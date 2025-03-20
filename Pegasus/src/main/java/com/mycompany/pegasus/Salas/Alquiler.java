package com.mycompany.pegasus.Salas;

import java.sql.CallableStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.time.LocalDateTime;
import java.util.ArrayList;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;
import Conex_base_datos.Conexion;

public class Alquiler {

    private int codigo;
    private int cod_sala;
    private String doctor;
    private LocalDateTime fechaInicio;
    private LocalDateTime fechaFin;
    private double total;

    
    public Alquiler(int cod_sala, String doctor) {
        this.cod_sala = cod_sala;
        this.doctor = doctor;
        this.fechaInicio = GestionFecha.obtenerFechaActual();
    }

    
    public Alquiler(int codigo, double total) {
        this.codigo = codigo;
        this.fechaFin = GestionFecha.obtenerFechaActual();
        this.total = total;
    }

    
    public Alquiler(int codigo) {
        this.codigo = codigo;
    }

    public static String obtenerTipoSala(ArrayList<Sala> sala, int codigo) {
        for (Sala sala1 : sala) {
            if (sala1.getCodigo() == codigo) {
                return sala1.getTipoSala().toString();
            }
        }
        return null;
    }

    //Metodos de gestion 
    public static DefaultTableModel consultar(ArrayList<Sala> salas) {
        Conexion conexion = new Conexion();
        DefaultTableModel model = new DefaultTableModel();

        model.addColumn("Codigo");
        model.addColumn("Tipo de sala");
        model.addColumn("Doctor");
        model.addColumn("Fecha de Inicio");
        model.addColumn("Fecha de Fin");
        model.addColumn("Total");

        String datos[] = new String[6];

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM Alquileres");

            while (rs.next()) {
                datos[0] = String.valueOf(rs.getInt("codigo"));
                datos[1] = obtenerTipoSala(salas, rs.getInt("sala_codigo"));
                datos[2] = rs.getString("doctor");
                datos[3] = rs.getTimestamp("fechaInicio").toLocalDateTime().
                        toString().replace("T", " ");

                if (rs.getTimestamp("fechaFin") == null) {
                    datos[4] = "N/A";
                } else {
                    datos[4] = rs.getTimestamp("fechaFin").toLocalDateTime().
                            toString()
                            .replace("T", " ");
                }

                if (rs.getDouble("total") == 0.0) {
                    datos[5] = "N/A";
                } else {
                    datos[5] = "₡" + rs.getDouble("total");
                }

                model.addRow(datos);
            }

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al consultar las salas",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }

        return model;
    }

    public void alquilar() {
        Conexion conexion = new Conexion();

        String sql = "INSERT INTO Alquileres "
                + "(sala_codigo, doctor, fechaInicio) VALUES (?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setInt(1, this.cod_sala);
            cs.setString(2, this.doctor);
            cs.setTimestamp(3, Timestamp.valueOf(this.fechaInicio));

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "reservacion realizada con exito.",
                    "Exito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al reservar",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }
    
     public void cobrar() {
        Conexion conexion = new Conexion();

        String sql = "UPDATE Alquileres "
                + "SET fechaFin = ?, total = ? WHERE codigo = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setTimestamp(1, Timestamp.valueOf(this.fechaFin));
            cs.setDouble(2, this.total);
            cs.setInt(3, this.codigo);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Cobro realizado con exito.",
                    "Exito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al cobrar",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public void eliminar() {
        Conexion conexion = new Conexion();

        String sql = "DELETE FROM Alquileres WHERE codigo = ?";
        
        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);
            
            cs.setInt(1, this.codigo);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Alquiler eliminado exito.",
                    "Exito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al alquilar",
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

    public int getCod_sala() {
        return cod_sala;
    }

    public void setCod_sala(int cod_sala) {
        this.cod_sala = cod_sala;
    }

    public String getDoctor() {
        return doctor;
    }

    public void setDoctor(String doctor) {
        this.doctor = doctor;
    }

    public LocalDateTime getFechaInicio() {
        return fechaInicio;
    }

    public void setFechaInicio(LocalDateTime fechaInicio) {
        this.fechaInicio = fechaInicio;
    }

    public LocalDateTime getFechaFin() {
        return fechaFin;
    }

    public void setFechaFin(LocalDateTime fechaFin) {
        this.fechaFin = fechaFin;
    }

    public double getTotal() {
        return total;
    }

    public void setTotal(double total) {
        this.total = total;
    }

}
