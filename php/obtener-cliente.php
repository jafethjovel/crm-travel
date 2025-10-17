<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

verificarAutenticacion();
verificarPermisos(ROLES_VENDEDOR);

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $cliente_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT id, nombre, email, telefono, numero_documento FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($cliente = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'cliente' => $cliente]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
}
?>