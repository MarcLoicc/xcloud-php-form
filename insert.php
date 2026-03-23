<?php
session_start();
header('Content-Type: application/json');

// Verificar sesión para evitar que bots o terceros usen el endpoint
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo json_encode(["status" => "error", "message" => "Acceso no autorizado"]);
    exit;
}

require_once 'db.php';

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Datos no recibidos"]);
    exit;
}

// Sanitización básica y validación
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$message = trim($data['message'] ?? '');

if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Nombre y email válido son obligatorios"]);
    exit;
}

// --- ROBUSTEZ: USO DE PREPARED STATEMENTS ---
$stmt = $conn->prepare("INSERT INTO leads (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Lead registrado correctamente de forma segura"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error de base de datos: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
