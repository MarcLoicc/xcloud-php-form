<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db.php';

// Desactivar errores visibles de PHP que rompen el JSON
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $field = $_POST['field'] ?? null;
    $value = $_POST['value'] ?? null;

    // Campos permitidos para edición rápida
    $allowedFields = ['status', 'source', 'proposal_price', 'created_at', 'phone', 'name', 'email'];

    if ($id && in_array($field, $allowedFields)) {
        
        // Corrección de formato para fechas si vienen desde el input date
        if ($field === 'created_at' && strlen($value) === 10) {
            $value .= " " . date('H:i:s');
        }

        $stmt = $conn->prepare("UPDATE leads SET $field = ? WHERE id = ?");
        $stmt->bind_param("si", $value, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
$conn->close();
