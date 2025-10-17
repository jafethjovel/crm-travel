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

// Buscar vuelos que coincidan con la query
$sql = "SELECT * FROM vuelos 
        WHERE (origen LIKE ? OR destino LIKE ? OR aerolinea LIKE ? OR numero_vuelo LIKE ?)
        ORDER BY fecha_salida DESC 
        LIMIT ?";

$search_term = "%$query%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $search_term, $search_term, $search_term, $search_term, $limit);
$stmt->execute();
$result = $stmt->get_result();

$vuelos = [];
while ($row = $result->fetch_assoc()) {
    $vuelos[] = [
        'id' => $row['id'],
        'tipo' => 'aereo',
        'codigo' => $row['numero_vuelo'],
        'descripcion' => $row['aerolinea'] . ' - ' . $row['origen'] . ' a ' . $row['destino'],
        'precio' => $row['precio'],
        'moneda' => 'USD',
        'detalles' => [
            'origen' => $row['origen'],
            'destino' => $row['destino'], 
            'aerolinea' => $row['aerolinea'],
            'fecha_salida' => $row['fecha_salida'] . ' ' . ($row['hora_salida'] ?? ''),
            'fecha_regreso' => $row['fecha_regreso'] . ' ' . ($row['hora_llegada'] ?? ''),
            'clase' => $row['clase'] ?? 'economica' // ← Lee de 'class' pero devuelve como 'clase'
        ]
    ];
}

echo json_encode($vuelos);

// Agrega esto temporalmente después del while loop
if (empty($vuelos)) {
    error_log("No se encontraron vuelos. Query: " . $query);
    error_log("SQL: " . $sql);
    error_log("Search term: " . $search_term);
}
?>