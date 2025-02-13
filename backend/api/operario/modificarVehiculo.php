<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Conexión a la BD
$servername = "localhost";
$username = "root";
$password = "";
$database = "proyecto_final_vue";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error en la conexión a la base de datos"]);
    exit;
}

// Obtenemos el JSON del body
$data = json_decode(file_get_contents("php://input"), true);

$id             = $data['id']             ?? null;
$fechaentrada   = $data['fechaentrada']   ?? null;
$fechasalida    = $data['fechasalida']    ?? null;
$lugar          = $data['lugar']          ?? null;
$direccion      = $data['direccion']      ?? null;
$agente         = $data['agente']         ?? null;
$matricula      = $data['matricula']      ?? null;
$marca          = $data['marca']          ?? null;
$modelo         = $data['modelo']         ?? null;
$color          = $data['color']          ?? null;
$motivo         = $data['motivo']         ?? null;
$tipovehiculo   = $data['tipovehiculo']   ?? null;
$grua           = $data['grua']           ?? null;
$estado         = $data['estado']         ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID no proporcionado"]);
    exit;
}

// Verificar si el vehículo existe
$stmt_check = $conn->prepare("SELECT COUNT(*) FROM vehiculos WHERE id = ?");
$stmt_check->bind_param("s", $id);
$stmt_check->execute();
$stmt_check->bind_result($count);
$stmt_check->fetch();
$stmt_check->close();

if ($count == 0) {
    echo json_encode(["success" => false, "message" => "El vehículo con ese ID no existe"]);
    exit;
}

// Actualizar con todos los campos
$stmt = $conn->prepare("UPDATE vehiculos
    SET 
        fechaentrada=?,
        fechasalida=?,
        lugar=?,
        direccion=?,
        agente=?,
        matricula=?,
        marca=?,
        modelo=?,
        color=?,
        motivo=?,
        tipovehiculo=?,
        grua=?,
        estado=?
    WHERE id = ?");

$stmt->bind_param("ssssssssssssss", 
    $fechaentrada,
    $fechasalida,
    $lugar,
    $direccion,
    $agente,
    $matricula,
    $marca,
    $modelo,
    $color,
    $motivo,
    $tipovehiculo,
    $grua,
    $estado,
    $id
);

$success = $stmt->execute();
$stmt->close();
$conn->close();

if ($success) {
    echo json_encode(["success" => true, "message" => "Vehículo modificado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al modificar el vehículo"]);
}
