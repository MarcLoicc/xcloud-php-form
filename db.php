<?php
require_once 'env_loader.php';

// Cargamos de forma segura desde el .env
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

// CREACIÓN DE TABLA Y COLUMNAS EXTENDIDAS
$tableQuery = "CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    company VARCHAR(255),
    website VARCHAR(255),
    source ENUM('organico', 'pago') DEFAULT 'organico',
    tags VARCHAR(255),
    proposal_price DECIMAL(10, 2) DEFAULT 0.00,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableQuery);

// Script de "migración" rápida por si la tabla ya existía sin estas columnas
$columns = [
    'company' => "VARCHAR(255)",
    'website' => "VARCHAR(255)",
    'source' => "ENUM('organico', 'pago') DEFAULT 'organico'",
    'tags' => "VARCHAR(255)",
    'proposal_price' => "DECIMAL(10, 2) DEFAULT 0.00"
];

foreach ($columns as $col => $type) {
    $check = $conn->query("SHOW COLUMNS FROM leads LIKE '$col'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE leads ADD COLUMN $col $type");
    }
}
?>
