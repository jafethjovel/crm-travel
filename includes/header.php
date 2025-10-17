<?php
// Aplicar configuraciones del sistema
if (file_exists(__DIR__ . '/../php/config.php')) {
    require_once __DIR__ . '/../php/config.php';
    aplicarConfiguracionesSistema();
}

// Verificar autenticación en páginas que lo requieran
$auth_required = basename($_SERVER['PHP_SELF']) != 'login.php';
if ($auth_required && !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Datos del usuario actual
$usuario_actual = null;
if (isset($_SESSION['usuario_id'])) {
    // En una implementación real, cargarías los datos desde la base de datos
    $usuario_actual = [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'] ?? 'Usuario',
        'email' => $_SESSION['usuario_email'] ?? '',
        'rol' => $_SESSION['usuario_rol'] ?? 'vendedor'
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Civitur Travel' : 'Civitur Travel'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --success-color: #27ae60;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        /* Navbar estilo Civitur */
        .navbar-civit {
            background-color: var(--primary-color);
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            color: white !important;
        }
        
        /* Sidebar estilo Civitur */
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 56px;
            left: 0;
            padding-top: 1rem;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.2rem;
        }
        
        .sidebar-menu a {
            color: white;
            padding: 0.8rem 1.5rem;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--secondary-color);
        }
        
        .sidebar-menu a i {
            width: 25px;
        }
        
        .sidebar-header {
            padding: 0 1.5rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #b8c7ce;
        }
        
        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            margin-top: 56px;
            min-height: calc(100vh - 56px);
            transition: all 0.3s;
        }
        
        /* Cards */
        .card-civit {
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .card-header-civit {
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px 5px 0 0 !important;
            padding: 12px 20px;
            font-weight: 600;
        }
        
        /* Buttons */
        .btn-civit-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }
        
        .btn-civit-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            color: white;
        }
        
        .btn-civit-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }
        
        /* Stats cards */
        .stat-card {
            border-radius: 5px;
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .bg-stat-1 { background-color: #3498db; color: white; }
        .bg-stat-2 { background-color: #2ecc71; color: white; }
        .bg-stat-3 { background-color: #e74c3c; color: white; }
        .bg-stat-4 { background-color: #f39c12; color: white; }
        
        /* Badges */
        .badge-confirmed { background-color: var(--success-color); }
        .badge-pending { background-color: #f39c12; }
        .badge-transfer { background-color: #3498db; }
        .badge-rejected { background-color: #e74c3c; }
        
        /* Table */
        .table-civit th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-var(--sidebar-width));
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                width: var(--sidebar-width);
                transform: translateX(0);
            }
            
            .main-content.active {
                margin-left: var(--sidebar-width);
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-civit">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-plane me-2"></i>
                Civitur Travel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (isset($usuario_actual)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($usuario_actual['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>