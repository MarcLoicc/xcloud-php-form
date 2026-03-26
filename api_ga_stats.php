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

$report_type = $_GET['report'] ?? 'init';

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

// Si solo piden la lista de productos
if ($report_type === 'init') {
    echo json_encode(['status' => 'success', 'data' => $pc]);
    exit;
}

// CACHÉ INDIVIDUAL PARA CADA REPORTE (Acelera aún más)
$cache_file = __DIR__ . '/ga_cache_' . $report_type . '.json';
$cache_time = 3600 * 12; // 12 horas

$force_refresh = isset($_GET['refresh']) && $_GET['refresh'] === 'true';

if ($force_refresh && file_exists($cache_file)) unlink($cache_file);

if (!$force_refresh && file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    echo file_get_contents($cache_file);
    exit;
}

// Inicializar Google API
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query && $ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';
$autoload_path = __DIR__ . '/vendor/autoload.php';

if (!$property_id || !file_exists($autoload_path)) {
    send_json_error('Configuración incompleta: Faltan credenciales.');
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
    
    $today = date('Y-m-d');
    $ranges = [];

    if ($report_type === 'w_yoy') {
        $ranges[] = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']); // act (0)
        $ranges[] = new DateRange(['start_date' => '372daysAgo', 'end_date' => '365daysAgo']); // prev (1)
    } elseif ($report_type === 'w_wow') {
        $ranges[] = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']); // act (0)
        $ranges[] = new DateRange(['start_date' => '14daysAgo', 'end_date' => '7daysAgo']); // prev (1)
    } elseif ($report_type === 'm_yoy') {
        $ranges[] = new DateRange(['start_date' => date('Y-m-01'), 'end_date' => 'today']); // act (0)
        $ranges[] = new DateRange(['start_date' => date('Y-m-01', strtotime('-365 days')), 'end_date' => date('Y-m-d', strtotime('-365 days'))]); // prev (1)
    } elseif ($report_type === 'y_yoy') {
        $ranges[] = new DateRange(['start_date' => date('Y-01-01'), 'end_date' => 'today']); // act (0)
        $ranges[] = new DateRange(['start_date' => date('Y-01-01', strtotime('-365 days')), 'end_date' => date('Y-m-d', strtotime('-365 days'))]); // prev (1)
    } else {
        send_json_error('Tipo de reporte desconocido');
    }

    $filter = new FilterExpression([
        'filter' => new Filter([
            'field_name' => 'pagePath',
            'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
        ])
    ]);

    $response = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => $ranges,
        'dimensionFilter' => $filter
    ]);

    $data_map = [];
    foreach ($pc as $path => $name) {
        $data_map[$path] = ['curr' => 0, 'prev' => 0];
    }

    foreach ($response->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $mv = $row->getMetricValues();
        if (isset($data_map[$path])) {
            $data_map[$path]['curr'] = (int)$mv[0]->getValue();
            $data_map[$path]['prev'] = isset($mv[1]) ? (int)$mv[1]->getValue() : 0;
        }
    }

    $calc_perc = function($curr, $prev) {
        if ($prev <= 0) return 0;
        return round((($curr - $prev) / $prev) * 100, 1);
    };

    $results = [];
    foreach ($data_map as $path => $d) {
        $results[$path] = [
            'curr' => $d['curr'],
            'prev' => $d['prev'],
            'perc' => $calc_perc($d['curr'], $d['prev'])
        ];
    }

    $final_json = json_encode(['status' => 'success', 'data' => $results]);
    
    file_put_contents($cache_file, $final_json);
    echo $final_json;

} catch (Throwable $e) {
    send_json_error("Google Analytics error en [{$report_type}]: " . $e->getMessage());
}
