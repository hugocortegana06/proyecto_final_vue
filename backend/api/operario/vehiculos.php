<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php'; // Ajusta la ruta si es necesario

try {
    $stmt = $pdo->prepare("SELECT id, fechaentrada, matricula, marca, modelo, lugar, direccion, tipovehiculo, estado FROM vehiculos");
    $stmt->execute();
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($vehiculos);
    
    unset($stmt); // Liberar recursos
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener los vehículos', 'message' => $e->getMessage()]);
}
?>
