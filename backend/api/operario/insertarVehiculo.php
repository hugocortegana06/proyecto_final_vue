<?php
// Configuración de error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir tipo de contenido JSON para la respuesta
header('Content-Type: application/json');

// Parámetros de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "proyecto_final_vue";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar si hay error en la conexión
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit;
}

// Obtener los datos enviados por el cliente
$data = json_decode(file_get_contents("php://input"), true);

// Extraer los datos del JSON recibido
$id = $data['id'] ?? null;
$fechaentrada = $data['fechaentrada'] ?? null;
$fechasalida = $data['fechasalida'] ?? null;
$lugar = $data['lugar'] ?? null;
$direccion = $data['direccion'] ?? null;
$agente = $data['agente'] ?? null;
$matricula = $data['matricula'] ?? null;
$marca = $data['marca'] ?? null;
$modelo = $data['modelo'] ?? null;
$color = $data['color'] ?? null;
$motivo = $data['motivo'] ?? null;
$tipovehiculo = $data['tipovehiculo'] ?? null;
$grua = $data['grua'] ?? null;
$estado = $data['estado'] ?? null;

// Verificar si los datos obligatorios están presentes
if (!$fechaentrada || !$fechasalida || !$lugar || !$direccion || !$agente || !$matricula || !$marca || !$modelo || !$tipovehiculo || !$grua || !$estado) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios"]);
    exit;
}

// Si el ID está presente (no autoincremental), lo usamos
if ($id) {
    $stmt = $conn->prepare("INSERT INTO vehiculos (id, fechaentrada, fechasalida, lugar, direccion, agente, matricula, marca, modelo, color, motivo, tipovehiculo, grua, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssssss", $id, $fechaentrada, $fechasalida, $lugar, $direccion, $agente, $matricula, $marca, $modelo, $color, $motivo, $tipovehiculo, $grua, $estado);
} else {
    // Si el ID no está presente, dejamos que la base de datos lo genere automáticamente (autoincremental)
    $stmt = $conn->prepare("INSERT INTO vehiculos (fechaentrada, fechasalida, lugar, direccion, agente, matricula, marca, modelo, color, motivo, tipovehiculo, grua, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $fechaentrada, $fechasalida, $lugar, $direccion, $agente, $matricula, $marca, $modelo, $color, $motivo, $tipovehiculo, $grua, $estado);
}

// Ejecutar la inserción
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Vehículo agregado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al agregar el vehículo"]);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
