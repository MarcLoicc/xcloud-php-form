<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

$resStatus = $conn->query("SELECT status, COUNT(*) as count FROM leads GROUP BY status");
$statusCounts = [];
while($r = $resStatus->fetch_assoc()) {
    $statusCounts[$r['status']] = $r['count'];
}

$resRaw = $conn->query("SELECT id, name, status, source FROM leads WHERE LOWER(status) = 'perdido'");
$lostLeads = [];
while($r = $resRaw->fetch_assoc()) {
    $lostLeads[] = $r;
}

echo json_encode([
    'db_status_counts' => $statusCounts,
    'lost_leads_found' => $lostLeads,
    'server_time' => date('Y-m-d H:i:s')
]);
?>
