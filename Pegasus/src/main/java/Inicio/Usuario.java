package Inicio;

import Conex_base_datos.Conexion;
import java.sql.CallableStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import javax.swing.JOptionPane;

public class Usuario {

    private String nbUsuario;
    private String contra;
    private String permisos;

    public Usuario(String nbUsuario, String contra, String permisos) {
        this.nbUsuario = nbUsuario;
        this.contra = contra;
        this.permisos = permisos;
    }

    public Usuario(String nbUsuario, String contra) {
        this.nbUsuario = nbUsuario;
        this.contra = contra;
        permisos = "";
    }

    public Usuario(String nbUsuario) {
        this.nbUsuario = nbUsuario;
    }

    public Usuario() {
    }

    public static ArrayList<Usuario> consultarUsuarios() {
        ArrayList<Usuario> listaUsuarios = new ArrayList<>();
        Conexion conexion = new Conexion();

        try {
            Statement stmt = conexion.conectar().createStatement();
            ResultSet rs = stmt.executeQuery("SELECT * FROM usuarios");

            while (rs.next()) {
                Usuario usuario = new Usuario();
                usuario.setNbUsuario(rs.getString("nbusuario"));
                usuario.setContra(rs.getString("contra"));
                usuario.setPermisos(rs.getString("permisos"));
                listaUsuarios.add(usuario);
            }

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al consultar los usuarios",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }

        return listaUsuarios;
    }

    public void agregarUsuario() {
        Conexion conexion = new Conexion();

        String sql = "INSERT INTO usuarios (nbusuario, contra, permisos) "
                + "VALUES (?, ?, ?)";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.nbUsuario);
            cs.setString(2, this.contra);
            cs.setString(3, this.permisos);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Usuario agregado con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al agregar el usuario",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public void modificarUsuario() {
        Conexion conexion = new Conexion();

        String sql = "UPDATE usuarios SET contra = ?, permisos = ? "
                + "WHERE nbusuario = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);

            cs.setString(1, this.contra);
            cs.setString(2, this.permisos);
            cs.setString(3, this.nbUsuario);

            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Usuario modificado con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al modificar el Usuario",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public void eliminarUsuario() {
        Conexion conexion = new Conexion();

        String sql = "DELETE FROM usuarios WHERE nbusuario = ?";

        try {
            CallableStatement cs = conexion.conectar().prepareCall(sql);
            cs.setString(1, this.nbUsuario);
            cs.execute();

            JOptionPane.showMessageDialog(null,
                    "Usuario eliminado con éxito.",
                    "Éxito",
                    JOptionPane.INFORMATION_MESSAGE);

        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(null,
                    "Error al eliminar el Usuario",
                    "Error",
                    JOptionPane.ERROR_MESSAGE);
            System.out.println("Error: " + ex.getMessage());
        } finally {
            conexion.desconectar();
        }
    }

    public String getNbUsuario() {
        return nbUsuario;
    }

    public void setNbUsuario(String nbUsuario) {
        this.nbUsuario = nbUsuario;
    }

    public String getContra() {
        return contra;
    }

    public void setContra(String contra) {
        this.contra = contra;
    }

    public String getPermisos() {
        return permisos;
    }

    public void setPermisos(String permisos) {
        this.permisos = permisos;
    }

}
