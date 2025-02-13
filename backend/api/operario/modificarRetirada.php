<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "proyecto_final_vue";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Campos recibidos (ajusta según tus columnas)
$idvehiculos     = $data['idvehiculos']     ?? null;
$nombre          = $data['nombre']          ?? null;
$nif             = $data['nif']             ?? null;
$domicilio       = $data['domicilio']       ?? null;
$poblacion       = $data['poblacion']       ?? null;
$provincia       = $data['provincia']       ?? null;
$permiso         = $data['permiso']         ?? null;
$fecha           = $data['fecha']           ?? null;
$agente          = $data['agente']          ?? null;
$importeretirada = $data['importeretirada'] ?? null;
$importedeposito = $data['importedeposito'] ?? null;
$total           = $data['total']           ?? null;
$opcionespago    = $data['opcionespago']    ?? null;

// Verificación mínima
if (!$idvehiculos) {
    echo json_encode(["success" => false, "message" => "idvehiculos no proporcionado"]);
    exit;
}

// Verificar si la retirada existe
$stmt_check = $conn->prepare("SELECT COUNT(*) FROM retiradas WHERE idvehiculos = ?");
$stmt_check->bind_param("s", $idvehiculos);
$stmt_check->execute();
$stmt_check->bind_result($count);
$stmt_check->fetch();
$stmt_check->close();

if ($count == 0) {
    echo json_encode(["success" => false, "message" => "No existe retirada con ese idvehiculos"]);
    exit;
}

// Actualizar
$stmt = $conn->prepare("UPDATE retiradas
    SET
      nombre=?,
      nif=?,
      domicilio=?,
      poblacion=?,
      provincia=?,
      permiso=?,
      fecha=?,
      agente=?,
      importeretirada=?,
      importedeposito=?,
      total=?,
      opcionespago=?
    WHERE idvehiculos = ?");

// Tipos: 8 strings, 3 doubles, 1 string, 1 string => total 13
// ssssssss ddd s s  -> "ssssssssdddss"
$stmt->bind_param("ssssssssdddss",
    $nombre,
    $nif,
    $domicilio,
    $poblacion,
    $provincia,
    $permiso,
    $fecha,
    $agente,
    $importeretirada,  // d
    $importedeposito,  // d
    $total,            // d
    $opcionespago,     // s
    $idvehiculos       // s
);

$ok = $stmt->execute();
$stmt->close();
$conn->close();

if ($ok) {
    echo json_encode(["success" => true, "message" => "Retirada modificada correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al modificar la retirada"]);
}
