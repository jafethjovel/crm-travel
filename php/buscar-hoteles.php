<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

verificarAutenticacion();
verificarPermisos(ROLES_VENDEDOR);

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$limit = 10;

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

// Buscar hoteles que coincidan con la query
$sql = "SELECT h.*, 
               (SELECT MIN(precio_noche) FROM habitaciones WHERE hotel_id = h.id) as precio_desde
        FROM hoteles h 
        WHERE (h.nombre LIKE ? OR h.ciudad LIKE ? OR h.pais LIKE ?)
        AND h.activo = 1
        ORDER BY h.nombre 
        LIMIT ?";

$search_term = "%$query%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $search_term, $search_term, $search_term, $limit);
$stmt->execute();
$result = $stmt->get_result();

$hoteles = [];
while ($row = $result->fetch_assoc()) {
    $hoteles[] = [
        'id' => $row['id'],
        'tipo' => 'hotel',
        'nombre' => $row['nombre'],
        'descripcion' => $row['nombre'] . ' - ' . $row['ciudad'] . ', ' . $row['pais'],
        'precio' => $row['precio_desde'] ?? 0,
        'moneda' => $row['moneda'],
        'detalles' => [
            'ciudad' => $row['ciudad'],
            'pais' => $row['pais'],
            'categoria' => $row['categoria'],
            'check_in' => $row['check_in'],
            'check_out' => $row['check_out']
        ]
    ];
}

echo json_encode($hoteles);
?>