<?php
require_once 'db.php';

$paths = [
    '/calculadora-de-tokens-para-chat-gpt/',
    '/calculadora-de-tokens-para-chat-gpt',
    '/mejores-herramientas-de-accesibilidad-web/',
    '/mejores-herramientas-de-accesibilidad-web',
    '/modelo-declaracion-jurada/',
    '/modelo-declaracion-jurada'
];

echo "<h3>Borrando registros de prueba</h3>";

foreach ($paths as $p) {
    echo "Borrando: $p... ";
    
    // 1. ga4_products
    $stmt1 = $conn->prepare("DELETE FROM ga4_products WHERE page_path = ?");
    $stmt1->bind_param('s', $p);
    $stmt1->execute();
    $prod_deleted = $conn->affected_rows;

    // 2. ga4_history_2025
    $stmt2 = $conn->prepare("DELETE FROM ga4_history_2025 WHERE page_path = ?");
    $stmt2->bind_param('s', $p);
    $stmt2->execute();
    $h25_deleted = $conn->affected_rows;

    // 3. ga4_history_2026
    $stmt3 = $conn->prepare("DELETE FROM ga4_history_2026 WHERE page_path = ?");
    $stmt3->bind_param('s', $p);
    $stmt3->execute();
    $h26_deleted = $conn->affected_rows;

    echo "OK (Prod: $prod_deleted, H25: $h25_deleted, H26: $h26_deleted)<br>";
}
echo "<h3>Limpieza completada.</h3>";
