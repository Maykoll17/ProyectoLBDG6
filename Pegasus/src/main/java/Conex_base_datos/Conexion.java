package Conex_base_datos;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Conexion {
    private String url = "jdbc:mysql://localhost:3306/PegasusBD"; // URL 
    private String user = "root"; // Usuario 
    private String password = "SGHPegasus"; // Contraseña 
    private Connection connection;

    public Connection conectar() {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver"); 
            connection = DriverManager.getConnection(url, user, password); 
            System.out.println("Conexión exitosa a la base de datos.");
        } catch (ClassNotFoundException e) {
            System.out.println("Error al cargar el driver: " + e.getMessage());
        } catch (SQLException e) {
            System.out.println("Error de conexión: " + e.getMessage());
        }
        return connection;
    }

    public void desconectar() {
        if (connection != null) {
            try {
                connection.close(); 
                System.out.println("Conexión cerrada.");
            } catch (SQLException e) {
                System.out.println("Error al cerrar la conexión: " + 
                        e.getMessage());
            }
        }
    }
}
