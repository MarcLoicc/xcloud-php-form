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

// Si solo piden la lista de productos y datos fijos 2025
if ($report_type === 'init') {
    $currDay = date('d');
    $currMonth = date('m');
    $currDateStr = date('m') . date('d'); // MMDD
    $currWeek = (int)date('W');

    // 1. Obtener orden por Total 2025
    $resOrder = $conn->query("SELECT page_path, sessions FROM ga4_history_2025 WHERE period_type = 'year' AND period_num = 2025 ORDER BY sessions DESC");
    $order_map = [];
    if ($resOrder) {
        while ($r = $resOrder->fetch_assoc()) $order_map[$r['page_path']] = (int)$r['sessions'];
    }

    // 2. Obtener Datos Fijos 2025 para Semanas, MTD y YTD (Exacto por día)
    $fixed_data = [];
    foreach ($pc as $path => $name) {
        $fixed_data[$path] = ['w_yoy' => 0, 'm_yoy' => 0, 'y_yoy' => 0];
    }
    
    // Semana YoY
    $resW = $conn->query("SELECT page_path, sessions FROM ga4_history_2025 WHERE period_type = 'week' AND period_num = $currWeek");
    while($r = $resW->fetch_assoc()) if(isset($fixed_data[$r['page_path']])) $fixed_data[$r['page_path']]['w_yoy'] = (int)$r['sessions'];

    // MTD Exacto 2025
    $startM = "2025" . $currMonth . "01";
    $endM = "2025" . $currMonth . $currDay;
    $resM = $conn->query("SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= $startM AND period_num <= $endM GROUP BY page_path");
    while($r = $resM->fetch_assoc()) if(isset($fixed_data[$r['page_path']])) $fixed_data[$r['page_path']]['m_yoy'] = (int)$r['total'];

    // YTD Exacto 2025
    $startY = "20250101";
    $endY = "2025" . $currDateStr;
    $resY = $conn->query("SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= $startY AND period_num <= $endY GROUP BY page_path");
    while($r = $resY->fetch_assoc()) if(isset($fixed_data[$r['page_path']])) $fixed_data[$r['page_path']]['y_yoy'] = (int)$r['total'];

    // Construir respuesta final ordenada
    $sorted_pc = [];
    $seen = [];
    arsort($order_map);
    foreach ($order_map as $path => $total) {
        if (isset($pc[$path])) {
            $sorted_pc[$path] = [
                'name' => $pc[$path],
                'fixed' => $fixed_data[$path] ?? ['w_yoy'=>0, 'm_yoy'=>0, 'y_yoy'=>0]
            ];
            $seen[$path] = true;
        }
    }
    foreach ($pc as $path => $name) {
        if (!isset($seen[$path])) {
            $sorted_pc[$path] = [
                'name' => $name,
                'fixed' => ['w_yoy'=>0, 'm_yoy'=>0, 'y_yoy'=>0]
            ];
        }
    }

    echo json_encode(['status' => 'success', 'data' => $sorted_pc]);
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
use Google\Analytics\Data\V1beta\FilterExpressionList;

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials_path]);
    
    $today = date('Y-m-d');
    $range_curr = null; 
    $use_db_for_prev = false;
    $db_type = '';
    $sql_prev = ''; // Variable para la consulta SQL del periodo anterior
    
    // Periodo a consultar siempre en GA4 (Año Actual 2026)
    if ($report_type === 'w_yoy') {
        $range_curr = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']);
        $use_db_for_prev = true; 
        $db_type = 'week'; 
        $db_num = (int)date('W'); // Semana actual ISO
        $sql_prev = "SELECT page_path, sessions as total FROM ga4_history_2025 WHERE period_type = 'week' AND period_num = $db_num";
    } elseif ($report_type === 'w_wow') {
        $range_curr = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']);
        $range_prev = new DateRange(['start_date' => '14daysAgo', 'end_date' => '7daysAgo']); // Esto es 2026, va por GA4
    } elseif ($report_type === 'm_yoy') {
        $currDayOfMonth = date('d');
        $currMonth = date('m');
        
        $range_curr = new DateRange(['start_date' => date('Y-m-01'), 'end_date' => 'today']);
        
        // MTD 2025 exacto: Sumar días del 01 al hoy del año pasado
        $startDayDB = "2025" . $currMonth . "01";
        $endDayDB = "2025" . $currMonth . $currDayOfMonth;

        $sql_prev = "SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 
                WHERE period_type = 'day' AND period_num >= $startDayDB AND period_num <= $endDayDB
                GROUP BY page_path";
        $use_db_for_prev = true;
    } elseif ($report_type === 'y_yoy') {
        // YTD: Del 1 de enero hasta hoy
        $currDayOfYearStr = date('m') . date('d'); // MMDD
        
        $range_curr = new DateRange(['start_date' => date('Y-01-01'), 'end_date' => 'today']);

        $startDayDB = "20250101";
        $endDayDB = "2025" . $currDayOfYearStr;

        $sql_prev = "SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 
                WHERE period_type = 'day' AND period_num >= $startDayDB AND period_num <= $endDayDB
                GROUP BY page_path";
        $use_db_for_prev = true;
    } else {
        send_json_error('Tipo de reporte desconocido');
    }

    $filter = new FilterExpression([
        'and_group' => new FilterExpressionList([
            'expressions' => [
                new FilterExpression([
                    'filter' => new Filter([
                        'field_name' => 'pagePath',
                        'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
                    ])
                ]),
                new FilterExpression([
                    'filter' => new Filter([
                        'field_name' => 'country',
                        'string_filter' => new Filter\StringFilter(['value' => 'Spain'])
                    ])
                ]),
                new FilterExpression([
                    'not_expression' => new FilterExpression([
                        'filter' => new Filter([
                            'field_name' => 'city',
                            'string_filter' => new Filter\StringFilter(['value' => 'Guadalajara'])
                        ])
                    ])
                ])
            ]
        ])
    ]);

    $options = ['timeoutMillis' => 25000];

    // Consulta 1: Periodo Actual (SIEMPRE A GA4 porque es código 2026/Actual)
    $response_curr = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'screenPageViews'])],
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

    // Consulta 2: Obtener Periodo Anterior (PREV) desde DB (Historico 2025) o GA4 (2026)
    if ($use_db_for_prev) {
        $resDB = $conn->query($sql_prev);
        if ($resDB) {
            while ($rowDB = $resDB->fetch_assoc()) {
                if (isset($data_map[$rowDB['page_path']])) {
                    $data_map[$rowDB['page_path']]['prev'] = (int)$rowDB['total'];
                }
            }
        }
    } else {
        // Consulta 2: Periodo Anterior (GA4 para WoW 2026)
        $response_prev = $client->runReport([
            'property' => 'properties/' . $property_id,
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics' => [new Metric(['name' => 'screenPageViews'])],
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
