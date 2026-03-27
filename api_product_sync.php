<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db.php';

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

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials]);

    // Filtro base España, sin Guadalajara
    $f_base = [
        new FilterExpression(['filter' => new Filter(['field_name' => 'country', 'string_filter' => new Filter\StringFilter(['value' => 'Spain'])])]),
        new FilterExpression(['not_expression' => new FilterExpression(['filter' => new Filter(['field_name' => 'city', 'string_filter' => new Filter\StringFilter(['value' => 'Guadalajara'])])])])
    ];

    // Filtro para esta URL concreta
    $f_url = new FilterExpression(['and_group' => new FilterExpressionList(['expressions' => array_merge([
        new FilterExpression(['filter' => new Filter(['field_name' => 'pagePath', 'string_filter' => new Filter\StringFilter(['value' => $page_path])])])
    ], $f_base)])]);

    $monthNames = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

    // ── Helper de procesado de filas ──────────────────────────────────────────
    $processRows = function($rows, $p_type) use ($monthNames) {
        $data = [];
        foreach ($rows as $row) {
            $dmns = $row->getDimensionValues();
            $mtrcs = $row->getMetricValues();
            $p_nm_raw = $dmns[0]->getValue();
            $dev = isset($dmns[1]) ? $dmns[1]->getValue() : '';
            $p_nm = (int)$p_nm_raw;
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
            if ($p_nm_raw === 'month') $lb = $monthNames[$n] ?? "Mes $n";
            elseif ($p_nm_raw === 'year') $lb = "Total";
            else $lb = (string)$n;
            if (in_array($p_nm_raw, ['month'])) $lb = $monthNames[$n] ?? "Mes $n";
            elseif ($p_type === 'month') $lb = $monthNames[$n] ?? "Mes $n";
            elseif ($p_type === 'week') $lb = "Semana $n";
            elseif ($p_type === 'year') $lb = "Total";
            else $lb = (string)$n;
            $result[] = ['nm'=>$n, 'lb'=>$lb, 'total'=>$vals['total'], 'web'=>$vals['web'], 'mob'=>$vals['mob'],
                         'av'=>($vals['count']>0) ? $vals['time']/$vals['count'] : 0];
        }
        return $result;
    };

    $dims_day  = [new Dimension(['name'=>'date']),       new Dimension(['name'=>'deviceCategory'])];
    $dims_week = [new Dimension(['name'=>'isoWeek']),    new Dimension(['name'=>'deviceCategory'])];
    $dims_mon  = [new Dimension(['name'=>'month']),      new Dimension(['name'=>'deviceCategory'])];
    $metrics   = [new Metric(['name'=>'screenPageViews']), new Metric(['name'=>'averageSessionDuration'])];

    $inserted_2026 = 0;
    $inserted_2025 = 0;

    // ── SINCRONIZAR 2026 ──────────────────────────────────────────────────────
    $stmt26 = $conn->prepare("INSERT INTO ga4_history_2026 (page_path, period_type, period_num, period_label, sessions, web_views, mobile_views, avg_retention)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE sessions=?, web_views=?, mobile_views=?, avg_retention=?, period_label=?");
    $pth=""; $ty=""; $nm=0; $lb=""; $vs=0; $wb=0; $mb=0; $av=0.0;
    $stmt26->bind_param("ssisiiidiiiis", $pth, $ty, $nm, $lb, $vs, $wb, $mb, $av, $vs, $wb, $mb, $av, $lb);

    $dr26 = new DateRange(['start_date' => '2026-01-01', 'end_date' => 'yesterday']);

    foreach ([['day',$dims_day,$dr26],['week',$dims_week,$dr26],['month',$dims_mon,$dr26]] as [$type, $dims, $dr]) {
        $rows = $client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>$dims,'metrics'=>$metrics,'dateRanges'=>[$dr],'dimensionFilter'=>$f_url])->getRows();
        $pth = $page_path; $ty = $type;
        foreach ($processRows($rows, $type) as $r) {
            $nm=$r['nm']; $lb=$r['lb']; $vs=$r['total']; $wb=$r['web']; $mb=$r['mob']; $av=$r['av'];
            $stmt26->execute();
            $inserted_2026++;
        }
    }

    // ── SINCRONIZAR 2025 ──────────────────────────────────────────────────────
    // Arreglar colación primero
    $conn->query("ALTER TABLE ga4_products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $stmt25 = $conn->prepare("INSERT INTO ga4_history_2025 (page_path, period_type, period_num, period_label, sessions, web_views, mobile_views, avg_retention)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE sessions=?, web_views=?, mobile_views=?, avg_retention=?, period_label=?");
    $stmt25->bind_param("ssisiiidiiiis", $pth, $ty, $nm, $lb, $vs, $wb, $mb, $av, $vs, $wb, $mb, $av, $lb);

    $dr25 = new DateRange(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);

    foreach ([['day',$dims_day,$dr25],['week',$dims_week,$dr25],['month',$dims_mon,$dr25]] as [$type, $dims, $dr]) {
        $rows = $client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>$dims,'metrics'=>$metrics,'dateRanges'=>[$dr],'dimensionFilter'=>$f_url])->getRows();
        $pth = $page_path; $ty = $type;
        foreach ($processRows($rows, $type) as $r) {
            $nm=$r['nm']; $lb=$r['lb']; $vs=$r['total']; $wb=$r['web']; $mb=$r['mob']; $av=$r['av'];
            $stmt25->execute();
            $inserted_2025++;
        }
    }

    // Actualizar flag has_2025_history
    $has_hist = $inserted_2025 > 0 ? 1 : 0;
    $safe_path = $conn->real_escape_string($page_path);
    $conn->query("UPDATE ga4_products SET has_2025_history = $has_hist WHERE page_path = '$safe_path'");

    echo json_encode([
        'status' => 'success',
        'inserted_2026' => $inserted_2026,
        'inserted_2025' => $inserted_2025,
        'has_2025_history' => $has_hist
    ]);

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
