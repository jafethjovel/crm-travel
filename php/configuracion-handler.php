<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

// Verificar permisos
verificarAutenticacion();
verificarPermisos(ROLES_ADMIN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seccion = $_POST['seccion'] ?? '';
    $datos = $_POST;
    
    // Aquí procesarías y guardarías las configuraciones en la base de datos
    // Este es un ejemplo básico
    
    try {
        switch ($seccion) {
            case 'general':
                // Validar y guardar configuración general
                $configuraciones = [
                    'nombre_sistema' => filter_var($datos['nombre_sistema'], FILTER_SANITIZE_STRING),
                    'moneda' => $datos['moneda'],
                    'formato_fecha' => $datos['formato_fecha'],
                    'timezone' => $datos['timezone'],
                    'idioma' => $datos['idioma']
                ];
                break;
                
            case 'empresa':
                // Validar y guardar información de la empresa
                $configuraciones = [
                    'nombre' => filter_var($datos['empresa_nombre'], FILTER_SANITIZE_STRING),
                    'nrc' => filter_var($datos['empresa_nrc'], FILTER_SANITIZE_STRING),
                    'telefono' => filter_var($datos['empresa_telefono'], FILTER_SANITIZE_STRING),
                    'email' => filter_var($datos['empresa_email'], FILTER_SANITIZE_EMAIL),
                    'direccion' => filter_var($datos['empresa_direccion'], FILTER_SANITIZE_STRING),
                    'website' => filter_var($datos['empresa_website'], FILTER_SANITIZE_URL)
                ];
                break;
                
            case 'correo':
                // Validar y guardar configuración de correo
                $configuraciones = [
                    'smtp_host' => filter_var($datos['smtp_host'], FILTER_SANITIZE_STRING),
                    'smtp_puerto' => filter_var($datos['smtp_puerto'], FILTER_SANITIZE_NUMBER_INT),
                    'smtp_seguridad' => $datos['smtp_seguridad'],
                    'smtp_usuario' => filter_var($datos['smtp_usuario'], FILTER_SANITIZE_EMAIL)
                ];
                break;
        }
        
        // En una aplicación real, guardarías estas configuraciones en la base de datos
        // Por ahora, solo retornamos éxito
        echo json_encode([
            'success' => true,
            'message' => 'Configuración actualizada correctamente'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>