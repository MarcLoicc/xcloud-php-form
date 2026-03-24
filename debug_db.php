<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

$resCurrent = $conn->query("SELECT COUNT(*) as total FROM leads");
$total = $resCurrent ? $resCurrent->fetch_assoc()['total'] : 0;

$leadsRaw = [];
$resLeads = $conn->query("SELECT id, name, created_at, source, status FROM leads ORDER BY created_at DESC");
if($resLeads) {
    while($l = $resLeads->fetch_assoc()) {
        $leadsRaw[] = $l;
    }
}

echo json_encode([
    'db_total' => $total,
    'raw_data' => $leadsRaw,
    'server_time' => date('Y-m-d H:i:s')
]);
?>
