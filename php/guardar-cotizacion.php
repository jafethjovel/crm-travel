<?php
session_start();
require_once 'database.php';
require_once 'auth.php';
require_once 'cotizaciones-crud.php';

verificarAutenticacion();
verificarPermisos(ROLES_VENDEDOR);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cotizacionesCRUD = new CotizacionesCRUD($conn);
        
        // DEBUG TEMPORAL
        error_log("Servicios recibidos: " . ($_POST['servicios_json'] ?? 'NO HAY SERVICIOS'));
        
        // Determinar tipo de cliente
        $tipo_cliente = $_POST['tipo_cliente'] ?? 'nuevo';
        $cliente_id = 0;
        
        if ($tipo_cliente === 'existente' && !empty($_POST['cliente_existente_id'])) {
            // Usar cliente existente
            $cliente_id = (int)$_POST['cliente_existente_id'];
        } else {
            // Crear cliente nuevo con la información proporcionada
            $cliente_id = $cotizacionesCRUD->crearOActualizarCliente([
                'cliente_nombre' => $_POST['cliente_nombre'] ?? 'Cliente Ocasional',
                'cliente_email' => $_POST['cliente_email'] ?? '',
                'cliente_telefono' => $_POST['cliente_telefono'] ?? '',
                'cliente_documento' => $_POST['cliente_documento'] ?? 'SIN DOCUMENTO'
            ]);
        }
        
        // Preparar datos de la cotización
        $datos_cotizacion = [
            'codigo' => $_POST['codigo_cotizacion'] ?? '',
            'cliente_id' => $cliente_id,
            'vendedor_id' => $_SESSION['usuario_id'],
            'fecha_vigencia' => $_POST['fecha_validez'] ?? '',
            'moneda' => $_POST['moneda'] ?? 'USD',
            'notas' => $_POST['notas'] ?? '',
            'subtotal' => $_POST['subtotal'] ?? 0,
            'impuestos' => $_POST['impuestos'] ?? 0,
            'total' => $_POST['total'] ?? 0
        ];
        
        // Validar datos requeridos
        if (empty($datos_cotizacion['codigo'])) {
            throw new Exception('Faltan campos obligatorios');
        }
        
        // Crear la cotización
        $cotizacion_id = $cotizacionesCRUD->crearCotizacion($datos_cotizacion);
        
        if ($cotizacion_id) {
            // Guardar los servicios de la cotización
            $servicios_guardados = $cotizacionesCRUD->guardarServiciosDesdeTabla($cotizacion_id, $_POST);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Cotización guardada correctamente con ' . $servicios_guardados . ' servicios',
                'id' => $cotizacion_id
            ]);
        } else {
            throw new Exception('Error al crear la cotización');
        }
        
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