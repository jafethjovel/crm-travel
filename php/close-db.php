<?php
// Cerrar conexión a la base de datos si está abierta
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>