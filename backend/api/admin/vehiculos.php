<?php
header('Content-Type: application/json');
include '../../db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, matricula, fechaentrada, estado FROM vehiculos");
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($vehiculos ? $vehiculos : []);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['matricula'], $data['fechaentrada'], $data['estado'])) {
        $stmt = $pdo->prepare("INSERT INTO vehiculos (matricula, fechaentrada, estado) VALUES (?, ?, ?)");
        $stmt->execute([$data['matricula'], $data['fechaentrada'], $data['estado']]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    }
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['id'])) {
        $stmt = $pdo->prepare("DELETE FROM vehiculos WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    }
}
?>
