<?php
require_once 'auth.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protección CSRF Extrema
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Falsificación de petición detectada (CSRF)']);
        exit;
    }
    
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
        exit;
    }

    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $company = $_POST['company'] ?? '';
    $website = $_POST['website'] ?? '';
    $source = $_POST['source'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $proposal_price = (float)($_POST['proposal_price'] ?? 0);
    $message = $_POST['message'] ?? '';

    $stmt = $conn->prepare("UPDATE leads SET name=?, email=?, phone=?, company=?, website=?, source=?, tags=?, proposal_price=?, message=? WHERE id=?");
    $stmt->bind_param("sssssssdsi", $name, $email, $phone, $company, $website, $source, $tags, $proposal_price, $message, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
