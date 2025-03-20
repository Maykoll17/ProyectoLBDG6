package Inicio;

import GestionCitas.GestionCitas;

import GestionFacturacion.InterfazPrincipalFacturacion;

import GestionFacturacion.GestionModificarMedicamentos;
import GestionFacturacion.InterfazDecisionInventario;
import GestionPersonas.ModificaEmpleado;
import GestionPersonas.ModificarPacienteCompleto;
import com.mycompany.pegasus.Salas.Sala;
import com.mycompany.pegasus.Salas.PantallaDeSalasOpcion;
import javax.swing.JButton;
import javax.swing.JOptionPane;

public class MenuPrincipalEmpleados extends javax.swing.JFrame {

    String permisos;
    GestionAutentificacion gu;

    public MenuPrincipalEmpleados(String permisos) {

        gu = new GestionAutentificacion();

        this.permisos = permisos;
        initComponents();
        setLocationRelativeTo(null);
        if (permisos.charAt(0) == 'F') {
            dispose();
            JOptionPane.showMessageDialog(null, "Usted no cuenta con los "
                    + "permisos para proceder");
        }
        habilitar_botones();
    }

    public void habilitar_botones() {
        JButton[] botones = {btnGestionPacientes, btnGestionEmpleados,
            btnGestionUsuarios,
            btnGestionSalas, btnGestionCitas, btnGestionFinanzas, 
            btnGestionInventario
        };
        int contador = 0;

        for (JButton boton : botones) {
            if (permisos.charAt(contador) == 'T') {
                boton.setVisible(true);
            } else {
                boton.setVisible(false);
            }
            contador += 1;
        }

    }

    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        buttonGroup = new javax.swing.ButtonGroup();
        jPanel1 = new javax.swing.JPanel();
        jPanel2 = new javax.swing.JPanel();
        btnGestionPacientes = new javax.swing.JButton();
        btnGestionEmpleados = new javax.swing.JButton();
        btnGestionUsuarios = new javax.swing.JButton();
        btnGestionSalas = new javax.swing.JButton();
        btnGestionCitas = new javax.swing.JButton();
        btnGestionFinanzas = new javax.swing.JButton();
        btnGestionInventario = new javax.swing.JButton();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);

        javax.swing.GroupLayout jPanel1Layout = new javax.swing.GroupLayout(jPanel1);
        jPanel1.setLayout(jPanel1Layout);
        jPanel1Layout.setHorizontalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGap(0, 324, Short.MAX_VALUE)
        );
        jPanel1Layout.setVerticalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGap(0, 295, Short.MAX_VALUE)
        );

        jPanel2.setBackground(new java.awt.Color(102, 102, 102));
        jPanel2.setBorder(javax.swing.BorderFactory.createTitledBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 0), 2), "Pagina Principal", javax.swing.border.TitledBorder.CENTER, javax.swing.border.TitledBorder.DEFAULT_POSITION, new java.awt.Font("Segoe UI", 1, 14), new java.awt.Color(255, 255, 255))); // NOI18N

        btnGestionPacientes.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnGestionPacientes.setForeground(new java.awt.Color(51, 0, 255));
        btnGestionPacientes.setText("Gestion Pacientes");
        btnGestionPacientes.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 204), 2));
        btnGestionPacientes.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGestionPacientesActionPerformed(evt);
            }
        });

        btnGestionEmpleados.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnGestionEmpleados.setForeground(new java.awt.Color(51, 0, 255));
        btnGestionEmpleados.setText("Gestion Empleados");
        btnGestionEmpleados.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 204), 2));
        btnGestionEmpleados.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGestionEmpleadosActionPerformed(evt);
            }
        });

        btnGestionUsuarios.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnGestionUsuarios.setForeground(new java.awt.Color(51, 0, 255));
        btnGestionUsuarios.setText("Gestion Usuarios");
        btnGestionUsuarios.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 204), 2));
        btnGestionUsuarios.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGestionUsuariosActionPerformed(evt);
            }
        });

        btnGestionSalas.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnGestionSalas.setForeground(new java.awt.Color(51, 0, 255));
        btnGestionSalas.setText("Gestion Salas");
        btnGestionSalas.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 204), 2));
        btnGestionSalas.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGestionSalasActionPerformed(evt);
            }
        });

        btnGestionCitas.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnGestionCitas.setForeground(new java.awt.Color(51, 0, 255));
        btnGestionCitas.setText("Gestion Citas");
        btnGestionCitas.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 204), 2));
        btnGestionCitas.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGestionCitasActionPerformed(evt);
            }
        });

        btnGestionFinanzas.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnGestionFinanzas.setForeground(new java.awt.Color(51, 0, 255));
        btnGestionFinanzas.setText("Gestion Finanzas");
        btnGestionFinanzas.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 204), 2));
        btnGestionFinanzas.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGestionFinanzasActionPerformed(evt);
            }
        });

        btnGestionInventario.setFont(new java.awt.Font("Segoe UI", 1, 14)); // NOI18N
        btnGestionInventario.setForeground(new java.awt.Color(51, 0, 255));
        btnGestionInventario.setText("Gestion Inventario");
        btnGestionInventario.setBorder(javax.swing.BorderFactory.createLineBorder(new java.awt.Color(0, 0, 204), 2));
        btnGestionInventario.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                btnGestionInventarioActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout jPanel2Layout = new javax.swing.GroupLayout(jPanel2);
        jPanel2.setLayout(jPanel2Layout);
        jPanel2Layout.setHorizontalGroup(
            jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel2Layout.createSequentialGroup()
                .addGap(60, 60, 60)
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                    .addComponent(btnGestionEmpleados, javax.swing.GroupLayout.PREFERRED_SIZE, 182, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionPacientes, javax.swing.GroupLayout.PREFERRED_SIZE, 182, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionSalas, javax.swing.GroupLayout.PREFERRED_SIZE, 182, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(33, 33, 33)
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(btnGestionCitas, javax.swing.GroupLayout.PREFERRED_SIZE, 182, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionInventario, javax.swing.GroupLayout.PREFERRED_SIZE, 182, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionFinanzas, javax.swing.GroupLayout.PREFERRED_SIZE, 182, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionUsuarios, javax.swing.GroupLayout.PREFERRED_SIZE, 182, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addContainerGap(52, Short.MAX_VALUE))
        );
        jPanel2Layout.setVerticalGroup(
            jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel2Layout.createSequentialGroup()
                .addGap(18, 18, 18)
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(btnGestionPacientes, javax.swing.GroupLayout.PREFERRED_SIZE, 56, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionInventario, javax.swing.GroupLayout.PREFERRED_SIZE, 56, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(btnGestionEmpleados, javax.swing.GroupLayout.PREFERRED_SIZE, 56, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionFinanzas, javax.swing.GroupLayout.PREFERRED_SIZE, 56, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addGroup(jPanel2Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(btnGestionSalas, javax.swing.GroupLayout.PREFERRED_SIZE, 56, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(btnGestionUsuarios, javax.swing.GroupLayout.PREFERRED_SIZE, 56, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(btnGestionCitas, javax.swing.GroupLayout.PREFERRED_SIZE, 56, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addContainerGap(144, Short.MAX_VALUE))
        );

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addComponent(jPanel2, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jPanel1, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addContainerGap())
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(jPanel2, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                        .addGap(0, 7, Short.MAX_VALUE))
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(jPanel1, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                        .addContainerGap(javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))))
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void btnGestionUsuariosActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGestionUsuariosActionPerformed
        Registro registro = new Registro(this);
        registro.setVisible(true);
        setVisible(false);
    }//GEN-LAST:event_btnGestionUsuariosActionPerformed

    private void btnGestionEmpleadosActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGestionEmpleadosActionPerformed
        ModificaEmpleado modEmp = new ModificaEmpleado(this);
        modEmp.setVisible(true);
        setVisible(false);
    }//GEN-LAST:event_btnGestionEmpleadosActionPerformed

    private void btnGestionPacientesActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGestionPacientesActionPerformed
        ModificarPacienteCompleto mpc = new ModificarPacienteCompleto(this);
        mpc.setVisible(true);
        setVisible(false);
    }//GEN-LAST:event_btnGestionPacientesActionPerformed

    private void btnGestionSalasActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGestionSalasActionPerformed
        PantallaDeSalasOpcion pn = new PantallaDeSalasOpcion(this);
        pn.setVisible(true);
        setVisible(false);
    }//GEN-LAST:event_btnGestionSalasActionPerformed

    private void btnGestionCitasActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGestionCitasActionPerformed
        GestionCitas gu = new GestionCitas(this);
        gu.setVisible(true);
        setVisible(false);
    }//GEN-LAST:event_btnGestionCitasActionPerformed


    private void btnGestionFinanzasActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGestionFinanzasActionPerformed
        InterfazPrincipalFacturacion ipf = new InterfazPrincipalFacturacion(this);
        ipf.setVisible(true);
        setVisible(false);
    }//GEN-LAST:event_btnGestionFinanzasActionPerformed

    private void btnGestionInventarioActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_btnGestionInventarioActionPerformed
        InterfazDecisionInventario gts = new InterfazDecisionInventario(this);
        gts.setVisible(true);
        setVisible(false);
    }//GEN-LAST:event_btnGestionInventarioActionPerformed


    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton btnGestionCitas;
    private javax.swing.JButton btnGestionEmpleados;
    private javax.swing.JButton btnGestionFinanzas;
    private javax.swing.JButton btnGestionInventario;
    private javax.swing.JButton btnGestionPacientes;
    private javax.swing.JButton btnGestionSalas;
    private javax.swing.JButton btnGestionUsuarios;
    private javax.swing.ButtonGroup buttonGroup;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JPanel jPanel2;
    // End of variables declaration//GEN-END:variables
}
