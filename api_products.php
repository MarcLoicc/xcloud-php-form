<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

function json_ok($data = []) { echo json_encode(['status' => 'success'] + $data); exit; }
function json_err($msg)      { echo json_encode(['status' => 'error', 'message' => $msg]); exit; }

$method = $_SERVER['REQUEST_METHOD'];

// GET: listar productos
if ($method === 'GET') {
    $res = $conn->query("SELECT id, page_path, name, active, has_2025_history, created_at FROM ga4_products ORDER BY name ASC");
    $products = [];
    while ($r = $res->fetch_assoc()) $products[] = $r;
    json_ok(['data' => $products]);
}

// POST: añadir producto
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $path  = trim($body['page_path'] ?? '');
    $name  = trim($body['name'] ?? '');
    $hist  = isset($body['has_2025_history']) ? (int)$body['has_2025_history'] : 0;

    if (!$path || !$name) json_err('Faltan campos obligatorios (page_path, name).');

    // Normalizar path: debe empezar y terminar con /
    if (substr($path, 0, 1) !== '/') $path = '/' . $path;
    if (substr($path, -1) !== '/')   $path = $path . '/';

    $stmt = $conn->prepare("INSERT INTO ga4_products (page_path, name, has_2025_history) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $path, $name, $hist);
    if (!$stmt->execute()) {
        if ($conn->errno === 1062) json_err('Ya existe un producto con esa URL.');
        json_err('Error al guardar: ' . $conn->error);
    }
    $id = $conn->insert_id;
    json_ok(['id' => $id, 'page_path' => $path, 'name' => $name]);
}

// DELETE: eliminar producto
if ($method === 'DELETE') {
    $body = json_decode(file_get_contents('php://input'), true);
    $id = (int)($body['id'] ?? 0);
    if (!$id) json_err('ID inválido.');

    $stmt = $conn->prepare("DELETE FROM ga4_products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    json_ok(['deleted' => $conn->affected_rows]);
}

// PATCH: activar/desactivar
if ($method === 'PATCH') {
    $body   = json_decode(file_get_contents('php://input'), true);
    $id     = (int)($body['id'] ?? 0);
    $active = (int)($body['active'] ?? 1);
    if (!$id) json_err('ID inválido.');

    $stmt = $conn->prepare("UPDATE ga4_products SET active = ? WHERE id = ?");
    $stmt->bind_param('ii', $active, $id);
    $stmt->execute();
    json_ok();
}

json_err('Método no soportado.');
