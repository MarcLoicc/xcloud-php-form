<?php
require_once 'auth.php'; // Protección de seguridad del CRM
require_once 'db.php';

$file = $_GET['file'] ?? '';

if (empty($file)) die("Error: No has indicado qué archivo quieres ver.");

// Normalizamos la ruta para evitar trucos de hackers
$file = basename($file); // Solo nos quedamos con el nombre del archivo (seguridad extra)
$filePath = 'uploads/' . $file;

if (!file_exists($filePath)) {
    // Si llegamos aquí, es que el archivo no está en el disco duro del servidor
    die("Error crítico: El archivo '$file' NO existe en el servidor. Puede que se haya borrado al hacer el despliegue de Git.");
}

// Detective de tipo de archivo (MIME Type)
$mimeType = mime_content_type($filePath);
header("Content-Type: $mimeType");
header("Content-Disposition: inline; filename=\"$file\"");
header("Content-Length: " . filesize($filePath));

// Leer y entregar el archivo
readfile($filePath);
exit;
?>
