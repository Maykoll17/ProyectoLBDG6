package com.mycompany.pegasus.Salas;

import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.time.Duration;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import javax.swing.JFrame;
import javax.swing.JOptionPane;
import javax.swing.table.DefaultTableModel;

public class GestionReservas extends javax.swing.JFrame {

    public GestionReservas(JFrame principal) {
        initComponents();

        addWindowListener(new WindowAdapter() {
            @Override
            public void windowClosing(WindowEvent e) {
                principal.setVisible(true);
                dispose();
            }
        });

        txtCodigo.setEnabled(false);
        txtFechaInicio.setEnabled(false);
        txtFechaFin.setEnabled(false);
        txtTotal.setEnabled(false);

        btnCobrar.setEnabled(false);
        btnEliminar.setEnabled(false);

        limpiar();
        mostrarAlquileres();
    }

    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        jPanel1 = new javax.swing.JPanel();
        jLabel1 = new javax.swing.JLabel();
        txtCodigo = new javax.swing.JTextField();
        jLabel2 = new javax.swing.JLabel();
        txtDoctor = new javax.swing.JTextField();
        jLabel3 = new javax.swing.JLabel();
        txtFechaInicio = new javax.swing.JTextField();
        jLabel4 = new javax.swing.JLabel();
        jLabel5 = new javax.swing.JLabel();
        cboxSala = new javax.swing.JComboBox();
        btnAlquilar = new javax.swing.JButton();
        btnCobrar = new javax.swing.JButton();
        btnEliminar = new javax.swing.JButton();
        btnLimpiar = new javax.swing.JButton();
        txtFechaFin = new javax.swing.JTextField();
        jLabel6 = new javax.swing.JLabel();
        txtTotal = new javax.swing.JTextField();
        jPanel2 = new javax.swing.JPanel();
        jScrollPane1 = new javax.swing.JScrollPane();
        tblAlquileres = new javax.swing.JTable();

        setDefaultCloseOperation(javax.swing.WindowConstants.DO_NOTHING_ON_CLOSE);

        jPanel1.setBackground(new java.awt.Color(51, 51, 51));
        jPanel1.setBorder(javax.swing.BorderFactory.createTitledBorder(new javax.swing.border.LineBorder(new java.awt.Color(153, 153, 153), 2, true), "Datos de la reserva\n", javax.swing.border.TitledBorder.CENTER, javax.swing.border.TitledBorder.DEFAULT_POSITION, new java.awt.Font("Arial", 2, 14), new java.awt.Color(255, 255, 255))); // NOI18N

        jLabel1.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        jLabel1.setForeground(new java.awt.Color(255, 255, 255));
        jLabel1.setText("Codigo:");

        txtCodigo.setBackground(new java.awt.Color(153, 153, 153));
        txtCodigo.setFont(new java.awt.Font("Verdana", 1, 14)); // NOI18N
        txtCodigo.setForeground(new java.awt.Color(255, 255, 255));

        jLabel2.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        jLabel2.setForeground(new java.awt.Color(255, 255, 255));
        jLabel2.setText("Sala:");

        txtDoctor.setBackground(new java.awt.Color(153, 153, 153));
        txtDoctor.setFont(new java.awt.Font("Verdana", 1, 14)); // NOI18N
        txtDoctor.setForeground(new java.awt.Color(255, 255, 255));

        jLabel3.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        jLabel3.setForeground(new java.awt.Color(255, 255, 255));
        jLabel3.setText("Doctor:");

        txtFechaInicio.setBackground(new java.awt.Color(153, 153, 153));
        txtFechaInicio.setFont(new java.awt.Font("Verdana", 1, 14)); // NOI18N
        txtFechaInicio.setForeground(new java.awt.Color(255, 255, 255));

        jLabel4.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        jLabel4.setForeground(new java.awt.Color(255, 255, 255));
        jLabel4.setText("Fecha Inicio:");

        jLabel5.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        jLabel5.setForeground(new java.awt.Color(255, 255, 255));
        jLabel5.setText("Fecha Fin:");

        cboxSala.setBackground(new java.awt.Color(153, 153, 153));
        cboxSala.setFont(new java.awt.Font("Verdana", 1, 14)); // NOI18N
        cboxSala.setForeground(new java.awt.Color(255, 255, 255));

        btnAlquilar.setBackground(new java.awt.Color(153, 153, 153));
        btnAlquilar.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        btnAlquilar.setForeground(new java.awt.Color(255, 255, 255));
        btnAlquilar.setText("Alquilar");
        btnAlquilar.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnAlquilarActionPerformed(evt);
            }
        });

        btnCobrar.setBackground(new java.awt.Color(153, 153, 153));
        btnCobrar.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        btnCobrar.setForeground(new java.awt.Color(255, 255, 255));
        btnCobrar.setText("Cobrar");
        btnCobrar.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnCobrarActionPerformed(evt);
            }
        });

        btnEliminar.setBackground(new java.awt.Color(153, 153, 153));
        btnEliminar.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        btnEliminar.setForeground(new java.awt.Color(255, 255, 255));
        btnEliminar.setText("Eliminar");
        btnEliminar.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnEliminarActionPerformed(evt);
            }
        });

        btnLimpiar.setBackground(new java.awt.Color(153, 153, 153));
        btnLimpiar.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        btnLimpiar.setForeground(new java.awt.Color(255, 255, 255));
        btnLimpiar.setText("Limpiar");
        btnLimpiar.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnLimpiarActionPerformed(evt);
            }
        });

        txtFechaFin.setBackground(new java.awt.Color(153, 153, 153));
        txtFechaFin.setFont(new java.awt.Font("Verdana", 1, 14)); // NOI18N
        txtFechaFin.setForeground(new java.awt.Color(255, 255, 255));

        jLabel6.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        jLabel6.setForeground(new java.awt.Color(255, 255, 255));
        jLabel6.setText("Total:");

        txtTotal.setBackground(new java.awt.Color(153, 153, 153));
        txtTotal.setFont(new java.awt.Font("Verdana", 1, 14)); // NOI18N
        txtTotal.setForeground(new java.awt.Color(255, 255, 255));

        javax.swing.GroupLayout jPanel1Layout = new javax.swing.GroupLayout(jPanel1);
        jPanel1.setLayout(jPanel1Layout);
        jPanel1Layout.setHorizontalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel1Layout.createSequentialGroup()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addGroup(jPanel1Layout.createSequentialGroup()
                        .addContainerGap()
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                            .addComponent(jLabel3)
                            .addComponent(jLabel4)
                            .addComponent(jLabel5)
                            .addComponent(jLabel2)
                            .addComponent(jLabel6)
                            .addComponent(jLabel1))
                        .addGap(21, 21, 21)
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                            .addComponent(txtDoctor)
                            .addComponent(txtFechaInicio)
                            .addComponent(txtFechaFin)
                            .addComponent(txtTotal)
                            .addComponent(txtCodigo)
                            .addComponent(cboxSala, 0, 396, Short.MAX_VALUE)))
                    .addGroup(jPanel1Layout.createSequentialGroup()
                        .addGap(64, 64, 64)
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                            .addComponent(btnCobrar, javax.swing.GroupLayout.PREFERRED_SIZE, 114, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(btnAlquilar, javax.swing.GroupLayout.PREFERRED_SIZE, 114, javax.swing.GroupLayout.PREFERRED_SIZE))
                        .addGap(18, 18, 18)
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                            .addComponent(btnLimpiar, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                            .addComponent(btnEliminar, javax.swing.GroupLayout.PREFERRED_SIZE, 114, javax.swing.GroupLayout.PREFERRED_SIZE))))
                .addContainerGap())
        );
        jPanel1Layout.setVerticalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel1Layout.createSequentialGroup()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(txtCodigo, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(jLabel1))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel2)
                    .addComponent(cboxSala, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(txtDoctor, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(jLabel3))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel4)
                    .addComponent(txtFechaInicio, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel5)
                    .addComponent(txtFechaFin, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel6)
                    .addComponent(txtTotal, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(39, 39, 39)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(btnEliminar, javax.swing.GroupLayout.PREFERRED_SIZE, 35, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnAlquilar, javax.swing.GroupLayout.PREFERRED_SIZE, 35, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(btnLimpiar, javax.swing.GroupLayout.PREFERRED_SIZE, 35, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnCobrar, javax.swing.GroupLayout.PREFERRED_SIZE, 35, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(0, 0, Short.MAX_VALUE))
        );

        jPanel2.setBackground(new java.awt.Color(51, 51, 51));
        jPanel2.setBorder(javax.swing.BorderFactory.createTitledBorder(new javax.swing.border.LineBorder(new java.awt.Color(153, 153, 153), 2, true), "Datos de la reserva\n", javax.swing.border.TitledBorder.CENTER, javax.swing.border.TitledBorder.DEFAULT_POSITION, new java.awt.Font("Arial", 2, 14), new java.awt.Color(255, 255, 255))); // NOI18N

        tblAlquileres.setFont(new java.awt.Font("Arial", 2, 14)); // NOI18N
        tblAlquileres.setForeground(new java.awt.Color(0, 0, 0));
        tblAlquileres.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null},
                {null, null, null, null}
            },
            new String [] {
                "Title 1", "Title 2", "Title 3", "Title 4"
            }
        ));
        tblAlquileres.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                tblAlquileresMouseClicked(evt);
            }
        });
        jScrollPane1.setViewportView(tblAlquileres);

        javax.swing.GroupLayout jPanel2Layout = new javax.swing.GroupLayout(jPanel2);
        jPanel2.setLayout(jPanel2Layout);
        jPanel2Layout.setHorizontalGroup(
            jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel2Layout.createSequentialGroup()
                .addContainerGap()
                .addComponent(jScrollPane1, javax.swing.GroupLayout.DEFAULT_SIZE, 839, Short.MAX_VALUE)
                .addContainerGap())
        );
        jPanel2Layout.setVerticalGroup(
            jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel2Layout.createSequentialGroup()
                .addComponent(jScrollPane1, javax.swing.GroupLayout.PREFERRED_SIZE, 359, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addGap(0, 5, Short.MAX_VALUE))
        );

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addComponent(jPanel1, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addGap(18, 18, 18)
                .addComponent(jPanel2, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                    .addComponent(jPanel2, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addComponent(jPanel1, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
                .addContainerGap(javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void btnAlquilarActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnAlquilarActionPerformed
        String doctor = txtDoctor.getText();
        Sala sala = (Sala) cboxSala.getSelectedItem();

        if (sala.getCodigo() == -1) {
            JOptionPane.showMessageDialog(null,
                    "No hay salas disponibles en este momento",
                    "Atencion",
                    JOptionPane.WARNING_MESSAGE);
            return;
        }

        Alquiler alquiler = new Alquiler(sala.getCodigo(), doctor);
        alquiler.alquilar();

        sala.setEstado(Estado.Alquilada);
        sala.modificar();

        limpiar();
        mostrarAlquileres();
    }//GEN-LAST:event_btnAlquilarActionPerformed

    private void btnCobrarActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnCobrarActionPerformed
        Sala sala = (Sala) cboxSala.getSelectedItem();
        
        int codigo = Integer.parseInt(txtCodigo.getText());
        double total = Double.parseDouble(txtTotal.getText());
        Alquiler alquiler = new Alquiler(codigo, total);
        alquiler.cobrar();

        sala.setEstado(Estado.Disponible);
        sala.modificar();

        limpiar();
        mostrarAlquileres();
    }//GEN-LAST:event_btnCobrarActionPerformed

    private void btnEliminarActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnEliminarActionPerformed
        int boton = JOptionPane.showConfirmDialog(this,
                "¿Desea eliminar el alquiler?",
                "Eliminar Alquiler",
                JOptionPane.OK_CANCEL_OPTION);

        if (boton == 0) {
            int codigo = Integer.parseInt(txtCodigo.getText());
            Alquiler alquiler = new Alquiler(codigo);
            alquiler.eliminar();

            limpiar();
            mostrarAlquileres();
        } else {
            JOptionPane.showMessageDialog(this, "Eliminacion cancelada.");
        }
    }//GEN-LAST:event_btnEliminarActionPerformed

    private void btnLimpiarActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnLimpiarActionPerformed
        limpiar();
    }//GEN-LAST:event_btnLimpiarActionPerformed

    private void tblAlquileresMouseClicked(java.awt.event.MouseEvent evt) {//GEN-FIRST:event_tblAlquileresMouseClicked
        int fila = tblAlquileres.getSelectedRow();
        txtCodigo.setText(tblAlquileres.getValueAt(fila, 0).toString());
        Sala sala = obtenerTipoSala(tblAlquileres.getValueAt(fila, 1).toString());
        txtDoctor.setText(tblAlquileres.getValueAt(fila, 2).toString());
        txtFechaInicio.setText(tblAlquileres.getValueAt(fila, 3).toString());
        String total = tblAlquileres.getValueAt(fila, 5).toString();
        btnAlquilar.setEnabled(false);
        btnEliminar.setEnabled(true);
        Sala salon = (Sala) cboxSala.getSelectedItem();
        if (total.contains("N/A")) {

            btnCobrar.setEnabled(true);

            txtFechaFin.setText(GestionFecha.obtenerFechaActualTxt());

            DateTimeFormatter formato = DateTimeFormatter.ofPattern
        ("yyyy-MM-dd HH:mm");
            LocalDateTime localDateTimeInicial = LocalDateTime.parse
        (tblAlquileres.getValueAt(fila, 3).toString(),
                    formato);
            Duration duration = Duration.between(localDateTimeInicial,
                    GestionFecha.obtenerFechaActual());

            long horas = duration.toHours();

            double totalMonto = (horas + 1 * salon.getPrecioPorHora());

            txtTotal.setText(String.valueOf(totalMonto));
        } else {
            btnCobrar.setEnabled(false);
            txtFechaFin.setText(tblAlquileres.getValueAt(fila, 4).toString());
            txtTotal.setText(total.substring(1));
        }


    }//GEN-LAST:event_tblAlquileresMouseClicked

    public void mostrarAlquileres() {
        DefaultTableModel modelo = Alquiler.consultar(salas);
        tblAlquileres.setModel(modelo);
    }

    public void limpiar() {
        cargarSalasCbox();
        txtCodigo.setText("");
        cboxSala.setSelectedIndex(0);
        txtDoctor.setText("");
        txtFechaInicio.setText(GestionFecha.obtenerFechaActualTxt());
        txtFechaFin.setText("N/A");
        txtTotal.setText("N/A");

        btnAlquilar.setEnabled(true);
        btnCobrar.setEnabled(false);
        btnEliminar.setEnabled(false);

    }

    public void cargarSalasCbox() {
        cboxSala.removeAllItems();
        salas = Sala.cargarSalas();

        boolean disponibilidad = false;

        for (Sala sala : salas) {
            if (sala.getEstado() == Estado.Disponible) {
                cboxSala.addItem(sala);
                cboxSala.setEnabled(true);
                disponibilidad = true;
            }
        }

        if (!disponibilidad) {
            Sala sala = new Sala();
            cboxSala.addItem(sala);
            cboxSala.setEnabled(false);
        }

    }

    public Sala obtenerTipoSala(String modelo) {
        for (Sala sala : salas) {
            if (sala.getTipoSala().equals(modelo)) {
                cboxSala.addItem(sala);
                cboxSala.setSelectedItem(sala);
                cboxSala.setEnabled(false);
                return sala;
            }
        }
        return null;
    }
    ArrayList<Sala> salas;
    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton btnAlquilar;
    private javax.swing.JButton btnCobrar;
    private javax.swing.JButton btnEliminar;
    private javax.swing.JButton btnLimpiar;
    private javax.swing.JComboBox cboxSala;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JLabel jLabel3;
    private javax.swing.JLabel jLabel4;
    private javax.swing.JLabel jLabel5;
    private javax.swing.JLabel jLabel6;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JPanel jPanel2;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTable tblAlquileres;
    private javax.swing.JTextField txtCodigo;
    private javax.swing.JTextField txtDoctor;
    private javax.swing.JTextField txtFechaFin;
    private javax.swing.JTextField txtFechaInicio;
    private javax.swing.JTextField txtTotal;
    // End of variables declaration//GEN-END:variables
}
