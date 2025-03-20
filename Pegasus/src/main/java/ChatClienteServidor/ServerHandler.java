package ChatClienteServidor;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.Socket;
import java.util.HashSet;

public class ServerHandler extends Thread {

    private String cedula;
    private Socket socket;
    private DataInputStream in;
    private DataOutputStream out;
    private static HashSet<DataOutputStream> escritores = new HashSet();
    private static HashSet<String> cedulas = new HashSet();

    public ServerHandler(Socket socket) {
        this.socket = socket;
    }

    @Override
    public void run() {
        try {
            in = new DataInputStream(socket.getInputStream());
            out = new DataOutputStream(socket.getOutputStream());
            boolean valida = true;
            while (true) {

                out.writeUTF("SUBMITID");
                cedula = in.readUTF();
                if (!GestionCitas.Gestion.verificar_cedula(cedula, 2)) {
                    out.writeUTF("MESSAGE " + "Su cedula no es valida");
                    valida = false;
                }

                synchronized (cedulas) {
                    if (!cedulas.contains(cedula)) {
                        cedulas.add(cedula);
                        break;
                    } else {
                        out.writeUTF("REPEATEDID");
                    }
                }
            }

            synchronized (escritores) {
                out.writeUTF("IDACCEPTED");
                escritores.add(out);
            }


            while (true) {
                String mensaje = in.readUTF();
                String monto = GestionFacturacion.GestionCobros.
                        devolver_monto(cedula);

                if (mensaje.equals("Monto") && valida) {
                    out.writeUTF("MESSAGE " + "Usted debe:" + monto);
                }
                if (mensaje.equals("Pagar") && valida) {
                    out.writeUTF("MESSAGE " + "servidor" + ": " + 
                            "se completo el pago por: " + monto);
                    GestionFacturacion.GestionCobros.cobrar_facturas(cedula);
                    System.out.println(cedula + ": acaba de pagar" + monto);
                }
            }

        } catch (IOException ex) {
            System.out.println("Cliente: " + cedula + " cerrado");
        } finally {

            if (out != null) {
                escritores.remove(out);
            }

            if (cedula != null) {
                cedulas.remove(cedula);
            }

            try {
                socket.close();
            } catch (IOException e) {
                System.out.println("Error: " + e.toString());
            }
        }

    }

}
