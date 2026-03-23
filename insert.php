<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

// Verificación de autenticación (evita inyecciones directas)
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

// Ya no usamos json_decode porque ahora enviamos FormData ($_POST y $_FILES)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $source = $_POST['source'] ?? 'organico';
    $proposal_price = floatval($_POST['proposal_price'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    
    // Tratamiento de etiquetas (vienen como array en $_POST)
    $tags_arr = $_POST['tags'] ?? [];
    $tags = is_array($tags_arr) ? implode(', ', $tags_arr) : $tags_arr;

    // --- MANEJO DE ARCHIVO ---
    $lead_name_clean = preg_replace('/[^A-Za-z0-9\-]/', '-', $name); // Sanitizar nombre para archivos
    $timestamp = date('Y-m-d_Hi');
    
    $file_path = null;
    if (isset($_FILES['lead_file']) && $_FILES['lead_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_tmp = $_FILES['lead_file']['tmp_name'];
        $extension = pathinfo($_FILES['lead_file']['name'], PATHINFO_EXTENSION);
        $new_file_name = "DOC_{$lead_name_clean}_{$timestamp}.{$extension}";
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $file_path = $target_file;
        }
    }

    // --- MANEJO DE GRABACIÓN DE AUDIO ---
    $audio_path = null;
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $audio_tmp = $_FILES['audio_file']['tmp_name'];
        $new_audio_name = "CALL_{$lead_name_clean}_{$timestamp}.webm";
        $target_audio = $upload_dir . $new_audio_name;

        if (move_uploaded_file($audio_tmp, $target_audio)) {
            $audio_path = $target_audio;
        }
    }


    if (empty($name) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Nombre y Email son obligatorios']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Email no válido']);
        exit;
    }

    // Insertar con Prepared Statements (11 campos)
    $stmt = $conn->prepare("INSERT INTO leads (name, email, phone, company, website, source, tags, proposal_price, file_path, audio_path, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssdsss", $name, $email, $phone, $company, $website, $source, $tags, $proposal_price, $file_path, $audio_path, $message);



    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => "¡Lead ($name) guardado correctamente en xCloud!"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Error al guardar: " . $conn->error]);
    }

    $stmt->close();
}

$conn->close();
?>
