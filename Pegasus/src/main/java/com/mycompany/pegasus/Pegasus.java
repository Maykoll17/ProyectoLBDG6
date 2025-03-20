package com.mycompany.pegasus;

import ChatClienteServidor.ServerHandler;
import Inicio.InicioSesion;
import Inicio.Registro;
import Conex_base_datos.Conexion;
import GestionCitas.Cita;
import GestionCitas.Gestion;
import GestionCitas.GestionCitas;
import GestionPersonas.ModificarPacienteCompleto;
import java.io.IOException;
import java.net.ServerSocket;

public class Pegasus {

    public static void main(String[] args) {
        InicioSesion inicioSesion = new InicioSesion();
        inicioSesion.setVisible(true);
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
//        Registro registro = new Registro();
//        registro.setVisible(true);
//
//
//        Gestion g = new Gestion();
//        g.actualizar_fechas_ocupadas();
//        g.actualizar_fechas_disponibles();
//        g.mostrar();
//
//           GestionCitas gc = new GestionCitas();
//           gc.setVisible(true);
    }
}
