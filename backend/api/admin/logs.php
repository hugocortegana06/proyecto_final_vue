<?php
// logs.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "proyecto_final_vue";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Obtener parámetro 'type'
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'sesion') {
    $sql = "SELECT id, accion, usuario, timestamp, detalles
            FROM logs
            WHERE tipo = 'sesion'
            ORDER BY timestamp DESC";
} else if ($type === 'accion') {
    $sql = "SELECT id, accion, usuario, timestamp, detalles
            FROM logs
            WHERE tipo = 'accion'
            ORDER BY timestamp DESC";
} else {
    // Si no se envía un type válido, devolvemos un array vacío
    echo json_encode([]);
    exit;
}

$result = $conn->query($sql);
if (!$result) {
    echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conn->error]);
    exit;
}

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode($logs);
$conn->close();
?>
