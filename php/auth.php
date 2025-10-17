<?php
// php/auth.php - Funciones de autenticación y autorización

/**
 * Verifica si el usuario está autenticado
 * Redirige al login si no está autenticado
 */
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Verifica si el usuario tiene los permisos requeridos
 * Redirige al dashboard si no tiene permisos
 */
function verificarPermisos($roles_permitidos) {
    $rol_actual = $_SESSION['usuario_rol'] ?? 'vendedor';
    
    if (!in_array($rol_actual, (array)$roles_permitidos)) {
        header("Location: index.php");
        exit();
    }
}

/**
 * Verifica si el usuario tiene un permiso específico
 * Retorna true o false
 */
function tienePermiso($rol_requerido) {
    $rol_actual = $_SESSION['usuario_rol'] ?? 'vendedor';
    return in_array($rol_actual, (array)$rol_requerido);
}

// Constantes con los roles permitidos para cada nivel
define('ROLES_SUPERADMIN', ['superadmin']);
define('ROLES_ADMIN', ['admin', 'superadmin']);
define('ROLES_VENDEDOR', ['vendedor', 'admin', 'superadmin']);
define('ROLES_TODOS', ['vendedor', 'admin', 'superadmin']);

/**
 * Obtiene el rol actual del usuario
 */
function obtenerRolActual() {
    return $_SESSION['usuario_rol'] ?? 'vendedor';
}

/**
 * Obtiene el nombre del usuario actual
 */
function obtenerNombreUsuario() {
    return $_SESSION['usuario_nombre'] ?? 'Usuario';
}