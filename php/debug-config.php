<?php
session_start();
require_once 'database.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SESSION['usuario_rol'] !== 'superadmin') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

$configuraciones = [
    'general' => obtenerConfiguraciones('general'),
    'empresa' => obtenerConfiguraciones('empresa'),
    'correo' => obtenerConfiguraciones('correo')
];

echo json_encode($configuraciones, JSON_PRETTY_PRINT);
?>