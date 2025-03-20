
package com.mycompany.pegasus.Salas;

import java.time.LocalDateTime;


public class GestionFecha {
    public static LocalDateTime obtenerFechaActual(){
        return LocalDateTime.now().withSecond(0).withNano(0);
    }
    
    public static String obtenerFechaActualTxt(){
        return LocalDateTime.now().withSecond(0).withNano(0)
                .toString().substring(0, 16).replace("T", " ");
    }
    
}
