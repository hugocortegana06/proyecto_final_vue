
<?php
/*
header('Content-Type: application/json');
include '../../db.php';

$query = "
    SELECT 
        r.Id AS id, 
        v.matricula, 
        DATE_FORMAT(r.fecha, '%Y-%m-%d') AS fecha, 
        r.nombre, 
        v.estado, 
        r.total 
    FROM retiradas r
    LEFT JOIN vehiculos v ON r.idvehiculos = v.id
    ORDER BY r.fecha DESC
";

$stmt = $pdo->query($query);
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$vehiculos) {
    echo json_encode([]);
} else {
    echo json_encode($vehiculos);
}
*/
?>


<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php'; // Ajusta la ruta si es necesario

try {
    $stmt = $pdo->prepare("SELECT idvehiculos ,nombre ,nif ,domicilio ,poblacion ,provincia ,permiso ,fecha ,agente ,importeretirada ,importedeposito ,total ,opcionespago FROM retiradas");
    $stmt->execute();
    $retiradas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($retiradas);
    
    unset($stmt); // Liberar recursos
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener los vehículos', 'message' => $e->getMessage()]);
}
?>




