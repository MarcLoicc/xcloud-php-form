<?php
header('Content-Type: application/json');
require_once 'db.php';

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Datos no recibidos"]);
    exit;
}

$name = $conn->real_escape_string($data['name'] ?? '');
$email = $conn->real_escape_string($data['email'] ?? '');
$phone = $conn->real_escape_string($data['phone'] ?? '');
$message = $conn->real_escape_string($data['message'] ?? '');

if (empty($name) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Nombre y email son obligatorios"]);
    exit;
}

$sql = "INSERT INTO leads (name, email, phone, message) VALUES ('$name', '$email', '$phone', '$message')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Lead registrado correctamente en xCloud"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>
