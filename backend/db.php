<?php
// backend/db.php

$host = 'localhost';
$db   = 'proyecto_final_vue';
$user = 'root';       // Reemplaza con tu usuario de MySQL
$pass = '';    // Reemplaza con tu contraseña de MySQL
$charset = 'utf8mb4';


/*
$host = 'localhost';
$db   = 'hugomuniz';
$user = 'hugomuniz';       // Reemplaza con tu usuario de MySQL
$pass = 'm0hqP8&30';    // Reemplaza con tu contraseña de MySQL
$charset = 'utf8mb4';
*/


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Modo de errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Modo de fetch
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Deshabilitar emulación
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexion a la base de datos: ' . $e->getMessage()]);
    exit;
}
?>
