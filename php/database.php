<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "civiturtravel";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8");

// Función para limpiar datos de entrada
function limpiar_datos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>