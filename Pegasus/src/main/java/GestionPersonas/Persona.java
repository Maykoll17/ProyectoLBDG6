package GestionPersonas;

import javax.swing.JOptionPane;

public abstract class Persona {

    protected String cedula;
    protected String nombre;
    protected String apellidos;
    protected String telefono;
    protected String direccion;
    protected String correo;

    public Persona(String cedula, String nombre, String apellidos, 
            String telefono, String direccion, String correo) {
        this.cedula = formatear_cedula(cedula);
        this.nombre = nombre;
        this.apellidos = apellidos;
        this.telefono = formatear_telefono(telefono);
        this.direccion = direccion;
        this.correo = correo;
    }

    public Persona(String cedula) {
        this.cedula = cedula;
    }

    public Persona() {
    }

    public static String formatear_cedula(String cedula) {
        if (verificar_cedula(cedula)) {
            String cedula_formateada = "";
            for (char caracter : cedula.toCharArray()) {
                if (Character.isDigit(caracter)) {
                    cedula_formateada += caracter;
                }
            }
            return cedula_formateada;
        } else {
            JOptionPane.showMessageDialog(null,
                    "La cedula no tiene el formato correcto",
                    "Advertencia",
                    JOptionPane.WARNING_MESSAGE);
            return "";
        }

    }

    public static String formatear_telefono(String telefono) {
        //8888 8888
        //8888-8888
        //88888888
        if (verificar_telefono(telefono)) {
            if (telefono.length() == 8) {
                return telefono.substring(0, 4) + "-" + telefono.substring(4);
            } else {
                return telefono.substring(0, 4) + "-" + telefono.substring(5);
            }
        } else {
            JOptionPane.showMessageDialog(null,
                    "El telefono no tiene el formato correcto: XXXX:XXXX, "
                            + "XXXXXXXX, XXXX-XXXX, XXXX XXXX",
                    "Advertencia",
                    JOptionPane.WARNING_MESSAGE);
            return "";
        }

    }

    public static boolean verificar_cedula(String cedula) {
        //504 34 0651
        String cedula_formateada = "";
        for (char caracter : cedula.toCharArray()) {
            if (Character.isDigit(caracter)) {
                cedula_formateada += caracter;
            }
        }
        return esNumero(cedula_formateada) && cedula_formateada.length() == 9;
    }

    public static boolean verificar_telefono(String telefono) {
        //8888 8888
        //8888-8888
        //88888888
        if (telefono.length() == 8) {
            return esNumero(telefono);
        } else if (telefono.length() > 8) {
            telefono = telefono.substring(0, 4) + telefono.substring(5);
            if (telefono.length() > 8) {
                return false;
            } else {
                return esNumero(telefono);
            }
        } else {
            return false;
        }

    }

    private static boolean esNumero(String palabra) {
        for (char c : palabra.toCharArray()) {
            if (!Character.isDigit(c)) {
                return false;
            }
        }
        return true;
    }

    public abstract void agregar();

    public abstract void modificar();

    public abstract void borrar();

    public String getCedula() {
        return cedula;
    }

    public void setCedula(String cedula) {
        this.cedula = cedula;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getApellidos() {
        return apellidos;
    }

    public void setApellidos(String apellidos) {
        this.apellidos = apellidos;
    }

    public String getTelefono() {
        return telefono;
    }

    public void setTelefono(String telefono) {
        this.telefono = telefono;
    }

    public String getDireccion() {
        return direccion;
    }

    public void setDireccion(String direccion) {
        this.direccion = direccion;
    }

    public String getCorreo() {
        return correo;
    }

    public void setCorreo(String correo) {
        this.correo = correo;
    }

}
