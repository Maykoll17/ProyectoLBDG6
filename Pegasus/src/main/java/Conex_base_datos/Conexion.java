package Conex_base_datos;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Conexion {
    private String url = "jdbc:oracle:thin:@localhost:1521:XE"; 
    private String user = "tu_usuario"; 
    private String password = "tu_contraseña"; 
    private Connection connection;

    public Connection conectar() {
        try {
            Class.forName("oracle.jdbc.driver.OracleDriver"); 
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
                System.out.println("Error al cerrar la conexión: " + e.getMessage());
            }
        }
    }
}
