<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

verificarAutenticacion();
verificarPermisos(ROLES_VENDEDOR);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $cotizacion_id = $input['id'] ?? 0;
    $estado = $input['estado'] ?? '';
    
    if ($cotizacion_id && in_array($estado, ['pendiente', 'aprobada', 'rechazada', 'vencida'])) {
        $stmt = $conn->prepare("UPDATE cotizaciones SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $cotizacion_id);
        
        if ($stmt->execute()) {
            // Verificar si realmente se actualizó
            $check_stmt = $conn->prepare("SELECT estado FROM cotizaciones WHERE id = ?");
            $check_stmt->bind_param("i", $cotizacion_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result()->fetch_assoc();
            error_log("Estado después de actualizar: " . $result['estado']);
            
            echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
        } else {
            error_log("Error en execute: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>