<?php
require_once 'env_loader.php';

// Cargar credenciales desde el archivo .env de forma segura
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Asegurar codificación UTF-8 para caracteres especiales
$conn->set_charset("utf8mb4");

// Ocultar la tabla principal para mayor privacidad (si no existe)
$tableQuery = "CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableQuery);
?>
