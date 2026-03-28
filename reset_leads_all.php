<?php
require_once 'db.php';
header('Content-Type: text/plain');

echo "--- LIMPIEZA CRM (LEADS) ---\n";

// 1. Vaciar tabla de leads
if ($conn->query("TRUNCATE TABLE leads")) {
    echo "✓ Tabla 'leads' vaciada con éxito.\n";
} else {
    echo "✗ Error al vaciar tabla: " . $conn->error . "\n";
}

// 2. Limpiar archivos de audio odoo antiguos (Opcional, pero recomendado)
$uploads = __DIR__ . '/uploads/';
if (is_dir($uploads)) {
    $files = glob($uploads . 'odoo_*');
    foreach($files as $file) {
        if(is_file($file)) unlink($file);
    }
    echo "✓ Archivos de audio antiguos eliminados.\n";
}

echo "\n--- INICIANDO IMPORTACIÓN LIMPIA DESDE ODOO ---\n";
include 'sync_odoo.php';
echo "\n--- RESET COMPLETADO ---";
