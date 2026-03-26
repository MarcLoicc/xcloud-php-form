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

if ($report_type === 'init') {
    $currDay = date('d');
    $currMonth = date('m');
    $currDateStr = date('m') . date('d'); // MMDD
    $currWeek = (int)date('W');

    $resOrder = $conn->query("SELECT page_path, sessions FROM ga4_history_2025 WHERE period_type = 'year' AND period_num = 2025 ORDER BY sessions DESC");
    $order_map = [];
    if ($resOrder) {
        while ($r = $resOrder->fetch_assoc()) $order_map[$r['page_path']] = (int)$r['sessions'];
    }

    $fixed_data = [];
    foreach ($pc as $path => $name) {
        $fixed_data[$path] = ['w_yoy' => 0, 'm_yoy' => 0, 'y_yoy' => 0];
    }
    
    $resW = $conn->query("SELECT page_path, sessions FROM ga4_history_2025 WHERE period_type = 'week' AND period_num = $currWeek");
    if($resW) while($r = $resW->fetch_assoc()) if(isset($fixed_data[$r['page_path']])) $fixed_data[$r['page_path']]['w_yoy'] = (int)$r['sessions'];

    $startM = "2025" . $currMonth . "01";
    $endM = "2025" . $currMonth . $currDay;
    $resM = $conn->query("SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= $startM AND period_num <= $endM GROUP BY page_path");
    if($resM) while($r = $resM->fetch_assoc()) if(isset($fixed_data[$r['page_path']])) $fixed_data[$r['page_path']]['m_yoy'] = (int)$r['total'];

    $startY = "20250101";
    $endY = "2025" . $currDateStr;
    $resY = $conn->query("SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= $startY AND period_num <= $endY GROUP BY page_path");
    if($resY) while($r = $resY->fetch_assoc()) if(isset($fixed_data[$r['page_path']])) $fixed_data[$r['page_path']]['y_yoy'] = (int)$r['total'];

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

$cache_file = __DIR__ . '/ga_cache_' . $report_type . '.json';
$cache_time = 3600 * 12; 
$force_refresh = isset($_GET['refresh']) && $_GET['refresh'] === 'true';

if ($force_refresh && file_exists($cache_file)) unlink($cache_file);
if (!$force_refresh && file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    echo file_get_contents($cache_file);
    exit;
}

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
    // Filtros base (España, Sin Guadalajara)
    $filter_base_expressions = [
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
    ];

    $filter_all = new FilterExpression([
        'and_group' => new FilterExpressionList(['expressions' => $filter_base_expressions])
    ]);

    // Filtro específico para productos del panel
    $filter_pc_expressions = array_merge([
        new FilterExpression([
            'filter' => new Filter([
                'field_name' => 'pagePath',
                'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
            ])
        ])
    ], $filter_base_expressions);

    $filter_pc = new FilterExpression([
        'and_group' => new FilterExpressionList(['expressions' => $filter_pc_expressions])
    ]);

    $today = date('Y-m-d');
    $range_curr = null; 
    $use_db_for_prev = false;
    $sql_prev = ''; 
    
    if ($report_type === 'w_yoy') {
        $range_curr = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']);
        $use_db_for_prev = true; 
        $db_num = (int)date('W'); 
        $sql_prev = "SELECT page_path, sessions as total FROM ga4_history_2025 WHERE period_type = 'week' AND period_num = $db_num";
    } elseif ($report_type === 'w_wow') {
        $range_curr = new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']);
        $range_prev = new DateRange(['start_date' => '14daysAgo', 'end_date' => '7daysAgo']); 
    } elseif ($report_type === 'm_yoy') {
        $currDayOfMonth = date('d');
        $currMonth = date('m');
        $range_curr = new DateRange(['start_date' => date('Y-m-01'), 'end_date' => 'today']);
        $startDayDB = "2025" . $currMonth . "01";
        $endDayDB = "2025" . $currMonth . $currDayOfMonth;
        $sql_prev = "SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 
                WHERE period_type = 'day' AND period_num >= $startDayDB AND period_num <= $endDayDB
                GROUP BY page_path";
        $use_db_for_prev = true;
    } elseif ($report_type === 'y_yoy') {
        $currDayOfYearStr = date('m') . date('d'); 
        $range_curr = new DateRange(['start_date' => date('Y-01-01'), 'end_date' => 'today']);
        $startDayDB = "20250101";
        $endDayDB = "2025" . $currDayOfYearStr;
        $sql_prev = "SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 
                WHERE period_type = 'day' AND period_num >= $startDayDB AND period_num <= $endDayDB
                GROUP BY page_path";
        $use_db_for_prev = true;
    } elseif ($report_type === 'monthly_trend') {
        // Reporte de tendencia mensual: 12 meses (2025 vs 2026)
        $monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $results = [];
        
        // 1. Obtener 2026 de GA4 (Datos reales acumulados por mes)
        $response_2026 = $client->runReport([
            'property' => 'properties/' . $property_id,
            'dimensions' => [new Dimension(['name' => 'month'])],
            'metrics' => [new Metric(['name' => 'screenPageViews'])],
            'dateRanges' => [new DateRange(['start_date' => '2026-01-01', 'end_date' => 'today'])],
            'dimensionFilter' => $filter_all
        ]);
        
        $months_2026 = array_fill(1, 12, 0);
        foreach ($response_2026->getRows() as $row) {
            $m = (int)$row->getDimensionValues()[0]->getValue();
            $months_2026[$m] = (int)$row->getMetricValues()[0]->getValue();
        }

        // 2. Obtener 2025 de DB
        $res2025 = $conn->query("SELECT period_num, sessions FROM ga4_history_2025 WHERE period_type = 'month' AND page_path = 'TOTAL' ORDER BY period_num ASC");
        $months_2025 = array_fill(1, 12, 0);
        if ($res2025) {
            while ($r = $res2025->fetch_assoc()) $months_2025[(int)$r['period_num']] = (int)$r['sessions'];
        }

        // 3. Montar comparativa
        for($i=1; $i<=12; $i++) {
            $curr = $months_2026[$i];
            $prev = $months_2025[$i];
            
            $diff = ($prev > 0) ? round((($curr - $prev) / $prev) * 100, 2) : 0;
            $sign = ($diff > 0) ? '+' : '';
            
            $results[] = [
                'month_name' => $monthNames[$i-1],
                'curr' => $curr,
                'prev' => $prev,
                'perc' => ($prev > 0) ? ($sign . $diff . '%') : ($curr > 0 ? '+∞' : '0%'),
                'raw_perc' => $diff
            ];
        }
        
        $final_json = json_encode(['status' => 'success', 'data' => $results]);
        echo $final_json;
        exit;
    }

    $options = ['timeoutMillis' => 25000];

    $response_curr = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'screenPageViews'])],
        'dateRanges' => [$range_curr],
        'dimensionFilter' => $filter_pc
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
        $response_prev = $client->runReport([
            'property' => 'properties/' . $property_id,
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics' => [new Metric(['name' => 'screenPageViews'])],
            'dateRanges' => [$range_prev],
            'dimensionFilter' => $filter_pc
        ], $options);
        
        foreach ($response_prev->getRows() as $row) {
            $path = $row->getDimensionValues()[0]->getValue();
            if (isset($data_map[$path])) {
                $data_map[$path]['prev'] = (int)$row->getMetricValues()[0]->getValue();
            }
        }
    }

    $results = [];
    foreach ($data_map as $path => $d) {
        $curr = (float)$d['curr'];
        $prev = (float)$d['prev'];
        
        $diff_val = 0;
        $perc_str = 'N/A';

        if ($prev > 0) {
            $diff_val = round((($curr - $prev) / $prev) * 100, 2);
            $sign = ($diff_val > 0) ? '+' : '';
            $perc_str = $sign . $diff_val . '%';
        } elseif ($curr > 0) {
            $diff_val = 999; // Representar infinito para colores
            $perc_str = '+∞';
        }

        $results[$path] = [
            'curr' => $d['curr'],
            'prev' => $d['prev'],
            'perc' => $perc_str,
            'raw_perc' => $diff_val
        ];
    }

    $final_json = json_encode(['status' => 'success', 'data' => $results]);
    
    file_put_contents($cache_file, $final_json);
    echo $final_json;

} catch (Throwable $e) {
    send_json_error("Google Analytics error en [{$report_type}]: " . $e->getMessage());
}