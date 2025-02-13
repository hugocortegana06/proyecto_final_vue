<?php
header('Content-Type: application/json');
include '../../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

$username = trim($data['username']);
$password = trim($data['password']);

// Validación de longitud de usuario y contraseña
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

// Verificar si el usuario existe en la base de datos
$stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuario no registrado']);
    exit;
}

// Comparar contraseñas sin hash (Nota: No recomendado en producción)
if ($user['password'] !== $password) {
    echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
    exit;
}

// Registrar log del inicio de sesión
$log_stmt = $pdo->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, 'Inicio de sesión')");
$log_stmt->execute([$user['id'], $username]);

// Responder con éxito y rol del usuario
echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'username' => $username,
        'role' => $user['role']
    ]
]);
?>
