package ChatClienteServidor;

import java.io.IOException;
import java.net.ServerSocket;

public class Servidor {
    
    public static void main(String[] args) {
        try {
            System.out.println("El servidor de chat esta corriendo...");
            ServerSocket server = new ServerSocket(9090);
            
            while (true) {                
                ServerHandler sh = new ServerHandler(server.accept());
                sh.start();
            }
            
        } catch (IOException ex) {
            System.out.println("Error: " + ex.toString());
        }
    }

}
