<?php
require_once 'auth.php';
require_once 'db.php';
header('Content-Type: application/json');

// 1. Obtener Configuración de GA4 desde la DB
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';

// Si no hay Property ID o no existe el vendor, devolvemos Mock
if (!$property_id || $property_id === 'PROPIEDAD_AQUI' || !file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo json_encode(getMockData($property_id));
    exit;
}

// 2. Lógica Real con GA4 (Optimizada en 1 sola llamada de BATCH)
require 'vendor/autoload.php';
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials_path]);
    
    $products_config = [
        '/' => 'Home / General',
        '/contacto/' => 'Contacto',
        '/diseno-web-mostoles/' => 'Móstoles',
        '/casos-de-exito-diseno-web/' => 'Casos de Éxito',
        '/diseno-web-para-clinicas-en-madrid/diseno-web-para-clinicas-capilares/' => 'Clínicas Capilares',
        '/diseno-web-para-clinicas-en-madrid/diseno-web-para-dentistas-y-clinicas-dentales-en-madrid/' => 'Dentistas Madrid',
        '/diseno-web-para-abogados/' => 'Abogados',
        '/diseno-web-para-escuelas-y-centros-educativos-en-madrid/' => 'Escuelas',
        '/diseno-web-para-concesionarios-en-madrid/' => 'Concesionarios',
        '/diseno-web-para-gimnasios-y-estudios-de-yoga-en-madrid/' => 'Gimnasios',
        '/diseno-de-paginas-web-para-restaurantes/' => 'Restaurantes',
        '/diseno-web-para-farmacias-en-madrid/' => 'Farmacias',
        '/diseno-web-en-alcobendas/' => 'Alcobendas',
        '/diseno-web-en-villaviciosa-de-odon/' => 'Villaviciosa',
        '/diseno-web-en-tres-cantos/' => 'Tres Cantos',
        '/diseno-web-en-collado-de-villalba/' => 'Collado Villalba',
        '/diseno-web-aranjuez/' => 'Aranjuez',
        '/diseno-web-en-arganda-del-rey/' => 'Arganda',
        '/diseno-web-en-leganes/' => 'Leganés',
        '/diseno-web-en-alcorcon/' => 'Alcorcón',
        '/diseno-web-en-alcala-de-henares/' => 'Alcalá Henares',
        '/diseno-web-para-clinicas-en-madrid/' => 'Clínicas Madrid',
        '/diseno-tienda-online-madrid/' => 'Tienda Online',
        '/calculadora-precio-web-online/' => 'Calculadora Precio'
    ];

    // Consulta única de GA4 para todas las páginas del listado
    $response = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [
            new Metric(['name' => 'screenPageViews']),
            new Metric(['name' => 'conversions']),
            new Metric(['name' => 'keyEvents'])
        ],
        'dateRanges' => [
            new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today'])
        ],
        'dimensionFilter' => [
            'filter' => [
                'field_name' => 'pagePath',
                'in_list_filter' => ['values' => array_keys($products_config)]
            ]
        ]
    ]);

    // Mapeamos los resultados obtenidos a nuestro formato final
    $ga_data = [];
    foreach ($response->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $ga_data[$path] = [
            'views' => (int)$row->getMetricValues()[0]->getValue(),
            'conv' => (int)$row->getMetricValues()[1]->getValue()
        ];
    }

    $results = [];
    foreach ($products_config as $path => $name) {
        $views = $ga_data[$path]['views'] ?? 0;
        $conv = $ga_data[$path]['conv'] ?? 0;

        $results[] = [
            'product' => $name,
            'tarificacion' => ['current' => $views, 'change' => 0],
            'ratio_tarificacion' => ['prev' => 0, 'current' => $views > 0 ? round(($conv * 10 / $views) * 100, 2) : 0, 'change' => 0],
            'ratio_cualificado' => ['current' => 0, 'change' => 0],
            'inicio_contratacion' => ['current' => 0, 'prev' => 0, 'change' => 0],
            'contrataciones' => ['current' => $conv, 'prev' => 0, 'change' => 0],
            'ratio_exito_global' => $views > 0 ? round(($conv / $views) * 100, 2) : 0
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $results]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

function getMockData($id) {
    return ['status' => 'mock_redirect', 'message' => 'Librería cargada. Esperando datos reales...', 'data' => []];
}
