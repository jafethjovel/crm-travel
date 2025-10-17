<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determinar página activa
$current_page = basename($_SERVER['PHP_SELF']);
$usuario_rol = $_SESSION['usuario_rol'] ?? 'vendedor';

// Incluir funciones de autenticación si existe
$auth_file = __DIR__ . '/../php/auth.php';
if (file_exists($auth_file)) {
    require_once $auth_file;
} else {
    // Funciones básicas si no existe auth.php
    function tienePermiso($rol_requerido) {
        global $usuario_rol;
        $roles_requeridos = is_array($rol_requerido) ? $rol_requerido : [$rol_requerido];
        return in_array($usuario_rol, $roles_requeridos);
    }
}

// Definir roles permitidos
$roles_vendedor = ['vendedor', 'admin', 'superadmin'];
$roles_admin = ['admin', 'superadmin'];
$roles_superadmin = ['superadmin'];
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">Navegación Principal</div>
    <ul class="sidebar-menu">
        <!-- Dashboard - Todos los roles -->
        <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-home me-2"></i> Dashboard</a></li>
        
        <!-- Cotizaciones - Vendedores, Admin y Superadmin -->
        <?php if (tienePermiso($roles_vendedor)): ?>
        <li><a href="cotizaciones.php" class="<?php echo $current_page == 'cotizaciones.php' ? 'active' : ''; ?>"><i class="fas fa-file-invoice me-2"></i> Cotizaciones</a></li>
        <?php endif; ?>
        
        <!-- Boletos Aéreos - Vendedores, Admin y Superadmin -->
        <?php if (tienePermiso($roles_vendedor)): ?>
        <li><a href="vuelos.php" class="<?php echo $current_page == 'vuelos.php' ? 'active' : ''; ?>"><i class="fas fa-plane me-2"></i> Boletos Aéreos</a></li>
        <?php endif; ?>
        
        <!-- Clientes - Vendedores, Admin y Superadmin -->
        <?php if (tienePermiso($roles_vendedor)): ?>
        <li><a href="clientes.php" class="<?php echo $current_page == 'clientes.php' ? 'active' : ''; ?>"><i class="fas fa-users me-2"></i> Clientes</a></li>
        <?php endif; ?>
        
        <!-- Hoteles - Vendedores, Admin y Superadmin -->
        <?php if (tienePermiso($roles_vendedor)): ?>
        <li><a href="hoteles.php" class="<?php echo $current_page == 'hoteles.php' ? 'active' : ''; ?>"><i class="fas fa-hotel me-2"></i> Hoteles</a></li>
        <?php endif; ?>
    </ul>
    
    <!-- Sección de Administración - Solo Admin y Superadmin -->
    <?php if (tienePermiso($roles_admin)): ?>
    <div class="sidebar-header mt-4">Administración</div>
    <ul class="sidebar-menu">
        <!-- Usuarios - Solo Superadmin -->
        <?php if (tienePermiso($roles_superadmin)): ?>
        <li><a href="usuarios.php" class="<?php echo $current_page == 'usuarios.php' ? 'active' : ''; ?>"><i class="fas fa-user-cog me-2"></i> Usuarios</a></li>
        <?php endif; ?>
        
        <!-- Reportes - Admin y Superadmin -->
        <?php if (tienePermiso($roles_admin)): ?>
        <li><a href="reportes.php" class="<?php echo $current_page == 'reportes.php' ? 'active' : ''; ?>"><i class="fas fa-chart-bar me-2"></i> Reportes</a></li>
        <?php endif; ?>
        
        <!-- Configuración - Admin y Superadmin -->
<?php if (tienePermiso(ROLES_ADMIN)): ?>
<li><a href="configuracion.php" class="<?php echo $current_page == 'configuracion.php' ? 'active' : ''; ?>"><i class="fas fa-cog me-2"></i> Configuración</a></li>
<?php endif; ?>
    </ul>
    <?php endif; ?>
    
    <!-- Sección adicional para Superadmin -->
    <?php if (tienePermiso($roles_superadmin)): ?>
    <div class="sidebar-header mt-4">Super Administración</div>
    <ul class="sidebar-menu">
        <li><a href="backup.php" class="<?php echo $current_page == 'backup.php' ? 'active' : ''; ?>"><i class="fas fa-database me-2"></i> Backup</a></li>
        <li><a href="logs.php" class="<?php echo $current_page == 'logs.php' ? 'active' : ''; ?>"><i class="fas fa-clipboard-list me-2"></i> Logs del Sistema</a></li>
    </ul>
    <?php endif; ?>
    
    <!-- Información del usuario -->
    <div class="sidebar-footer p-3 mt-auto">
        <div class="text-center">
            <small class="text-light">Conectado como:</small>
            <br>
            <strong class="text-white"><?php echo $_SESSION['usuario_nombre'] ?? 'Usuario'; ?></strong>
            <br>
            <span class="badge bg-<?php 
                switch($usuario_rol) {
                    case 'superadmin': echo 'danger'; break;
                    case 'admin': echo 'warning'; break;
                    case 'vendedor': echo 'info'; break;
                    default: echo 'secondary';
                }
            ?>"><?php echo ucfirst($usuario_rol); ?></span>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">