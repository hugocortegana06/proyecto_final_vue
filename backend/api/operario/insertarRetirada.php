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
$idvehiculos      = $data['idvehiculos']      ?? null;
$nombre           = $data['nombre']           ?? null;
$nif              = $data['nif']              ?? null;
$domicilio        = $data['domicilio']        ?? null;
$poblacion        = $data['poblacion']        ?? null;
$provincia        = $data['provincia']        ?? null;
$permiso          = $data['permiso']          ?? null;
$fecha            = $data['fecha']            ?? null;
$agente           = $data['agente']           ?? null;
$importeretirada  = $data['importeretirada']  ?? null;
$importedeposito  = $data['importedeposito']  ?? null;
$total            = $data['total']            ?? null;
$opcionespago     = $data['opcionespago']     ?? null;

// Verificar que los datos obligatorios NO sean nulos
if (
    $idvehiculos      === null ||
    $nombre           === null ||
    $nif              === null ||
    $domicilio        === null ||
    $poblacion        === null ||
    $provincia        === null ||
    $permiso          === null ||
    $fecha            === null ||
    $agente           === null ||
    $importeretirada  === null ||
    $importedeposito  === null ||
    $total            === null ||
    $opcionespago     === null
) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios"]);
    exit;
}

// Preparar la consulta para insertar la retirada
$stmt = $conn->prepare("INSERT INTO retiradas (idvehiculos, nombre, nif, domicilio, poblacion, provincia, permiso, fecha, agente, importeretirada, importedeposito, total, opcionespago) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssssssddds", $idvehiculos, $nombre, $nif, $domicilio, $poblacion, $provincia, $permiso, $fecha, $agente, $importeretirada, $importedeposito, $total, $opcionespago);

// Ejecutar la inserción
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Retirada agregada correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al agregar la retirada", "error" => $stmt->error]);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
