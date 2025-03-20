package com.mycompany.pegasus.Fallas;

public enum Reporte {
    NadaGrave,
    Pocograve,
    Grave,
    MuyGrave,
    DemaciadoGrave;

    public static Reporte getNadaGrave() {
        return NadaGrave;
    }

    public static Reporte getPocograve() {
        return Pocograve;
    }

    public static Reporte getGrave() {
        return Grave;
    }

    public static Reporte getMuyGrave() {
        return MuyGrave;
    }

    public static Reporte getDemaciadoGrave() {
        return DemaciadoGrave;
    }

}
