<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

// Verificar permisos
verificarAutenticacion();
verificarPermisos(ROLES_ADMIN);

// Obtener parámetros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
$tipo_exportacion = $_GET['tipo'] ?? 'excel';

// Obtener datos para exportar
$datos = [];
if ($stmt = $conn->prepare("
    SELECT c.codigo, c.fecha_emision, c.servicio, c.total, c.estado,
           cl.nombre as cliente, cl.email, cl.telefono,
           u.nombre as vendedor
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.cliente_id = cl.id
    LEFT JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.fecha_emision BETWEEN ? AND ?
    ORDER BY c.fecha_emision DESC
")) {
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    $stmt->close();
}

// Configurar headers según el tipo de exportación
if ($tipo_exportacion === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="reporte-ventas-' . date('Y-m-d') . '.xls"');
    
    echo "<table border='1'>";
    echo "<tr>
            <th>Código</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Servicio</th>
            <th>Vendedor</th>
            <th>Estado</th>
            <th>Monto</th>
          </tr>";
    
    foreach ($datos as $fila) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($fila['codigo']) . "</td>";
        echo "<td>" . $fila['fecha_emision'] . "</td>";
        echo "<td>" . htmlspecialchars($fila['cliente']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['email']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['telefono']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['servicio']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['vendedor']) . "</td>";
        echo "<td>" . ucfirst($fila['estado']) . "</td>";
        echo "<td>$" . number_format($fila['total'], 2) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} elseif ($tipo_exportacion === 'pdf') {
    // Aquí iría la generación de PDF (requeriría una librería como TCPDF o Dompdf)
    echo "Exportación PDF aún no implementada";
}

$conn->close();
?>