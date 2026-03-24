<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

$range = $_GET['range'] ?? '7';
$startDate = $_GET['start'] ?? null;
$endDate = $_GET['end'] ?? null;

$where = "WHERE 1=1";
if ($startDate && $endDate) {
    $where .= " AND created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
} else {
    $where .= " AND created_at >= DATE_SUB(NOW(), INTERVAL $range DAY)";
}

// 1. Métricas de tarjetas
$totalLeads = $conn->query("SELECT COUNT(*) as total FROM leads $where")->fetch_assoc()['total'];
$revenue = $conn->query("SELECT SUM(proposal_price) as total FROM leads $where AND status IN ('ganado', 'propuesta_enviada')")->fetch_assoc()['total'] ?? 0;
$wonLeads = $conn->query("SELECT COUNT(*) as total FROM leads $where AND status = 'ganado'")->fetch_assoc()['total'];
$lostLeads = $conn->query("SELECT COUNT(*) as total FROM leads $where AND status = 'perdido'")->fetch_assoc()['total'];

// 2. Gráfica de Tendencia Séparada (Pago vs Orgánico)
$historyQuery = "
    SELECT DATE(created_at) as date, 
           SUM(CASE WHEN source = 'pago' THEN 1 ELSE 0 END) as pago,
           SUM(CASE WHEN source = 'organico' THEN 1 ELSE 0 END) as organico
    FROM leads 
    $where 
    GROUP BY DATE(created_at) 
    ORDER BY date ASC
";
$historyData = $conn->query($historyQuery);
$dates = []; $pagoCounts = []; $organicoCounts = [];
while($h = $historyData->fetch_assoc()) {
    $dates[] = date('d M', strtotime($h['date']));
    $pagoCounts[] = (int)$h['pago'];
    $organicoCounts[] = (int)$h['organico'];
}

// 3. Distribución por Origen
$sourceData = ['pago' => 0, 'organico' => 0];
$sourcesResult = $conn->query("SELECT source, COUNT(*) as count FROM leads $where GROUP BY source");
while($s = $sourcesResult->fetch_assoc()) {
    if(isset($sourceData[$s['source']])) $sourceData[$s['source']] = (int)$s['count'];
}

echo json_encode([
    'metrics' => [
        'totalLeads' => $totalLeads,
        'revenue' => number_format((float)$revenue, 0, '.', ','),
        'wonLeads' => $wonLeads,
        'lostLeads' => $lostLeads,
        'pago' => $sourceData['pago'],
        'organico' => $sourceData['organico']
    ],
    'chart' => [
        'labels' => $dates,
        'pago' => $pagoCounts,
        'organico' => $organicoCounts
    ],
    'donut' => [
        'data' => [$sourceData['pago'], $sourceData['organico']]
    ]
]);
$conn->close();
?>
