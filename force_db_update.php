<?php
require_once 'env_loader.php';
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "ALTER TABLE leads ADD COLUMN IF NOT EXISTS category ENUM('coche', 'hogar', 'salud', 'moto', 'viajes', 'mascotas') DEFAULT NULL AFTER source";
if ($conn->query($sql)) {
    echo "Column category added or already exists.";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
