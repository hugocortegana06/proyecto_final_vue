<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "proyecto_final_vue";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error en la conexión a la base de datos"]);
    exit;
}

// Obtener datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID no proporcionado"]);
    exit;
}

// Eliminar solo el vehículo con el ID específico
$stmt = $conn->prepare("DELETE FROM vehiculos WHERE id = ?");
$stmt->bind_param("s", $id);  // "s" porque el ID es VARCHAR en tu tabla
$success = $stmt->execute();
$stmt->close();
$conn->close();

if ($success) {
    echo json_encode(["success" => true, "message" => "Vehículo eliminado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al eliminar el vehículo"]);
}
?>
