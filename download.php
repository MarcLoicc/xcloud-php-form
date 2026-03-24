<?php
require_once 'auth.php'; // Protección de seguridad del CRM
require_once 'db.php';

$file = $_GET['file'] ?? '';

if (empty($file)) {
    die("Error: No has indicado qué archivo quieres ver. (Parámetro vacío)");
}

// Limpiamos la ruta
$fileName = basename($file); 
$filePath = 'uploads/' . $fileName;

if (!file_exists($filePath)) {
    die("Error crítico: El archivo '$fileName' NO existe en el servidor en la ruta $filePath.");
}

// Detective de tipo de archivo (MIME Type)
$mimeType = mime_content_type($filePath);
header("Content-Type: $mimeType");
header("Content-Disposition: inline; filename=\"$fileName\"");
header("Content-Length: " . filesize($filePath));

readfile($filePath);
exit;
?>
