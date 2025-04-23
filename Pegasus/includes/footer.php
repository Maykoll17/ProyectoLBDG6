</div><!-- Fin del contenedor principal -->

    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase"><?php echo SITE_NAME; ?></h5>
                    <p>
                        Sistema de Gestión Hospitalaria desarrollado para optimizar y mejorar
                        la gestión de pacientes, citas, medicamentos y facturación.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Enlaces</h5>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <a href="<?php echo BASE_URL; ?>" class="text-dark">Inicio</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="text-dark">Dashboard</a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/pages/perfil/index.php" class="text-dark">Mi Perfil</a>
                        </li>
                        <?php else: ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/pages/login.php" class="text-dark">Iniciar Sesión</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Soporte</h5>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <a href="#" class="text-dark">Manual de Usuario</a>
                        </li>
                        <li>
                            <a href="#" class="text-dark">Preguntas Frecuentes</a>
                        </li>
                        <li>
                            <a href="#" class="text-dark">Contacto</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
            © <?php echo date('Y'); ?> Copyright:
            <a class="text-dark" href="<?php echo BASE_URL; ?>"><?php echo SITE_NAME; ?></a>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($pageScript)): ?>
    <script src="<?php echo JS_URL; ?>/<?php echo $pageScript; ?>.js"></script>
    <?php endif; ?>
</body>
</html>