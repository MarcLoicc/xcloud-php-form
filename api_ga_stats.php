<?php
require 'db.php';
require __DIR__ . '/vendor/autoload.php';

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\InListFilter;
use Google\Analytics\Data\V1beta\FilterExpressionList;

$report_type = $_GET['report'] ?? 'init';
$property_id = '472095333';
$credentials_path = __DIR__ . '/google-credentials.json';

// URLs de productos a trackear
$pc = [
    '/diseno-web-en-madrid/' => 'HOME / GENERAL',
    '/contacto/' => 'CONTACTO',
    '/calculadora-precio-web-online/' => 'CALCULADORA PRECIO',
    '/diseno-web-en-alcala-de-henares/' => 'ALCALÁ HENARES',
    '/diseno-web-para-abogados-en-madrid/' => 'ABOGADOS',
    '/diseno-web-para-clinicas-en-madrid/' => 'CLÍNICAS MADRID',
    '/diseno-web-para-restaurantes-en-madrid/' => 'RESTAURANTES',
    '/diseno-web-para-dentistas-en-madrid/' => 'DENTISTAS MADRID',
    '/diseno-web-en-mostoles/' => 'MÓSTOLES',
    '/diseno-web-en-alcorcon/' => 'ALCORCÓN',
    '/diseno-web-en-gimnasios-en-madrid/' => 'GIMNASIOS',
    '/diseno-web-para-colegios-escuelas-en-madrid/' => 'ESCUELAS',
    '/diseno-web-para-concesionarios-en-madrid/' => 'CONCESIONARIOS',
    '/diseno-web-en-leganes/' => 'LEGANÉS',
    '/diseno-web-aranjuez/' => 'ARANJUEZ',
    '/diseno-web-para-clinicas-capilares-en-madrid/' => 'CLÍNICAS CAPILARES',
    '/diseno-web-en-arganda-del-rey/' => 'ARGANDA',
    '/diseno-web-en-collado-de-villalba/' => 'COLLADO VILLALBA',
    '/diseno-web-en-alcobendas/' => 'ALCOBENDAS',
    '/diseno-web-en-villaviciosa-de-odon/' => 'VILLAVICIOSA',
    '/diseno-web-en-tres-cantos/' => 'TRES CANTOS',
    '/farmacias/' => 'FARMACIAS',
    '/diseno-tienda-online-madrid/' => 'TIENDA ONLINE'
];

function send_json_error($msg) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

if ($report_type === 'init') {
    $data = [];
    foreach ($pc as $path => $name) {
        $data[] = [
            'path' => $path,
            'name' => $name,
            'w_prev' => '-', 'w_curr' => '-', 'w_var' => '-',
            'm_prev' => '-', 'm_curr' => '-', 'm_var' => '-',
            'y_prev' => '-', 'y_curr' => '-', 'y_var' => '-'
        ];
    }
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials_path]);
    
    // Filtros base
    $filter_base = [
        new FilterExpression(['filter' => new Filter(['field_name' => 'country','string_filter' => new Filter\StringFilter(['value' => 'Spain'])])]),
        new FilterExpression(['not_expression' => new FilterExpression(['filter' => new Filter(['field_name' => 'city','string_filter' => new Filter\StringFilter(['value' => 'Guadalajara'])])])])
    ];
    $filter_pc = new FilterExpression(['and_group' => new FilterExpressionList(['expressions' => array_merge([new FilterExpression(['filter' => new Filter(['field_name' => 'pagePath','in_list_filter' => new InListFilter(['values' => array_keys($pc)])])])], $filter_base)])]);
    $filter_all = new FilterExpression(['and_group' => new FilterExpressionList(['expressions' => $filter_base])]);

    $metrics = [new Metric(['name' => 'screenPageViews']), new Metric(['name' => 'totalUsers']), new Metric(['name' => 'userEngagementDuration'])];
    $dimensions = [new Dimension(['name' => 'pagePath'])];

    if ($report_type === 'monthly_trend') {
        // TENDENCIA 12 MESES
        $monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $results = [];

        // 2026 de GA4
        $res2026 = $client->runReport([
            'property' => 'properties/' . $property_id,
            'dimensions' => [new Dimension(['name' => 'month']), new Dimension(['name' => 'deviceCategory'])],
            'metrics' => $metrics,
            'dateRanges' => [new DateRange(['start_date' => '2026-01-01', 'end_date' => 'today'])],
            'dimensionFilter' => $filter_all
        ]);
        
        $m26 = [];
        for($i=1;$i<=12;$i++) $m26[$i] = ['v'=>0,'u'=>0,'d'=>0,'mob'=>0,'tab'=>0,'pc'=>0];
        foreach($res2026->getRows() as $row) {
            $m = (int)$row->getDimensionValues()[0]->getValue();
            $dev = $row->getDimensionValues()[1]->getValue();
            $v = (int)$row->getMetricValues()[0]->getValue();
            $u = (int)$row->getMetricValues()[1]->getValue();
            $d = (float)$row->getMetricValues()[2]->getValue();
            $m26[$m]['v'] += $v; $m26[$m]['u'] += $u; $m26[$m]['d'] += $d;
            if ($dev === 'mobile') $m26[$m]['mob'] += $u;
            elseif ($dev === 'tablet') $m26[$m]['tab'] += $u;
            else $m26[$m]['pc'] += $u;
        }

        // 2025 de DB
        $res2025 = $conn->query("SELECT * FROM ga4_history_2025 WHERE period_type = 'month' AND page_path = 'TOTAL' ORDER BY period_num ASC");
        $m25 = [];
        for($i=1;$i<=12;$i++) $m25[$i] = ['v'=>0,'u'=>0,'mob'=>0,'tab'=>0,'pc'=>0,'avg'=>0];
        while($r = $res2025->fetch_assoc()) {
            $m = (int)$r['period_num'];
            $m25[$m] = ['v'=>$r['sessions'], 'u'=>$r['mobile_users']+$r['tablet_users']+$r['desktop_users'], 'mob'=>$r['mobile_users'], 'tab'=>$r['tablet_users'], 'pc'=>$r['desktop_users'], 'avg'=>$r['avg_engagement_time']];
        }

        for($i=1;$i<=12;$i++) {
            $c = $m26[$i]; $p = $m25[$i];
            $diff = ($p['v'] > 0) ? round((($c['v'] - $p['v']) / $p['v']) * 100, 2) : 0;
            $avg26 = $c['u'] > 0 ? round($c['d'] / $c['u'], 2) : 0;
            
            $results[] = [
                'month_name' => $monthNames[$i-1],
                'curr' => $c['v'], 'prev' => $p['v'],
                'perc' => ($p['v'] > 0) ? (($diff>0 ? '+': '').$diff.'%') : ($c['v']>0 ? '+∞' : '0%'),
                'raw_perc' => $diff,
                // Nuevos datos
                'c_mob' => $c['mob'], 'c_tab' => $c['tab'], 'c_pc' => $c['pc'], 'c_avg' => $avg26,
                'p_mob' => $p['mob'], 'p_tab' => $p['tab'], 'p_pc' => $p['pc'], 'p_avg' => $p['avg']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $results]);
        exit;
    }

    // LÓGICA NORMAL (YoY, WoW, etc)
    $range_curr = null;
    $use_db_for_prev = false;
    $sql_prev = "";

    if ($report_type === 'w_yoy') {
        $weekNo = (int)date('W');
        $range_curr = new DateRange(['start_date' => 'monday this week', 'end_date' => 'today']);
        $sql_prev = "SELECT page_path, sessions as total FROM ga4_history_2025 WHERE period_type = 'week' AND period_num = $weekNo";
        $use_db_for_prev = true;
    } elseif ($report_type === 'm_yoy') {
        $currMonth = date('m'); $currDay = date('d');
        $range_curr = new DateRange(['start_date' => date('Y-m-01'), 'end_date' => 'today']);
        $startDayDB = "2025" . $currMonth . "01";
        $endDayDB = "2025" . $currMonth . $currDay;
        $sql_prev = "SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= $startDayDB AND period_num <= $endDayDB GROUP BY page_path";
        $use_db_for_prev = true;
    } elseif ($report_type === 'y_yoy') {
        $range_curr = new DateRange(['start_date' => date('Y-01-01'), 'end_date' => 'today']);
        $startDayDB = "20250101"; $endDayDB = "2025" . date('md');
        $sql_prev = "SELECT page_path, SUM(sessions) as total FROM ga4_history_2025 WHERE period_type = 'day' AND period_num >= $startDayDB AND period_num <= $endDayDB GROUP BY page_path";
        $use_db_for_prev = true;
    }

    $response_curr = $client->runReport(['property' => 'properties/' . $property_id, 'dimensions' => $dimensions, 'metrics' => $metrics, 'dateRanges' => [$range_curr], 'dimensionFilter' => $filter_pc]);
    
    $data_map = [];
    foreach ($response_curr->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $v = (int)$row->getMetricValues()[0]->getValue();
        $data_map[$path] = ['curr' => $v, 'prev' => 0];
    }

    if ($use_db_for_prev) {
        $resP = $conn->query($sql_prev);
        while ($r = $resP->fetch_assoc()) {
            if (isset($data_map[$r['page_path']])) $data_map[$r['page_path']]['prev'] = (int)$r['total'];
        }
    }

    $final = [];
    foreach ($data_map as $path => $vals) {
        $c = $vals['curr']; $p = $vals['prev'];
        $diff = ($p > 0) ? round((($c - $p) / $p) * 100, 2) : 0;
        $final[] = [
            'path' => $path, 'name' => $pc[$path] ?? $path,
            'curr' => $c, 'prev' => $p,
            'var' => ($p > 0) ? (($diff>0 ? '+': '').$diff.'%') : ($c>0 ? '+∞' : '0%'),
            'raw_var' => $diff
        ];
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'data' => $final]);

} catch (Exception $e) {
    send_json_error($e->getMessage());
}