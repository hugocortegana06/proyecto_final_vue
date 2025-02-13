<?php
header('Content-Type: application/json');
include '../../db.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validar que los datos están presentes y no están vacíos
if (empty($data['username']) || empty($data['password']) || empty($data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

$username = trim($data['username']);
$password = trim($data['password']);
$role = trim($data['role']);

// Validación de longitud del nombre de usuario y contraseña
if (strlen($username) < 3 || strlen($username) > 10) {
    echo json_encode(['success' => false, 'message' => 'El nombre de usuario debe tener entre 3 y 10 caracteres']);
    exit;
}

if (strlen($password) < 1 || strlen($password) > 6) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener entre 1 y 6 caracteres']);
    exit;
}

// Validación de caracteres permitidos en el nombre de usuario
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    echo json_encode(['success' => false, 'message' => 'El nombre de usuario solo puede contener letras, números y guiones bajos']);
    exit;
}

// Validación del rol
if (!in_array($role, ['admin', 'operario'])) {
    echo json_encode(['success' => false, 'message' => 'Rol no válido']);
    exit;
}

// Verificar si el usuario ya existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
    exit;
}

// Insertar el usuario en la base de datos
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");

if ($stmt->execute([$username, $password, $role])) {
    $user_id = $pdo->lastInsertId();

    // Registrar log del registro
    $log_stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, 'Usuario registrado')");
    $log_stmt->execute([$user_id, $username]);

    echo json_encode(['success' => true, 'message' => 'Registro exitoso. Redirigiendo al login...']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar']);
}
?>
