package GestionCitas;

import GestionPersonas.Empleado;
import java.awt.Color;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.ArrayList;
import javax.swing.JFrame;
import javax.swing.JOptionPane;

public class GestionCitas extends javax.swing.JFrame {

    Gestion gestion;

    public GestionCitas(JFrame principal) {
        initComponents();
        gestion = new Gestion();
        setLocationRelativeTo(null);

        addWindowListener(new WindowAdapter() {
            @Override
            public void windowClosing(WindowEvent e) {
                principal.setVisible(true);
                dispose();
            }
        });

        for (Asunto asunto : Asunto.values()) {
            cbAsunto.addItem(asunto.toString());
        }
        txaMensaje.setVisible(false);
        btnProgramarCita.setEnabled(false);
        btnHora.setEnabled(false);
    }


    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        jComboBox1 = new javax.swing.JComboBox<>();
        jPanel1 = new javax.swing.JPanel();
        jLabel1 = new javax.swing.JLabel();
        jLabel2 = new javax.swing.JLabel();
        btnBuscar = new javax.swing.JButton();
        txtPaciente = new javax.swing.JTextField();
        txtEmpleado = new javax.swing.JTextField();
        cbFechas = new javax.swing.JComboBox<>();
        jLabel3 = new javax.swing.JLabel();
        jLabel4 = new javax.swing.JLabel();
        jLabel5 = new javax.swing.JLabel();
        cbHora = new javax.swing.JComboBox<>();
        jScrollPane1 = new javax.swing.JScrollPane();
        txaMensaje = new javax.swing.JTextArea();
        btnProgramarCita = new javax.swing.JButton();
        btnHora = new javax.swing.JButton();
        cbAsunto = new javax.swing.JComboBox<>();
        btnResetear = new javax.swing.JButton();

        jComboBox1.setModel(new javax.swing.DefaultComboBoxModel<>(new String[] { "Item 1", "Item 2", "Item 3", "Item 4" }));

        setDefaultCloseOperation(javax.swing.WindowConstants.DO_NOTHING_ON_CLOSE);

        jLabel1.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        jLabel1.setText("Cedula Paciente:");

        jLabel2.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        jLabel2.setText("Cedula Empleado:");

        btnBuscar.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        btnBuscar.setForeground(new java.awt.Color(51, 51, 255));
        btnBuscar.setText("Buscar");
        btnBuscar.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(51, 51, 255)));
        btnBuscar.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnBuscarActionPerformed(evt);
            }
        });

        txtPaciente.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        txtPaciente.setText("321654987");
        txtPaciente.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                txtPacienteActionPerformed(evt);
            }
        });

        txtEmpleado.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        txtEmpleado.setText("123456789");
        txtEmpleado.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                txtEmpleadoActionPerformed(evt);
            }
        });

        cbFechas.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N

        jLabel3.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        jLabel3.setText("Fecha:");

        jLabel4.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        jLabel4.setText("Hora:");

        jLabel5.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        jLabel5.setText("Asunto:");

        cbHora.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N

        txaMensaje.setColumns(20);
        txaMensaje.setFont(new java.awt.Font("Segoe UI", 1, 18)); // NOI18N
        txaMensaje.setRows(5);
        txaMensaje.setText("Mensaje:\n");
        jScrollPane1.setViewportView(txaMensaje);

        btnProgramarCita.setBackground(new java.awt.Color(51, 51, 255));
        btnProgramarCita.setFont(new java.awt.Font("Segoe UI", 1, 18)); // NOI18N
        btnProgramarCita.setForeground(new java.awt.Color(255, 255, 255));
        btnProgramarCita.setText("Programar cita");
        btnProgramarCita.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(51, 51, 255)));
        btnProgramarCita.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnProgramarCitaActionPerformed(evt);
            }
        });

        btnHora.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N
        btnHora.setForeground(new java.awt.Color(51, 51, 255));
        btnHora.setText("Seleccionar hora");
        btnHora.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(51, 51, 255)));
        btnHora.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnHoraActionPerformed(evt);
            }
        });

        cbAsunto.setFont(new java.awt.Font("Segoe UI", 1, 12)); // NOI18N

        btnResetear.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnResetear.setForeground(new java.awt.Color(51, 51, 255));
        btnResetear.setText("Resetear");
        btnResetear.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(51, 51, 255)));
        btnResetear.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnResetearActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout jPanel1Layout = new javax.swing.GroupLayout(jPanel1);
        jPanel1.setLayout(jPanel1Layout);
        jPanel1Layout.setHorizontalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, jPanel1Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                    .addComponent(btnResetear, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addComponent(btnProgramarCita, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addGroup(jPanel1Layout.createSequentialGroup()
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                            .addGroup(javax.swing.GroupLayout.Alignment.LEADING, jPanel1Layout.createSequentialGroup()
                                .addComponent(jLabel1, javax.swing.GroupLayout.PREFERRED_SIZE, 101, javax.swing.GroupLayout.PREFERRED_SIZE)
                                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                                .addComponent(txtPaciente))
                            .addGroup(javax.swing.GroupLayout.Alignment.LEADING, jPanel1Layout.createSequentialGroup()
                                .addComponent(jLabel3, javax.swing.GroupLayout.PREFERRED_SIZE, 101, javax.swing.GroupLayout.PREFERRED_SIZE)
                                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                                .addComponent(cbFechas, 0, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
                            .addGroup(javax.swing.GroupLayout.Alignment.LEADING, jPanel1Layout.createSequentialGroup()
                                .addComponent(jLabel2, javax.swing.GroupLayout.PREFERRED_SIZE, 101, javax.swing.GroupLayout.PREFERRED_SIZE)
                                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                                .addComponent(txtEmpleado))
                            .addGroup(javax.swing.GroupLayout.Alignment.LEADING, jPanel1Layout.createSequentialGroup()
                                .addComponent(jLabel4, javax.swing.GroupLayout.PREFERRED_SIZE, 101, javax.swing.GroupLayout.PREFERRED_SIZE)
                                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                                .addComponent(cbHora, 0, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
                            .addGroup(jPanel1Layout.createSequentialGroup()
                                .addComponent(jLabel5, javax.swing.GroupLayout.PREFERRED_SIZE, 101, javax.swing.GroupLayout.PREFERRED_SIZE)
                                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                                .addComponent(cbAsunto, 0, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)))
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                            .addComponent(btnBuscar, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                            .addComponent(btnHora, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)))
                    .addComponent(jScrollPane1, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.DEFAULT_SIZE, 598, Short.MAX_VALUE))
                .addContainerGap())
        );
        jPanel1Layout.setVerticalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel1Layout.createSequentialGroup()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addGroup(jPanel1Layout.createSequentialGroup()
                        .addGap(34, 34, 34)
                        .addComponent(btnBuscar, javax.swing.GroupLayout.PREFERRED_SIZE, 40, javax.swing.GroupLayout.PREFERRED_SIZE))
                    .addGroup(jPanel1Layout.createSequentialGroup()
                        .addContainerGap()
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                            .addComponent(jLabel1, javax.swing.GroupLayout.PREFERRED_SIZE, 40, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(txtPaciente, javax.swing.GroupLayout.PREFERRED_SIZE, 40, javax.swing.GroupLayout.PREFERRED_SIZE))
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                            .addComponent(jLabel2, javax.swing.GroupLayout.PREFERRED_SIZE, 43, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(txtEmpleado, javax.swing.GroupLayout.PREFERRED_SIZE, 40, javax.swing.GroupLayout.PREFERRED_SIZE))))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel5, javax.swing.GroupLayout.PREFERRED_SIZE, 43, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(cbAsunto, javax.swing.GroupLayout.PREFERRED_SIZE, 43, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel3, javax.swing.GroupLayout.PREFERRED_SIZE, 43, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(cbFechas, javax.swing.GroupLayout.PREFERRED_SIZE, 43, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnHora, javax.swing.GroupLayout.PREFERRED_SIZE, 40, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(jLabel4, javax.swing.GroupLayout.PREFERRED_SIZE, 43, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(cbHora, javax.swing.GroupLayout.PREFERRED_SIZE, 43, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addComponent(jScrollPane1, javax.swing.GroupLayout.PREFERRED_SIZE, 135, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(btnProgramarCita, javax.swing.GroupLayout.PREFERRED_SIZE, 40, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(btnResetear, javax.swing.GroupLayout.PREFERRED_SIZE, 40, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addContainerGap(9, Short.MAX_VALUE))
        );

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                .addContainerGap(javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                .addComponent(jPanel1, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addContainerGap())
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addComponent(jPanel1, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void btnBuscarActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnBuscarActionPerformed

        txaMensaje.setVisible(true);
        cbFechas.removeAllItems();
        String cedula_empleado = Empleado.formatear_cedula
        (txtEmpleado.getText());
        String cedula_paciente = Empleado.formatear_cedula
        (txtPaciente.getText());
        if (!cedula_empleado.equals("") && !cedula_paciente.equals("") && 
                gestion.verificar_cedula(cedula_empleado, 1)
                && gestion.verificar_cedula(cedula_paciente, 2)) {

            gestion.actualizar_fechas_disponibles(cedula_empleado, 
                    cedula_paciente);
            ArrayList<String> fechas_usadas = new ArrayList();
            fechas_usadas.add(gestion.fechas_disponibles.get(1).toString().
                    substring(0, 10));
            cbFechas.addItem(gestion.fechas_disponibles.get(1).toString().
                    substring(0, 10));
            for (LocalDateTime fecha : gestion.fechas_disponibles) {
                if (!fechas_usadas.contains(fecha.toString().substring(0, 10))){
                    cbFechas.addItem(fecha.toString().substring(0, 10));
                    fechas_usadas.add(fecha.toString().substring(0, 10));
                }
            }
            txaMensaje.setText("""
                               Empleado y Paciente encontrados con exito, 
                               seleccione una fecha y un asunto,
                               luego dele al boton seleccionar hora""");
            txaMensaje.setForeground(Color.green);
            txtEmpleado.setEnabled(false);
            txtPaciente.setEnabled(false);
            btnBuscar.setEnabled(false);
            btnHora.setEnabled(true);
        } else {
            txaMensaje.setText("""
                               La cedula no se encuentra en el sistema o
                               el formato es incorrecto,
                               recuerde que las cedulas consisten de 9 numeros
                               los espacios son aceptados""");
            txaMensaje.setForeground(Color.red);
        }


    }//GEN-LAST:event_btnBuscarActionPerformed

    private void txtEmpleadoActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_txtEmpleadoActionPerformed
        // TODO add your handling code here:
    }//GEN-LAST:event_txtEmpleadoActionPerformed

    private void txtPacienteActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_txtPacienteActionPerformed
        // TODO add your handling code here:
    }//GEN-LAST:event_txtPacienteActionPerformed

    private void btnProgramarCitaActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnProgramarCitaActionPerformed

        boolean exito = false;
        int duracion = Cita.devolver_duracion_asunto(Asunto.valueOf
        (cbAsunto.getSelectedItem().toString()));
        for (int i = 0; i < duracion; i++) {
            LocalDateTime fecha = LocalDateTime.parse(cbHora.getSelectedItem().
                    toString());
            fecha = fecha.plusMinutes(15 * i);
            Cita cita = new Cita(Empleado.formatear_cedula(txtEmpleado.getText()),
                    Empleado.formatear_cedula(txtPaciente.getText()),
                    fecha);
            exito = cita.agregar();
        }
        if (exito) {
            txaMensaje.setText("Cita programada con exito");
            txaMensaje.setForeground(Color.green);
            reset();

        } else {
            txaMensaje.setText("Hubo un problema, "
                    + "por favor verifique la base de datos");
            txaMensaje.setForeground(Color.red);
        }


    }//GEN-LAST:event_btnProgramarCitaActionPerformed

    private void btnHoraActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnHoraActionPerformed
        cbHora.removeAllItems();
        String cedula_empleado = Empleado.formatear_cedula(txtEmpleado.
                getText());
        String cedula_paciente = Empleado.formatear_cedula(txtPaciente.
                getText());
        gestion.actualizar_fechas_disponibles(cedula_empleado, cedula_paciente);
        String fecha_seleccionada = cbFechas.getSelectedItem().toString();
        int duracion = Cita.devolver_duracion_asunto(Asunto.valueOf((String)
                cbAsunto.getSelectedItem()));
        duracion -= 1;

        for (int i = 0; i < gestion.fechas_disponibles.size() - duracion; i++) {
            LocalDateTime fecha_antes = gestion.fechas_disponibles.get(0);
            fecha_antes = fecha_antes.plusMinutes(i * 15);
            if (i > duracion) {
                fecha_antes = gestion.fechas_disponibles.get(i - duracion);
                fecha_antes = fecha_antes.plusMinutes(duracion * 15);
            }
            LocalDateTime fecha_actual = gestion.fechas_disponibles.get(i);
            LocalDateTime fecha_ultima = gestion.fechas_disponibles.get
        (i + duracion);
            fecha_ultima = fecha_ultima.minusMinutes(duracion * 15);
            if (fecha_seleccionada.equals(fecha_actual.toString().substring
        (0, 10))
                    && fecha_actual.toString().substring(0, 16).equals
        (fecha_ultima.toString().substring(0, 16))
                    && fecha_actual.toString().substring(0, 16).equals
        (fecha_antes.toString().substring(0, 16))) { //
                cbHora.addItem(fecha_actual.toString().substring(0, 16));
            }
        }
        txaMensaje.setText("Horas disponibles cargadas con exito\n, escoja una "
                + "hora y dele a programar cita");
        txaMensaje.setForeground(Color.green);
        cbAsunto.setEnabled(false);
        cbFechas.setEnabled(false);
        btnHora.setEnabled(false);
        btnProgramarCita.setEnabled(true);
    }//GEN-LAST:event_btnHoraActionPerformed

    public void reset() {
        btnBuscar.setEnabled(true);
        btnHora.setEnabled(true);
        cbFechas.removeAllItems();
        cbHora.removeAllItems();
        cbAsunto.setEnabled(true);
        cbFechas.setEnabled(true);
        txtEmpleado.setEnabled(true);
        txtPaciente.setEnabled(true);
        txaMensaje.setVisible(false);
        btnProgramarCita.setEnabled(false);
        btnHora.setEnabled(false);
    }
    private void btnResetearActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnResetearActionPerformed
        reset();
    }//GEN-LAST:event_btnResetearActionPerformed


    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton btnBuscar;
    private javax.swing.JButton btnHora;
    private javax.swing.JButton btnProgramarCita;
    private javax.swing.JButton btnResetear;
    private javax.swing.JComboBox<String> cbAsunto;
    private javax.swing.JComboBox<String> cbFechas;
    private javax.swing.JComboBox<String> cbHora;
    private javax.swing.JComboBox<String> jComboBox1;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JLabel jLabel3;
    private javax.swing.JLabel jLabel4;
    private javax.swing.JLabel jLabel5;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTextArea txaMensaje;
    private javax.swing.JTextField txtEmpleado;
    private javax.swing.JTextField txtPaciente;
    // End of variables declaration//GEN-END:variables
}
