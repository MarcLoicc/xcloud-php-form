<?php
require_once 'db.php';

$path = '/modelo-declaracion-jurada/';

echo "<h3>Verificando $path en BD</h3>";

// 1. ga4_products
$res1 = $conn->query("SELECT * FROM ga4_products WHERE page_path = '$path'");
echo "En ga4_products: " . $res1->num_rows . " filas.<br>";

// 2. ga4_history_2025
$res2 = $conn->query("SELECT period_type, count(*) as cnt, SUM(sessions) as total FROM ga4_history_2025 WHERE page_path = '$path' GROUP BY period_type");
echo "En ga4_history_2025:<br>";
if ($res2 && $res2->num_rows > 0) {
    while ($r = $res2->fetch_assoc()) {
        echo "- {$r['period_type']}: {$r['cnt']} filas, TOTAL {$r['total']} sesiones.<br>";
    }
} else {
    echo "¡VACÍO!<br>";
}

// 3. ga4_history_2026
$res3 = $conn->query("SELECT period_type, count(*) as cnt, SUM(sessions) as total FROM ga4_history_2026 WHERE page_path = '$path' GROUP BY period_type");
echo "En ga4_history_2026:<br>";
if ($res3 && $res3->num_rows > 0) {
    while ($r = $res3->fetch_assoc()) {
        echo "- {$r['period_type']}: {$r['cnt']} filas, TOTAL {$r['total']} sesiones.<br>";
    }
} else {
    echo "¡VACÍO!<br>";
}
