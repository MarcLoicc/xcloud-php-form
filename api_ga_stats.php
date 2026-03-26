<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

// Obtener Configuración de GA4 desde la DB
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$ga4_id = ($ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : 'GA4-DEFAULT';

// Categorías definidas (Corresponden a los productos)
$products = [
    ['name' => 'Coche', 'path' => '/seguro-coche'],
    ['name' => 'Hogar', 'path' => '/seguro-hogar'],
    ['name' => 'Salud', 'path' => '/seguro-salud'],
    ['name' => 'Moto', 'path' => '/seguro-moto'],
    ['name' => 'Viajes', 'path' => '/seguro-viajes'],
    ['name' => 'Mascotas', 'path' => '/seguro-mascotas']
];

// Simulamos datos DIRECTAMENTE DE GA4 (Métricas como Views, Sessions, Conversions)
// En realidad, haríamos llamadas filtrando por 'path' en GA4
$mockGA4Metrics = [
    'Coche' => ['p1' => 6353, 'p2_ratio' => 37.64, 'p3_ratio' => 54.7, 'p4' => 820, 'p5' => 96, 'exito' => 1.51],
    'Hogar' => ['p1' => 2076, 'p2_ratio' => 36.43, 'p3_ratio' => 58.4, 'p4' => 488, 'p5' => 36, 'exito' => 1.73],
    'Salud' => ['p1' => 2329, 'p2_ratio' => 43.33, 'p3_ratio' => 67.7, 'p4' => 372, 'p5' => 9,  'exito' => 0.39],
    'Moto'  => ['p1' => 1345, 'p2_ratio' => 50.37, 'p3_ratio' => 71.2, 'p4' => 172, 'p5' => 31, 'exito' => 2.30],
    'Viajes' => ['p1' => 1056, 'p2_ratio' => 68.76, 'p3_ratio' => 98.8, 'p4' => 608, 'p5' => 190, 'exito' => 17.9],
    'Mascotas' => ['p1' => 1286, 'p2_ratio' => 53.58, 'p3_ratio' => 71.2, 'p4' => 290, 'p5' => 69, 'exito' => 5.37]
];

$results = [];

foreach ($products as $prod) {
    $m = $mockGA4Metrics[$prod['name']];
    
    $results[] = [
        'product' => $prod['name'],
        'tarificacion' => [
            'current' => $m['p1'],
            'change' => 2.5 // Mock trend
        ],
        'ratio_tarificacion' => [
            'prev' => 36.78, 
            'current' => $m['p2_ratio'],
            'change' => 1.5
        ],
        'ratio_cualificado' => [
            'current' => $m['p3_ratio'],
            'change' => -3.1
        ],
        'inicio_contratacion' => [
            'current' => $m['p4'],
            'prev' => 800,
            'change' => -6.0
        ],
        'contrataciones' => [
            'current' => $m['p5'],
            'prev' => 100,
            'change' => -11.7
        ],
        'ratio_exito_global' => $m['exito']
    ];
}

echo json_encode(['status' => 'success', 'ga4_property' => $ga4_id, 'data' => $results]);
$conn->close();
