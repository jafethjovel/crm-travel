    </div><!-- Cierre de main-content -->
</div><!-- Cierre del wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
// Funcionalidad para el sidebar en móviles
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });
    }
    
    // Cerrar sidebar al hacer clic fuera en móviles
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const navbarToggler = document.querySelector('.navbar-toggler');
        
        if (window.innerWidth <= 768 && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(event.target) && 
            !navbarToggler.contains(event.target)) {
            sidebar.classList.remove('active');
            mainContent.classList.remove('active');
        }
    });
    
    // Manejar mensajes flash de sesión
    <?php if (isset($_SESSION['success_message'])): ?>
        alertify.success('<?php echo $_SESSION['success_message']; ?>');
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        alertify.error('<?php echo $_SESSION['error_message']; ?>');
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
});
</script>
</body>
</html>