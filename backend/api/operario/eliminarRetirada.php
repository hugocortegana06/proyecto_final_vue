<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "proyecto_final_vue";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit;
}

// Lee el JSON
$data = json_decode(file_get_contents("php://input"), true);
$idvehiculos = $data['idvehiculos'] ?? null;

if (!$idvehiculos) {
    echo json_encode(["success" => false, "message" => "idvehiculos no proporcionado"]);
    exit;
}

// Elimina la fila de la tabla `retiradas`
$stmt = $conn->prepare("DELETE FROM retiradas WHERE idvehiculos = ?");
$stmt->bind_param("s", $idvehiculos);
$success = $stmt->execute();
$stmt->close();
$conn->close();

if ($success) {
    echo json_encode(["success" => true, "message" => "Retirada eliminada correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al eliminar la retirada."]);
}
