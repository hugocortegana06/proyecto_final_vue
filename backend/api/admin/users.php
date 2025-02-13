<?php
header('Content-Type: application/json');
include '../../db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': // Obtener lista de usuarios
        $stmt = $pdo->query("SELECT id, username, role FROM users");
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST': // Crear usuario
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['username'], $data['password'], $data['role'])) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            exit;
        }

        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        if ($stmt->execute([$data['username'], $data['password'], $data['role']])) {
            echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear usuario']);
        }
        break;

    case 'PUT': // Modificar usuario
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['id'], $data['username'], $data['role'])) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        if ($stmt->execute([$data['username'], $data['role'], $data['id']])) {
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario']);
        }
        break;

    case 'DELETE': // Eliminar usuario
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$data['id']])) {
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar usuario']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
