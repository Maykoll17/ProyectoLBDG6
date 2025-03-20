
package com.mycompany.pegasus.Objeto;


public enum Comida {
    DietasBlandas,
    DietasLíquidas,
    DietasDiabéticos,
    BajasSodio,
    AltasProteínas,
    BajasGrasa,
    SinGluten;
    
    public static Comida getDietasBlandas() {
        return DietasBlandas;
    }

    public static Comida getDietasLíquidas() {
        return DietasLíquidas;
    }

    public static Comida getDietasDiabéticos() {
        return DietasDiabéticos;
    }

    public static Comida getBajasSodio() {
        return BajasSodio;
    }

    public static Comida getAltasProteínas() {
        return AltasProteínas;
    }

    public static Comida getBajasGrasa() {
        return BajasGrasa;
    }

    public static Comida getSinGluten() {
        return SinGluten;
    }
}
