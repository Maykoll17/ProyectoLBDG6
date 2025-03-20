package Inicio;

import java.util.ArrayList;

public class GestionAutentificacion {

    private static ArrayList<Usuario> usuarios;
    private Usuario user;

    public static ArrayList<Usuario> getUsuarios() {
        return usuarios;
    }

    public static void setUsuarios(ArrayList<Usuario> aUsuarios) {
        usuarios = aUsuarios;
    }

    public GestionAutentificacion() {
        usuarios = new ArrayList();
        user = new Usuario();
    }

    public static boolean consultar() {
        usuarios.clear();
        usuarios = Usuario.consultarUsuarios();
        return !usuarios.isEmpty();
    }

    public void agregarUsuario() {

    }

    public boolean existe_usuario(String nbUsuario) {
        consultar();
        for (Usuario usuario : usuarios) {
            if (usuario.getNbUsuario().equals(nbUsuario)) {
                return true;
            }
        }
        return false;
    }

    public boolean autenticar_contra(String nbUsuario, String contra) {
        consultar();
        for (Usuario usuario : usuarios) {
            if (usuario.getNbUsuario().equals(nbUsuario) && 
                    usuario.getContra().equals(contra)) {
                return true;
            }
        }
        return false;
    }
    
    public String retornar_permisos(String nbUsuario){
        consultar();
        for (Usuario usuario : usuarios) {
            if (usuario.getNbUsuario().equals(nbUsuario)) {
                return usuario.getPermisos();
            }
        }
        return "";
    }

    public Usuario getUser() {
        return user;
    }

    public void setUser(Usuario user) {
        this.user = user;
    }

}
