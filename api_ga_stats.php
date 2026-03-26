<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'auth.php';
require_once 'db.php';

header('Content-Type: application/json');

function send_json_error($msg) {
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

// 1. Obtener Configuración de GA4
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query && $ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';
$autoload_path = __DIR__ . '/vendor/autoload.php';

if (!$property_id || !file_exists($autoload_path)) {
    echo json_encode(['status' => 'mock', 'message' => 'Configuración incompleta', 'data' => []]);
    exit;
}

require_once $autoload_path;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\InListFilter;

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials_path]);
    
    $pc = [
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

    // Consulta Mejorada con Dimensión de Rango de Fecha para evitar ambigüedad en los datos
    $response = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [
            new Dimension(['name' => 'pagePath']),
            new Dimension(['name' => 'dateRange'])
        ],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => [
            new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today', 'name' => 'current']),
            new DateRange(['start_date' => '395daysAgo', 'end_date' => '365daysAgo', 'name' => 'last_year'])
        ],
        'dimensionFilter' => new FilterExpression([
            'filter' => new Filter([
                'field_name' => 'pagePath',
                'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
            ])
        ])
    ]);

    $data_map = [];
    foreach ($response->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $range_name = $row->getDimensionValues()[1]->getValue();
        $sessions = (int)$row->getMetricValues()[0]->getValue();
        
        if (!isset($data_map[$path])) {
            $data_map[$path] = ['current' => 0, 'prev' => 0];
        }
        
        if ($range_name === 'current') {
            $data_map[$path]['current'] = $sessions;
        } else {
            $data_map[$path]['prev'] = $sessions;
        }
    }

    $results = [];
    foreach ($pc as $path => $name) {
        $curr = $data_map[$path]['current'] ?? 0;
        $prev = $data_map[$path]['prev'] ?? 0;
        
        $change = ($prev > 0) ? round((($curr - $prev) / $prev) * 100, 1) : 0;
        
        $results[] = [
            'product' => $name,
            'visits' => $curr,
            'change' => $change,
            'prev_year' => $prev
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $results]);

} catch (Throwable $e) {
    send_json_error($e->getMessage());
}
