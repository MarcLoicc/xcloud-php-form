<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

$range = $_GET['range'] ?? 'all';
$startDate = $_GET['start'] ?? null;
$endDate = $_GET['end'] ?? null;

$where = "WHERE 1=1";
if ($startDate && $endDate) {
    $where .= " AND created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
} elseif ($range !== 'all') {
    $where .= " AND created_at >= DATE_SUB(NOW(), INTERVAL $range DAY)";
}

// 1. Métricas de tarjetas desglosadas
$totalLeads = $conn->query("SELECT COUNT(*) as total FROM leads $where")->fetch_assoc()['total'];
$revenue = $conn->query("SELECT SUM(proposal_price) as total FROM leads $where AND status IN ('ganado', 'propuesta_enviada')")->fetch_assoc()['total'] ?? 0;

// Desglose Pago
$wonPago = $conn->query("SELECT COUNT(*) as total FROM leads $where AND source = 'pago' AND status = 'ganado'")->fetch_assoc()['total'];
$lostPago = $conn->query("SELECT COUNT(*) as total FROM leads $where AND source = 'pago' AND status = 'perdido'")->fetch_assoc()['total'];
$revPago = $conn->query("SELECT SUM(proposal_price) as total FROM leads $where AND source = 'pago' AND status IN ('ganado', 'propuesta_enviada')")->fetch_assoc()['total'] ?? 0;

// Desglose Orgánico
$wonOrganico = $conn->query("SELECT COUNT(*) as total FROM leads $where AND source = 'organico' AND status = 'ganado'")->fetch_assoc()['total'];
$lostOrganico = $conn->query("SELECT COUNT(*) as total FROM leads $where AND source = 'organico' AND status = 'perdido'")->fetch_assoc()['total'];
$revOrganico = $conn->query("SELECT SUM(proposal_price) as total FROM leads $where AND source = 'organico' AND status IN ('ganado', 'propuesta_enviada')")->fetch_assoc()['total'] ?? 0;

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
if($sourcesResult) {
    while($s = $sourcesResult->fetch_assoc()) {
        if(isset($sourceData[$s['source']])) $sourceData[$s['source']] = (int)$s['count'];
    }
}

$response = [
    'metrics' => [
        'totalLeads' => (int)$totalLeads,
        'revenue' => number_format((float)$revenue, 0, '.', ','),
        'pago' => [
            'total' => (int)$sourceData['pago'],
            'won' => (int)$wonPago,
            'lost' => (int)$lostPago,
            'revenue' => number_format((float)$revPago, 0, '.', ',')
        ],
        'organico' => [
            'total' => (int)$sourceData['organico'],
            'won' => (int)$wonOrganico,
            'lost' => (int)$lostOrganico,
            'revenue' => number_format((float)$revOrganico, 0, '.', ',')
        ]
    ],
    'chart' => [
        'labels' => $dates,
        'pago' => $pagoCounts,
        'organico' => $organicoCounts
    ],
    'donut' => [
        'data' => [(int)$sourceData['pago'], (int)$sourceData['organico']]
    ],
    'debug' => [
        'query' => "SELECT COUNT(*) as total FROM leads $where",
        'actual_total' => $totalLeads
    ]
];

echo json_encode($response);
$conn->close();
?>
