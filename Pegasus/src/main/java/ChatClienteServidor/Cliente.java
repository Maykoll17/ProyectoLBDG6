package ChatClienteServidor;

import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.Socket;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import javax.swing.JTextField;

public class Cliente {

    private DataInputStream in;
    private DataOutputStream out;
    private JFrame frame = new JFrame("Pagos");
    private JLabel lbNombre = new JLabel();
    private JTextField txtCampoMensaje = new JTextField(20);
    private JTextArea txaMensajes = new JTextArea(8, 40);
    private JButton btnPagar = new JButton("PAGAR");
    private JButton btnMonto = new JButton("MONTO");

    public Cliente() {
        txtCampoMensaje.setEditable(false);
        txaMensajes.setEditable(false);

        JPanel panel = new JPanel();

        panel.add(lbNombre, "East");
        panel.add(txtCampoMensaje, "Center");
        panel.add(btnPagar);
        panel.add(btnMonto);

        frame.getContentPane().add(panel, "North");
        frame.getContentPane().add(new JScrollPane(txaMensajes), "Center");
        frame.pack();

        btnPagar.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                try {
                    out.writeUTF("Pagar");
                } catch (IOException ex) {
                    System.out.println("Error: " + ex.toString());
                }
            }
        });
        
        btnMonto.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                try {
                    out.writeUTF("Monto");
                } catch (IOException ex) {
                    System.out.println("Error: " + ex.toString());
                }
            }
        });

        txtCampoMensaje.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                try {
                    out.writeUTF(txtCampoMensaje.getText());
                    txtCampoMensaje.setText("");
                } catch (IOException ex) {
                    System.out.println("Error: " + ex.toString());
                }
            }
        });

    }

    public void conectarServidor() {
        try {
            Socket socket = new Socket("localhost", 9090); // 172.16.4.27
            in = new DataInputStream(socket.getInputStream());
            out = new DataOutputStream(socket.getOutputStream());
            String cedula = "";

            while (true) {
                String mensaje = in.readUTF();

                if (mensaje.startsWith("SUBMITID")) {
                    cedula = JOptionPane.showInputDialog(frame, 
                            "Ingrese su cedula");
                    out.writeUTF(cedula);
                } else if (mensaje.startsWith("REPEATEDID")) {
                    JOptionPane.showMessageDialog(frame,
                            "La cedula: " + cedula + " ya esta en el sistema.");
                } else if (mensaje.startsWith("IDACCEPTED")) {
                    txtCampoMensaje.setEditable(true);
                    lbNombre.setText("Cedula: " + cedula);
                } else if (mensaje.startsWith("MESSAGE")) {
                    txaMensajes.append(mensaje.substring(8) + "\n");
                }

            }

        } catch (IOException ex) {
            System.out.println("Error: " + ex.toString());
        }
    }

    public static void main(String[] args) {
        Cliente chatClient = new Cliente();
        chatClient.frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        chatClient.frame.setSize(600, 600);
        chatClient.frame.setLocationRelativeTo(null);
        chatClient.frame.setVisible(true);
        chatClient.conectarServidor();
    }
}
