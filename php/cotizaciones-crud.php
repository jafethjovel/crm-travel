<?php
require_once 'database.php';

class CotizacionesCRUD {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
public function crearCotizacion($datos) {
    $stmt = $this->conn->prepare("INSERT INTO cotizaciones 
        (codigo, cliente_id, usuario_id, fecha_vigencia, subtotal, impuestos, total, moneda, estado, notas) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $estado = 'pendiente';
    
    $stmt->bind_param("siisdddsss", 
        $datos['codigo'], 
        $datos['cliente_id'],
        $datos['vendedor_id'],
        $datos['fecha_vigencia'],
        $datos['subtotal'],
        $datos['impuestos'],
        $datos['total'],
        $datos['moneda'],
        $estado,
        $datos['notas']
    );
    
    if ($stmt->execute()) {
        return $stmt->insert_id;
    }
    return false;
}

public function crearOActualizarCliente($datos) {
    // Si no hay nombre, usar "Cliente Ocasional"
    $nombre = !empty($datos['cliente_nombre']) ? $datos['cliente_nombre'] : 'Cliente Ocasional';
    $email = $datos['cliente_email'] ?? '';
    $telefono = $datos['cliente_telefono'] ?? '';
    $documento = $datos['cliente_documento'] ?? 'SIN DOCUMENTO';
    
    // Buscar si el cliente ya existe por documento o email
    if (!empty($email) || !empty($documento)) {
        $stmt = $this->conn->prepare("SELECT id FROM clientes WHERE numero_documento = ? OR email = ?");
        $stmt->bind_param("ss", $documento, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $cliente = $result->fetch_assoc();
            return $cliente['id'];
        }
    }
    
    // Crear nuevo cliente
    $stmt = $this->conn->prepare("INSERT INTO clientes (nombre, email, telefono, numero_documento) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $telefono, $documento);
    
    if ($stmt->execute()) {
        return $stmt->insert_id;
    }
    
    // Si falla, retornar 0 (cliente por defecto) o manejar el error
    return 0;
}
    
public function agregarServicio($cotizacion_id, $servicio) {
    // Calcular el subtotal automáticamente si no viene
    $subtotal = $servicio['subtotal'] ?? (($servicio['precio'] ?? 0) * ($servicio['cantidad'] ?? 1));
    
    $stmt = $this->conn->prepare("INSERT INTO cotizacion_servicios 
        (cotizacion_id, tipo_servicio, detalles, precio, cantidad, subtotal) 
        VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("issdid", 
        $cotizacion_id, 
        $servicio['tipo_servicio'],
        $servicio['detalles'], 
        $servicio['precio'],
        $servicio['cantidad'],
        $subtotal
    );
    
    return $stmt->execute();
}
    
public function obtenerCotizacion($id) {
    $stmt = $this->conn->prepare("SELECT 
        c.*,
        cl.nombre as cliente_nombre,
        cl.email as cliente_email,
        cl.telefono as cliente_telefono,
        cl.numero_documento as cliente_documento,
        u.nombre as vendedor_nombre
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.cliente_id = cl.id
    LEFT JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.id = ?");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
    
    public function obtenerServicios($cotizacion_id) {
        $stmt = $this->conn->prepare("SELECT * FROM cotizacion_servicios WHERE cotizacion_id = ?");
        $stmt->bind_param("i", $cotizacion_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
// En la función listarCotizaciones, añade:
public function listarCotizaciones($filtros = []) {
    $query = "SELECT 
        c.*,
        cl.nombre as cliente_nombre,
        cl.email as cliente_email,
        cl.telefono as cliente_telefono,
        cl.numero_documento as cliente_documento,
        u.nombre as vendedor_nombre
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.cliente_id = cl.id
    LEFT JOIN usuarios u ON c.usuario_id = u.id
    WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if (!empty($filtros['estado'])) {
        $query .= " AND c.estado = ?";
        $params[] = $filtros['estado'];
        $types .= "s";
    }
    
    if (!empty($filtros['desde'])) {
        $query .= " AND DATE(c.fecha_creacion) >= ?";
        $params[] = $filtros['desde'];
        $types .= "s";
    }
    
    if (!empty($filtros['hasta'])) {
        $query .= " AND DATE(c.fecha_creacion) <= ?";
        $params[] = $filtros['hasta'];
        $types .= "s";
    }
    
    $query .= " ORDER BY c.fecha_creacion DESC";
    
    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        error_log("Error preparing query: " . $this->conn->error);
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Error executing query: " . $stmt->error);
        return [];
    }
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

public function guardarServiciosDesdeTabla($cotizacion_id, $post_data) {
    $servicios_guardados = 0;
    
    // Recibir los servicios como JSON desde el formulario
    if (isset($post_data['servicios_json']) && !empty($post_data['servicios_json'])) {
        $servicios = json_decode($post_data['servicios_json'], true);
        
        foreach ($servicios as $servicio) {
            $servicio_data = [
                'tipo_servicio' => $servicio['tipo'] ?? 'General',
                'detalles' => $servicio['descripcion'] . ' - ' . $servicio['detalles'],
                'precio' => $servicio['precioUnitario'],
                'cantidad' => $servicio['cantidad'],
                'subtotal' => $servicio['subtotal']
            ];
            
            if ($this->agregarServicio($cotizacion_id, $servicio_data)) {
                $servicios_guardados++;
            }
        }
    }
    
    return $servicios_guardados;
}

}
?>