<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

// 1. Obtener Configuración de GA4 desde la DB
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';

// Si no hay Property ID o no existe el vendor, devolvemos Mock para no romper el dashboard
if (!$property_id || $property_id === 'PROPIEDAD_AQUI' || !file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo json_encode(getMockData($property_id));
    exit;
}

// 2. Lógica Real con GA4
require 'vendor/autoload.php';
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials_path]);
    
    // Configuración real de tus páginas
    $products_config = [
        'Home / General' => '/',
        'Contacto' => '/contacto/',
        'Móstoles' => '/diseno-web-mostoles/',
        'Casos de Éxito' => '/casos-de-exito-diseno-web/',
        'Clínicas Capilares' => '/diseno-web-para-clinicas-en-madrid/diseno-web-para-clinicas-capilares/',
        'Dentistas Madrid' => '/diseno-web-para-clinicas-en-madrid/diseno-web-para-dentistas-y-clinicas-dentales-en-madrid/',
        'Abogados' => '/diseno-web-para-abogados/',
        'Escuelas' => '/diseno-web-para-escuelas-y-centros-educativos-en-madrid/',
        'Concesionarios' => '/diseno-web-para-concesionarios-en-madrid/',
        'Gimnasios / Yoga' => '/diseno-web-para-gimnasios-y-estudios-de-yoga-en-madrid/',
        'Restaurantes' => '/diseno-de-paginas-web-para-restaurantes/',
        'Farmacias' => '/diseno-web-para-farmacias-en-madrid/',
        'Alcobendas' => '/diseno-web-en-alcobendas/',
        'Villaviciosa' => '/diseno-web-en-villaviciosa-de-odon/',
        'Tres Cantos' => '/diseno-web-en-tres-cantos/',
        'Collado Villalba' => '/diseno-web-en-collado-de-villalba/',
        'Aranjuez' => '/diseno-web-aranjuez/',
        'Arganda' => '/diseno-web-en-arganda-del-rey/',
        'Leganés' => '/diseno-web-en-leganes/',
        'Alcorcón' => '/diseno-web-en-alcorcon/',
        'Alcalá Henares' => '/diseno-web-en-alcala-de-henares/',
        'Clínicas Madrid' => '/diseno-web-para-clinicas-en-madrid/',
        'Tienda Online' => '/diseno-tienda-online-madrid/',
        'Calculadora Precio' => '/calculadora-precio-web-online/'
    ];

    $results = [];

    foreach ($products_config as $name => $path) {
        $response = $client->runReport([
            'property' => 'properties/' . $property_id,
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics' => [
                new Metric(['name' => 'screenPageViews']),
                new Metric(['name' => 'sessions']),
                new Metric(['name' => 'conversions']),
                new Metric(['name' => 'keyEvents'])
            ],
            'dateRanges' => [
                new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']),
                new DateRange(['start_date' => '14daysAgo', 'end_date' => '8daysAgo'])
            ],
            'dimensionFilter' => [
                'filter' => [
                    'field_name' => 'pagePath',
                    'string_filter' => ['match_type' => 'EXACT', 'value' => $path]
                ]
            ]
        ]);

        $current_views = 0; $current_conv = 0;
        foreach ($response->getRows() as $row) {
            $current_views += (int)$row->getMetricValues()[0]->getValue();
            $current_conv += (int)$row->getMetricValues()[2]->getValue();
        }

        // Si no hay datos, ponemos un placeholder de 0 para que no salga vacío
        $results[] = [
            'product' => $name,
            'tarificacion' => [
                'current' => $current_views,
                'change' => 0
            ],
            'ratio_tarificacion' => [
                'prev' => 0,
                'current' => $current_views > 0 ? round(($current_conv / $current_views) * 100, 2) : 0,
                'change' => 0
            ],
            'ratio_cualificado' => ['current' => 0, 'change' => 0],
            'inicio_contratacion' => ['current' => 0, 'prev' => 0, 'change' => 0],
            'contrataciones' => ['current' => $current_conv, 'prev' => 0, 'change' => 0],
            'ratio_exito_global' => $current_views > 0 ? round(($current_conv / $current_views) * 100, 2) : 0
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $results]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'fallback' => getMockDataByList($products_config)]);
}

function getMockDataByList($list) {
    $data = [];
    foreach($list as $name => $path) {
         $data[] = [
            'product' => $name,
            'tarificacion' => ['current' => rand(500, 2000), 'change' => rand(-10, 20)],
            'ratio_tarificacion' => ['prev' => 30.5, 'current' => rand(25, 38), 'change' => 2.5],
            'ratio_cualificado' => ['current' => rand(40, 60), 'change' => 0],
            'inicio_contratacion' => ['current' => rand(100, 400), 'prev' => 300, 'change' => 5],
            'contrataciones' => ['current' => rand(5, 50), 'prev' => 20, 'change' => 0],
            'ratio_exito_global' => rand(1, 8)
        ];
    }
    return ['status' => 'mock_debug', 'data' => $data];
}

function getMockData($id) {
    return getMockDataByList(['Test' => '/']);
}
