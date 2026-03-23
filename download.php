<?php
require_once 'auth.php'; // Solo tú puedes descargar
require_once 'db.php';

$file = $_GET['file'] ?? '';

if (empty($file)) die("Archivo no especificado.");

// Seguridad: Solo permitir leer archivos de la carpeta uploads/
// Evitar ataques de "../../../etc/passwd"
$baseDir = realpath('uploads/');
$filePath = realpath($file);

if ($filePath === false || strpos($filePath, $baseDir) !== 0) {
    die("Acceso denegado o archivo no encontrado.");
}

if (!file_exists($filePath)) {
    die("El archivo no existe físicamente en el servidor.");
}

// Detective de tipo de archivo (MIME Type)
$mimeType = mime_content_type($filePath);
header("Content-Type: $mimeType");
header("Content-Disposition: inline; filename=\"" . basename($filePath) . "\"");
header("Content-Length: " . filesize($filePath));

// Leer y entregar el archivo
readfile($filePath);
exit;
?>
