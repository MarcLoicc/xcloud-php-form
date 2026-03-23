<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

// Verificación de autenticación (evita inyecciones directas)
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $company = trim($data['company'] ?? '');
    $website = trim($data['website'] ?? '');
    $source = $data['source'] ?? 'organico';
    $proposal_price = floatval($data['proposal_price'] ?? 0);
    $message = trim($data['message'] ?? '');
    
    // Tratamiento de etiquetas (Convertir array de checkboxes a string)
    $tags_arr = $data['tags'] ?? [];
    $tags = is_array($tags_arr) ? implode(', ', $tags_arr) : $tags_arr;

    if (empty($name) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Nombre y Email son obligatorios']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Email no válido']);
        exit;
    }

    // Insertar con Prepared Statements (Seguridad Máxima)
    $stmt = $conn->prepare("INSERT INTO leads (name, email, phone, company, website, source, tags, proposal_price, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssdss", $name, $email, $phone, $company, $website, $source, $tags, $proposal_price, $message);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => "¡Lead ($name) guardado correctamente en xCloud!"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Error al guardar: " . $conn->error]);
    }

    $stmt->close();
}

$conn->close();
?>
