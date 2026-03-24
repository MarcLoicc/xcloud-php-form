<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db.php';

// Modo diagnóstico forzado para ver el error exacto
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $status = $_POST['status'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $created_at = $_POST['created_at'] ?? date('Y-m-d H:i:s');
    $proposal_price = (float)($_POST['proposal_price'] ?? 0);
    $message = $_POST['message'] ?? '';

    // Preparar campos dinámicos para el UPDATE (RE-ACTIVADO MESSAGE)
    $fields = "name=?, email=?, phone=?, company=?, website=?, source=?, status=?, tags=?, proposal_price=?, message=?, created_at=?";
    $types = "ssssssssdss";
    $params = [$name, $email, $phone, $company, $website, $source, $status, $tags, $proposal_price, $message, $created_at];

    // Manejo de Archivo
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777, true);
    
    $lead_name_clean = preg_replace('/[^A-Za-z0-9\-]/', '-', $name);
    $timestamp = date('Y-m-d_Hi');

    if (isset($_FILES['lead_file']) && $_FILES['lead_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['lead_file']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['lead_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'docx', 'doc', 'png', 'jpg', 'jpeg', 'zip'];
        
        if (in_array($extension, $allowed)) {
            $new_file_name = "DOC_{$lead_name_clean}_{$timestamp}_{$id}.{$extension}";
            $target_file = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $target_file)) {
                $fields .= ", file_path=?";
                $types .= "s";
                $params[] = $target_file;
            }
        }
    }

    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $audio_tmp = $_FILES['audio_file']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
        // Permitir archivos de audio habituales
        $allowed_audio = ['webm', 'mp3', 'ogg', 'wav', 'm4a'];
        if (in_array($extension, $allowed_audio) || strpos($_FILES['audio_file']['type'], 'audio/') === 0) {
            $ext = in_array($extension, $allowed_audio) ? $extension : 'mp3';
            $new_audio_name = "CALL_{$lead_name_clean}_{$timestamp}_{$id}.{$ext}";
            $target_audio = $upload_dir . $new_audio_name;
            if (move_uploaded_file($audio_tmp, $target_audio)) {
                $fields .= ", audio_path=?";
                $types .= "s";
                $params[] = $target_audio;
            }
        }
    }

    // EL ID SIEMPRE DEBE IR AL FINAL DE TODO
    $types .= "i";
    $params[] = $id;

    $query = "UPDATE leads SET $fields WHERE id=?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        // Si hay error aqui, es por el SQL generado
        error_log("SQL Error: " . $conn->error . " Query: " . $query);
        echo json_encode(['success' => false, 'message' => 'Error en estructura SQL: ' . $conn->error]);
        exit;
    }
    
    // Binding dinámico
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
