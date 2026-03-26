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
    $range_curr = null; 
    $use_db_for_prev = false;
    $db_type = '';
    $db_num = 0;
    
    // Periodo a consultar siempre en GA4 (Año Actual 2026)
    if ($report_type === 'w_yoy') {
        $range_curr = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']);
        $use_db_for_prev = true; $db_type = 'week'; $db_num = (int)date('W'); // Semana actual ISO
    } elseif ($report_type === 'w_wow') {
        $range_curr = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']);
        $range_prev = new DateRange(['start_date' => '14daysAgo', 'end_date' => '7daysAgo']); // Esto es 2026, va por GA4
    } elseif ($report_type === 'm_yoy') {
        $range_curr = new DateRange(['start_date' => date('Y-m-01'), 'end_date' => 'today']);
        $use_db_for_prev = true; $db_type = 'month'; $db_num = (int)date('n'); // Mes actual
    } elseif ($report_type === 'y_yoy') {
        $range_curr = new DateRange(['start_date' => date('Y-01-01'), 'end_date' => 'today']);
        $use_db_for_prev = true; $db_type = 'ytd'; $db_num = (int)date('n'); // Acumulado hasta este mes
    } else {
        send_json_error('Tipo de reporte desconocido');
    }

    $filter = new FilterExpression([
        'filter' => new Filter([
            'field_name' => 'pagePath',
            'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
        ])
    ]);

    $options = ['timeoutMillis' => 25000];

    // Consulta 1: Periodo Actual (SIEMPRE A GA4 porque es código 2026/Actual)
    $response_curr = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => [$range_curr],
        'dimensionFilter' => $filter
    ], $options);

    $data_map = [];
    foreach ($pc as $path => $name) {
        $data_map[$path] = ['curr' => 0, 'prev' => 0];
    }

    foreach ($response_curr->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        if (isset($data_map[$path])) {
            $data_map[$path]['curr'] = (int)$row->getMetricValues()[0]->getValue();
        }
    }

    // Consulta 2: Obtener Periodo Anterior (PREV)
    if ($use_db_for_prev) {
        // Tirar de MySQL ultrarrápido (2025)
        if ($db_type === 'ytd') {
            // YTD = Suma de todos los meses de 2025 hasta el mes actual
            $stmt = $conn->prepare("SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 WHERE period_type = 'month' AND period_num <= ? GROUP BY page_path");
            $stmt->bind_param("i", $db_num);
        } else {
            // Un solo mes o una sola semana
            $stmt = $conn->prepare("SELECT page_path, sessions as total FROM ga4_history_2025 WHERE period_type = ? AND period_num = ?");
            $stmt->bind_param("si", $db_type, $db_num);
        }
        
        $stmt->execute();
        $resDB = $stmt->get_result();
        while ($rowDB = $resDB->fetch_assoc()) {
            if (isset($data_map[$rowDB['page_path']])) {
                $data_map[$rowDB['page_path']]['prev'] = (int)$rowDB['total'];
            }
        }
    } else {
        // Si no usamos BD (ej: WoW de hace 14 días), tirar de GA4
        $response_prev = $client->runReport([
            'property' => 'properties/' . $property_id,
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics' => [new Metric(['name' => 'sessions'])],
            'dateRanges' => [$range_prev],
            'dimensionFilter' => $filter
        ], $options);
        
        foreach ($response_prev->getRows() as $row) {
            $path = $row->getDimensionValues()[0]->getValue();
            if (isset($data_map[$path])) {
                $data_map[$path]['prev'] = (int)$row->getMetricValues()[0]->getValue();
            }
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
