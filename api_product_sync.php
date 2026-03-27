<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db.php';

// Asegurar colación correcta
$conn->query("ALTER TABLE ga4_products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->query("ALTER TABLE ga4_history_2025 CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->query("ALTER TABLE ga4_history_2026 CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

$input = json_decode(file_get_contents('php://input'), true);
$page_path = trim($input['page_path'] ?? '');

if (!$page_path) {
    echo json_encode(['status' => 'error', 'message' => 'page_path requerido']);
    exit;
}

$ga4_id_q = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_q && $ga4_id_q->num_rows > 0) ? $ga4_id_q->fetch_assoc()['setting_value'] : null;
$autoload = __DIR__ . '/vendor/autoload.php';
$credentials = __DIR__ . '/google-credentials.json';

if (!$property_id || !file_exists($autoload)) {
    echo json_encode(['status' => 'error', 'message' => 'Configuración GA4 incompleta']);
    exit;
}

require_once $autoload;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials]);

    // Filtros base negocio
    $f_base = [
        new FilterExpression(['filter' => new Filter(['field_name' => 'country', 'string_filter' => new Filter\StringFilter(['value' => 'Spain'])])]),
        new FilterExpression(['not_expression' => new FilterExpression(['filter' => new Filter(['field_name' => 'city', 'string_filter' => new Filter\StringFilter(['value' => 'Guadalajara'])])])])
    ];

    // Filtro URL flexible (BEGINS_WITH para capturar variaciones con/sin slash final o params)
    $f_url = new FilterExpression(['and_group' => new FilterExpressionList(['expressions' => array_merge([
        new FilterExpression(['filter' => new Filter(['field_name' => 'pagePath', 'string_filter' => new Filter\StringFilter([
            'value' => rtrim($page_path, '/'), 
            'match_type' => MatchType::BEGINS_WITH
        ])])])
    ], $f_base)])]);

    $monthNames = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

    // ── Procesa respuesta GA4 ────────────────────────────────────────────────
    $processRows = function($rows, $p_type) use ($monthNames) {
        $data = [];
        foreach ($rows as $row) {
            $dmns = $row->getDimensionValues();
            $mtrcs = $row->getMetricValues();
            
            if ($p_type === 'year') {
                $p_nm = 0; 
                $dev = $dmns[0]->getValue();
            } else {
                $p_nm = (int)$dmns[0]->getValue();
                $dev = $dmns[1]->getValue();
            }

            $v = (int)$mtrcs[0]->getValue();
            $t = (float)$mtrcs[1]->getValue();

            if (!isset($data[$p_nm])) $data[$p_nm] = ['total'=>0,'web'=>0,'mob'=>0,'time'=>0,'count'=>0];
            $data[$p_nm]['total'] += $v;
            $data[$p_nm]['time'] += $t;
            $data[$p_nm]['count']++;
            if ($dev === 'desktop') $data[$p_nm]['web'] += $v;
            elseif ($dev === 'mobile' || $dev === 'tablet') $data[$p_nm]['mob'] += $v;
        }
        
        $result = [];
        foreach ($data as $n => $vals) {
            $lb = "";
            if ($p_type === 'month') $lb = $monthNames[$n] ?? "Mes $n";
            elseif ($p_type === 'week') $lb = "Semana $n";
            elseif ($p_type === 'year') $lb = "Total";
            else $lb = (string)$n;

            $result[] = ['nm'=>$n, 'lb'=>$lb, 'total'=>$vals['total'], 'web'=>$vals['web'], 'mob'=>$vals['mob'],
                         'av'=>($vals['count']>0) ? $vals['time']/$vals['count'] : 0];
        }
        return $result;
    };

    $metrics = [new Metric(['name'=>'screenPageViews']), new Metric(['name'=>'averageSessionDuration'])];
    $inserted_2026 = 0;
    $inserted_2025 = 0;
    $debug = [];

    // ── FUNCION SYNC ─────────────────────────────────────────────────────────
    $syncYear = function($yr, $client, $propId, $f_url, $metrics, $page_path, $conn) use ($processRows, &$inserted_2026, &$inserted_2025, &$debug) {
        $tbl = "ga4_history_$yr";
        $dr = ($yr == 2025) ? new DateRange(['start_date' => '2025-01-01', 'end_date' => '2025-12-31'])
                            : new DateRange(['start_date' => '2026-01-01', 'end_date' => 'today']); // Usar today para 2026
        
        $stmt = $conn->prepare("INSERT INTO $tbl (page_path, period_type, period_num, period_label, sessions, web_views, mobile_views, avg_retention)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE sessions=?, web_views=?, mobile_views=?, avg_retention=?, period_label=?");
        
        $pth=$page_path; $ty=""; $nm=0; $lb=""; $vs=0; $wb=0; $mb=0; $av=0.0;
        $stmt->bind_param("ssisiiidiiiis", $pth, $ty, $nm, $lb, $vs, $wb, $mb, $av, $vs, $wb, $mb, $av, $lb);

        $configs = [
            ['day',   [new Dimension(['name'=>'date']),    new Dimension(['name'=>'deviceCategory'])]],
            ['week',  [new Dimension(['name'=>'isoWeek']), new Dimension(['name'=>'deviceCategory'])]],
            ['month', [new Dimension(['name'=>'month']),   new Dimension(['name'=>'deviceCategory'])]],
            ['year',  [new Dimension(['name'=>'deviceCategory'])]]
        ];

        foreach ($configs as [$type, $dims]) {
            $rows = $client->runReport(['property'=>'properties/'.$propId,'dimensions'=>$dims,'metrics'=>$metrics,'dateRanges'=>[$dr],'dimensionFilter'=>$f_url])->getRows();
            $debug[] = "Year $yr, Type $type: " . count($rows) . " rows from GA4";
            $ty = $type;
            foreach ($processRows($rows, $type) as $r) {
                $nm = ($type === 'year') ? (int)$yr : $r['nm'];
                $lb = $r['lb']; $vs = $r['total']; $wb = $r['web']; $mb = $r['mob']; $av = $r['av'];
                if (!$stmt->execute()) throw new Exception($stmt->error);
                if ($yr == 2025) $inserted_2025++; else $inserted_2026++;
            }
        }
        $stmt->close();
    };

    // ── EJECUTAR ─────────────────────────────────────────────────────────────
    $syncYear(2026, $client, $property_id, $f_url, $metrics, $page_path, $conn);
    $syncYear(2025, $client, $property_id, $f_url, $metrics, $page_path, $conn);

    // Actualizar flag
    $has_hist = $inserted_2025 > 0 ? 1 : 0;
    $safe_path = $conn->real_escape_string($page_path);
    $conn->query("UPDATE ga4_products SET has_2025_history = $has_hist WHERE page_path = '$safe_path'");

    echo json_encode([
        'status' => 'success',
        'inserted_2026' => $inserted_2026,
        'inserted_2025' => $inserted_2025,
        'has_2025_history' => $has_hist,
        'debug' => $debug
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
