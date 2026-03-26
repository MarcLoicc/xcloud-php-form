<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

// Categorías definidas
$categories = ['coche', 'hogar', 'salud', 'moto', 'viajes', 'mascotas'];

// Función para obtener leads por categoría y periodo
function getLeadsByCategory($conn, $category, $days = 7) {
    $where = "WHERE category = '$category' AND created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)";
    $total = $conn->query("SELECT COUNT(*) as total FROM leads $where")->fetch_assoc()['total'];
    $qualified = $conn->query("SELECT COUNT(*) as total FROM leads $where AND status NOT IN ('no_cualificado', 'perdido')")->fetch_assoc()['total'];
    $won = $conn->query("SELECT COUNT(*) as total FROM leads $where AND status = 'ganado'")->fetch_assoc()['total'];
    
    return [
        'total' => (int)$total,
        'qualified' => (int)$qualified,
        'won' => (int)$won
    ];
}

// Simulamos datos de Google Analytics (Esto se reemplazará con GA4 API)
// En un escenario real, haríamos una llamada a la API de GA4 aquí.
$mockGA4Data = [
    'coche' => ['views' => 6353, 'prev_views' => 6100, 'starts' => 872, 'prev_starts' => 920],
    'hogar' => ['views' => 2076, 'prev_views' => 2200, 'starts' => 535, 'prev_starts' => 600],
    'salud' => ['views' => 2329, 'prev_views' => 2350, 'starts' => 377, 'prev_starts' => 372],
    'moto' => ['views' => 1345, 'prev_views' => 1300, 'starts' => 206, 'prev_starts' => 172],
    'viajes' => ['views' => 1056, 'prev_views' => 900, 'starts' => 589, 'prev_starts' => 608],
    'mascotas' => ['views' => 1286, 'prev_views' => 1350, 'starts' => 310, 'prev_starts' => 290]
];

$results = [];

foreach ($categories as $cat) {
    $leads = getLeadsByCategory($conn, $cat, 7);
    $prevLeads = getLeadsByCategory($conn, $cat, 14); // Esto es simplificado para el ejemplo
    
    $ga = $mockGA4Data[$cat];
    
    // Cálculos de ratios (siguiendo la lógica del Excel)
    $ratioTarificacion = $ga['views'] > 0 ? ($leads['total'] / $ga['views']) * 100 : 0;
    $ratioCualificado = $leads['total'] > 0 ? ($leads['qualified'] / $leads['total']) * 100 : 0;
    $ratioContratacion = $leads['qualified'] > 0 ? ($leads['won'] / $leads['qualified']) * 100 : 0;
    
    $results[] = [
        'product' => ucfirst($cat),
        'tarificacion' => [
            'current' => $ga['views'],
            'change' => round((($ga['views'] - $ga['prev_views']) / $ga['prev_views']) * 100, 1)
        ],
        'ratio_tarificacion' => [
            'prev' => 36.78, // Mock historic
            'current' => round($ratioTarificacion, 2),
            'change' => 2.5
        ],
        'ratio_cualificado' => [
            'current' => round($ratioCualificado, 1),
            'change' => 6.0
        ],
        'inicio_contratacion' => [
            'current' => $ga['starts'],
            'prev' => $ga['prev_starts'],
            'change' => round((($ga['starts'] - $ga['prev_starts']) / $ga['prev_starts']) * 100, 1)
        ],
        'contrataciones' => [
            'current' => $leads['won'],
            'prev' => 96,
            'change' => -15.0
        ],
        'ratio_exito_global' => round(($leads['won'] / $ga['views']) * 100, 2)
    ];
}

echo json_encode(['status' => 'success', 'data' => $results]);
$conn->close();
