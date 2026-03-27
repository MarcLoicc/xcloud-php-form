<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

// Si se ejecuta por Cron, no necesita salida HTML pero la dejamos por si se abre manualmente
echo "<h2>🔄 GA4 Auto-Sync Dashboard 2026</h2>";

// Cargar productos desde BD (con fallback si la tabla aún no existe)
$pc = [];
$pc_res = $conn->query("SELECT page_path, name FROM ga4_products WHERE active = 1 ORDER BY name ASC");
if ($pc_res && $pc_res->num_rows > 0) {
    while ($r = $pc_res->fetch_assoc()) $pc[$r['page_path']] = $r['name'];
}
if (empty($pc)) {
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
        '/calculadora-precio-web-online/' => 'Calculadora Precio',
    ];
}

$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query && $ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';
$autoload_path = __DIR__ . '/vendor/autoload.php';

if (!$property_id || !file_exists($autoload_path)) {
    die("❌ Error en configuración de API.");
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
    
    // Filtros de España y sin Guadalajara y solo Productos
    $f_base = [
        new FilterExpression(['filter' => new Filter(['field_name' => 'country', 'string_filter' => new Filter\StringFilter(['value' => 'Spain'])])]),
        new FilterExpression(['not_expression' => new FilterExpression(['filter' => new Filter(['field_name' => 'city', 'string_filter' => new Filter\StringFilter(['value' => 'Guadalajara'])])])])
    ];
    $f_pc = new FilterExpression(['and_group' => new FilterExpressionList(['expressions' => array_merge([new FilterExpression(['filter' => new Filter(['field_name' => 'pagePath', 'in_list_filter' => new InListFilter(['values' => array_keys($pc)])])])], $f_base)])]);

    $stmt = $conn->prepare("INSERT INTO ga4_history_2026 (page_path, period_type, period_num, period_label, sessions, web_views, mobile_views, avg_retention) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE sessions = ?, web_views = ?, mobile_views = ?, avg_retention = ?, period_label = ?");
    $pth=""; $ty=""; $nm=0; $lb=""; $vs=0; $wb=0; $mb=0; $av=0.0;
    $stmt->bind_param("ssisiiidiiiis", $pth, $ty, $nm, $lb, $vs, $wb, $mb, $av, $vs, $wb, $mb, $av, $lb);

    $process = function($rows, $p_type, $label_prefix, $stmt) use (&$pth, &$ty, &$nm, &$lb, &$vs, &$wb, &$mb, &$av) {
        $ty = $p_type; $data = []; $monthNames = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        foreach ($rows as $row) {
            $dmns = $row->getDimensionValues(); $mtrcs = $row->getMetricValues();
            $p = (count($dmns) > 1 && !is_numeric($dmns[0]->getValue())) ? $dmns[0]->getValue() : "TOTAL";
            $p_nm = (int)$dmns[count($dmns) === 1 ? 0 : (is_numeric($dmns[0]->getValue()) ? 0 : 1)]->getValue();
            $dev = count($dmns) === 3 ? $dmns[2]->getValue() : (count($dmns) === 2 ? $dmns[1]->getValue() : "");
            if (!isset($data[$p][$p_nm])) $data[$p][$p_nm] = ['total'=>0, 'web'=>0, 'mob'=>0, 'time'=>0, 'count'=>0];
            $v = (int)$mtrcs[0]->getValue(); $t = (float)$mtrcs[1]->getValue();
            $data[$p][$p_nm]['total'] += $v; $data[$p][$p_nm]['time'] += $t; $data[$p][$p_nm]['count']++;
            if ($dev === 'desktop') $data[$p][$p_nm]['web'] += $v;
            else if ($dev === 'mobile' || $dev === 'tablet') $data[$p][$p_nm]['mob'] += $v;
        }
        foreach ($data as $p => $pds) {
            foreach ($pds as $n => $vals) {
                $pth = $p; $nm = ($ty === 'year') ? 2026 : $n;
                if($ty==='month') $lb = $monthNames[$n] ?? "Mes $n";
                else if($ty==='year') $lb = "Total 2026";
                else $lb = $label_prefix . " " . $n;
                $vs = $vals['total']; $wb = $vals['web']; $mb = $vals['mob'];
                $av = ($vals['count'] > 0) ? ($vals['time'] / $vals['count']) : 0;
                $stmt->execute();
            }
        }
    };

    // 2. Ejecutar Reportes para 2026
    $dr = new DateRange(['start_date' => '2026-01-01', 'end_date' => 'yesterday']);

    echo "📊 Sincronizando Anuales 2026...<br>";
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'pagePath']),new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr],'dimensionFilter'=>$f_pc])->getRows(), 'year', '', $stmt);
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr],'dimensionFilter'=>$f_pc])->getRows(), 'year', '', $stmt); // "TOTAL" implícito

    echo "📅 Sincronizando Mensuales 2026...<br>";
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'pagePath']),new Dimension(['name'=>'month']),new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr],'dimensionFilter'=>$f_pc])->getRows(), 'month', '', $stmt);
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'month']),new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr],'dimensionFilter'=>$f_pc])->getRows(), 'month', '', $stmt);

    echo "🔄 Sincronizando Semanales 2026...<br>";
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'pagePath']),new Dimension(['name'=>'isoWeek']),new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr],'dimensionFilter'=>$f_pc])->getRows(), 'week', 'Semana', $stmt);
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'isoWeek']),new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr],'dimensionFilter'=>$f_pc])->getRows(), 'week', 'Semana', $stmt);

    echo "☀️ Sincronizando Diarios 2026 (Solo ayer y hoy parcial)...<br>";
    $dr_day = new DateRange(['start_date' => '30daysAgo', 'end_date' => 'yesterday']); // Sincronizamos últimos 30 días para cubrir cualquier delay
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'pagePath']),new Dimension(['name'=>'date']),new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr_day],'dimensionFilter'=>$f_pc])->getRows(), 'day', '', $stmt);
    $process($client->runReport(['property'=>'properties/'.$property_id,'dimensions'=>[new Dimension(['name'=>'date']),new Dimension(['name'=>'deviceCategory'])],'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],'dateRanges'=>[$dr_day],'dimensionFilter'=>$f_pc])->getRows(), 'day', '', $stmt);

    echo "<h2 style='color:green'>🎉 ¡Sincronización Automática 2026 completada!</h2>";

    // ─── AUTO-IMPORTACIÓN HISTÓRICO 2025 PARA PRODUCTOS NUEVOS ───────────────
    // Arreglar colación de ga4_products si difiere de ga4_history_2025
    $conn->query("ALTER TABLE ga4_products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Detecta productos en ga4_products que NO tienen ninguna fila en ga4_history_2025
    $new_products_res = $conn->query("
        SELECT p.page_path, p.name FROM ga4_products p
        WHERE p.active = 1
          AND NOT EXISTS (
              SELECT 1 FROM ga4_history_2025 h
              WHERE h.page_path = p.page_path COLLATE utf8mb4_unicode_ci
          )
    ");

    if ($new_products_res && $new_products_res->num_rows > 0) {
        echo "<hr><h3>🔎 Productos nuevos sin histórico 2025 detectados: {$new_products_res->num_rows}</h3>";

        $monthNames = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
        $dr_2025 = new DateRange(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);

        $stmt25 = $conn->prepare("INSERT INTO ga4_history_2025 (page_path, period_type, period_num, period_label, sessions, web_views, mobile_views, avg_retention) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE sessions = ?, web_views = ?, mobile_views = ?, avg_retention = ?, period_label = ?");
        $pth25=""; $ty25=""; $nm25=0; $lb25=""; $vs25=0; $wb25=0; $mb25=0; $av25=0.0;
        $stmt25->bind_param("ssisiiidiiiis", $pth25, $ty25, $nm25, $lb25, $vs25, $wb25, $mb25, $av25, $vs25, $wb25, $mb25, $av25, $lb25);

        while ($prod = $new_products_res->fetch_assoc()) {
            $path = $prod['page_path'];
            $pname = $prod['name'];
            echo "📂 Importando 2025 para <b>$pname</b> ($path)...<br>";

            // Filtro solo para este producto + filtros base
            $f_single = new FilterExpression(['and_group' => new FilterExpressionList(['expressions' => array_merge([
                new FilterExpression(['filter' => new Filter(['field_name' => 'pagePath', 'string_filter' => new Filter\StringFilter(['value' => $path])])])
            ], $f_base)])]);

            $inserted = 0;

            // Helper para insertar filas 2025 por period_type
            $insert2025 = function($rows, $p_type) use (&$pth25, &$ty25, &$nm25, &$lb25, &$vs25, &$wb25, &$mb25, &$av25, $stmt25, $path, $monthNames, &$inserted) {
                $ty25 = $p_type;
                $data = [];
                foreach ($rows as $row) {
                    $dmns = $row->getDimensionValues();
                    $mtrcs = $row->getMetricValues();
                    // first dim is period number (date/week/month), second is deviceCategory
                    $p_nm_raw = $dmns[0]->getValue();
                    $dev = isset($dmns[1]) ? $dmns[1]->getValue() : '';
                    $p_nm = ($p_type === 'day') ? (int)$p_nm_raw : (int)$p_nm_raw;
                    $v = (int)$mtrcs[0]->getValue();
                    $t = (float)$mtrcs[1]->getValue();
                    if (!isset($data[$p_nm])) $data[$p_nm] = ['total'=>0,'web'=>0,'mob'=>0,'time'=>0,'count'=>0];
                    $data[$p_nm]['total'] += $v;
                    $data[$p_nm]['time'] += $t;
                    $data[$p_nm]['count']++;
                    if ($dev === 'desktop') $data[$p_nm]['web'] += $v;
                    elseif ($dev === 'mobile' || $dev === 'tablet') $data[$p_nm]['mob'] += $v;
                }
                foreach ($data as $n => $vals) {
                    $pth25 = $path; $nm25 = $n;
                    if ($p_type === 'month') $lb25 = $monthNames[$n] ?? "Mes $n";
                    elseif ($p_type === 'year') $lb25 = "Total 2025";
                    else $lb25 = ($p_type === 'week') ? "Semana $n" : (string)$n;
                    $vs25 = $vals['total']; $wb25 = $vals['web']; $mb25 = $vals['mob'];
                    $av25 = ($vals['count'] > 0) ? ($vals['time'] / $vals['count']) : 0;
                    $stmt25->execute();
                    $inserted++;
                }
            };

            try {
                // Diarios 2025
                $rows_d = $client->runReport(['property'=>'properties/'.$property_id,
                    'dimensions'=>[new Dimension(['name'=>'date']),new Dimension(['name'=>'deviceCategory'])],
                    'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],
                    'dateRanges'=>[$dr_2025],'dimensionFilter'=>$f_single])->getRows();
                $insert2025($rows_d, 'day');

                // Semanales 2025
                $rows_w = $client->runReport(['property'=>'properties/'.$property_id,
                    'dimensions'=>[new Dimension(['name'=>'isoWeek']),new Dimension(['name'=>'deviceCategory'])],
                    'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],
                    'dateRanges'=>[$dr_2025],'dimensionFilter'=>$f_single])->getRows();
                $insert2025($rows_w, 'week');

                // Mensuales 2025
                $rows_m = $client->runReport(['property'=>'properties/'.$property_id,
                    'dimensions'=>[new Dimension(['name'=>'month']),new Dimension(['name'=>'deviceCategory'])],
                    'metrics'=>[new Metric(['name'=>'screenPageViews']),new Metric(['name'=>'averageSessionDuration'])],
                    'dateRanges'=>[$dr_2025],'dimensionFilter'=>$f_single])->getRows();
                $insert2025($rows_m, 'month');

                if ($inserted > 0) {
                    // Marcar como con histórico 2025
                    $conn->query("UPDATE ga4_products SET has_2025_history = 1 WHERE page_path = '" . $conn->real_escape_string($path) . "'");
                    echo "   ✅ $inserted filas importadas para $pname<br>";
                } else {
                    echo "   ℹ️ Sin datos en 2025 para $pname (URL nueva de 2026)<br>";
                    // Marcar explícitamente como sin histórico 2025
                    $conn->query("UPDATE ga4_products SET has_2025_history = 0 WHERE page_path = '" . $conn->real_escape_string($path) . "'");
                }
            } catch (Throwable $ex) {
                echo "   ⚠️ Error importando 2025 para $pname: " . $ex->getMessage() . "<br>";
            }
        }
        echo "<h3 style='color:green'>✅ Auto-importación 2025 completada.</h3>";
    } else {
        echo "<p style='color:#888'>✔️ Todos los productos ya tienen histórico 2025 (o no aplica).</p>";
    }
    // ─────────────────────────────────────────────────────────────────────────

} catch (Throwable $e) {
    echo "❌ Error en sincronización: " . $e->getMessage();
}
