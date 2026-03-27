<?php
require_once 'db.php';

echo "<h3>Listando paths únicos en ga4_history_2025</h3>";
$res = $conn->query("SELECT DISTINCT page_path FROM ga4_history_2025 WHERE page_path LIKE '%declaracion%'");
while($r = $res->fetch_assoc()) {
    echo "- '{$r['page_path']}' (Longitud: " . strlen($r['page_path']) . ")<br>";
}
echo "<h3>Listando paths únicos en ga4_products</h3>";
$res = $conn->query("SELECT DISTINCT page_path FROM ga4_products WHERE page_path LIKE '%declaracion%'");
while($r = $res->fetch_assoc()) {
    echo "- '{$r['page_path']}' (Longitud: " . strlen($r['page_path']) . ")<br>";
}
