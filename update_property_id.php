<?php
require_once 'env_loader.php';
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$property_id = '450593597';
$sql = "UPDATE settings SET setting_value = '$property_id' WHERE setting_key = 'ga4_property_id'";
if ($conn->query($sql)) {
    if ($conn->affected_rows > 0) {
        echo "Property ID updated to $property_id.";
    } else {
        // En caso de que no exista el registro, lo insertamos
        $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('ga4_property_id', '$property_id')");
        echo "Property ID set to $property_id.";
    }
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
