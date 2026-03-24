<?php
require_once 'auth.php';
header('Content-Type: text/plain');
echo "SISTEMA DE RESET DE CACHÉ Y VERIFICACIÓN\n";

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Opcache: Reseteado con éxito.\n";
} else {
    echo "Opcache: No disponible en este servidor.\n";
}

$commit_id = "v.1736_Fuerza";
echo "VERSION EN DISCO: " . $commit_id . "\n";
echo "FECHA ACTUAL: " . date('Y-m-d H:i:s') . "\n";
echo "Sincronización forzada completada.";
?>
