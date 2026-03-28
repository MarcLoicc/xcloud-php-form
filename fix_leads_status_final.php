<?php
require_once 'db.php';
header('Content-Type: text/plain');

// 1. Ampliar columna status para soportar nombres largos (propuesta_enviada, no_responde, etc)
if ($conn->query("ALTER TABLE leads MODIFY COLUMN status VARCHAR(100) DEFAULT 'nuevo'")) {
    echo "✓ Columna 'status' ampliada con éxito.\n";
} else {
    echo "✗ Error al ampliar columna: " . $conn->error . "\n";
}

// 2. Ejecutar sincronización para aplicar mapeo de los 60 leads
echo "--- INICIANDO ACTUALIZACIÓN DE ESTADOS (MAREO ODOO) ---\n";
include 'sync_odoo.php';
echo "\n--- PROCESO COMPLETADO ---";
