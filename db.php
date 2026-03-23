<?php
// Configuración de la base de datos MariaDB para xCloud (CREDENCIALES ACTUALIZADAS s208273)
$host = '127.0.0.1';
$user = 'u208273_crm';
$pass = 'Nwpd0WGdyRO810tA';
$db   = 's208273_crm';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Fallo de conexión: " . $conn->connect_error]));
}

// Crear la tabla de leads si no existe
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
