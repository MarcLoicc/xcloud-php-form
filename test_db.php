<?php
require 'db.php';
$r = $conn->query("SELECT SUM(sessions) as t FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= 20250301 AND period_num <= 20250326");
echo "TOTAL MTD 2025: " . ($r ? $r->fetch_assoc()['t'] : 'ERROR') . "\n";

$r2 = $conn->query("SELECT page_path, SUM(sessions) as v FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= 20250301 AND period_num <= 20250326 AND page_path = '/' GROUP BY page_path");
echo "HOME MTD 2025: " . ($r2 ? $r2->fetch_assoc()['v'] : 'ERROR') . "\n";
