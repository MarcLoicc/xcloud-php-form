<?php
require_once 'db.php';

echo "<h2>🔧 Setup de Histórico 2026</h2>";

$createTable2026 = "
    CREATE TABLE IF NOT EXISTS ga4_history_2026 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_path VARCHAR(255) NOT NULL,
        period_type ENUM('day', 'week', 'month', 'year') NOT NULL,
        period_num INT NOT NULL,
        period_label VARCHAR(50),
        sessions INT DEFAULT 0,
        web_views INT DEFAULT 0,
        mobile_views INT DEFAULT 0,
        avg_retention FLOAT DEFAULT 0,
        UNIQUE KEY u_path_period_2026 (page_path, period_type, period_num)
    )
";

if ($conn->query($createTable2026)) {
    echo "✅ Tabla 'ga4_history_2026' lista.<br>";
} else {
    die("❌ Error creando tabla 2026: " . $conn->error);
}
