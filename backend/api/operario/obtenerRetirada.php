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

// idvehiculos por GET o POST
$idvehiculos = $_GET['id'] ?? null;  // "id" = "idvehiculos"
if (!$idvehiculos) {
    echo json_encode(["success" => false, "message" => "idvehiculos no proporcionado"]);
    exit;
}

$stmt = $conn->prepare("SELECT
    idvehiculos,
    nombre,
    nif,
    domicilio,
    poblacion,
    provincia,
    permiso,
    fecha,
    agente,
    importeretirada,
    importedeposito,
    total,
    opcionespago
  FROM retiradas
  WHERE idvehiculos = ?");
$stmt->bind_param("s", $idvehiculos);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $retirada = $result->fetch_assoc();
    echo json_encode(["success" => true, "retirada" => $retirada]);
} else {
    echo json_encode(["success" => false, "message" => "Retirada no encontrada"]);
}

$stmt->close();
$conn->close();
