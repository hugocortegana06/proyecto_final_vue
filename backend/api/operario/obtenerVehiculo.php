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
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit;
}

// Obtener el ID desde la petición GET o POST
$id = $_GET['id'] ?? null; // o $_POST, depende de cómo lo envíes

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID no proporcionado"]);
    exit;
}

$stmt = $conn->prepare("SELECT 
    id,
    fechaentrada,
    fechasalida,
    lugar,
    direccion,
    agente,
    matricula,
    marca,
    modelo,
    color,
    motivo,
    tipovehiculo,
    grua,
    estado
  FROM vehiculos
  WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $vehiculo = $result->fetch_assoc();
    echo json_encode(["success" => true, "vehiculo" => $vehiculo]);
} else {
    echo json_encode(["success" => false, "message" => "Vehículo no encontrado"]);
}

$stmt->close();
$conn->close();
