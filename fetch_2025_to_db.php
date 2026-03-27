<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

echo "<h2>🔧 Iniciando Motor de Extracción Histórica (2025)</h2>";

// 1. Crear la Tabla de Historico 2025 en MySQL
$createTableQuery = "
    CREATE TABLE IF NOT EXISTS ga4_history_2025 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_path VARCHAR(255) NOT NULL,
        period_type ENUM('day', 'week', 'month', 'year') NOT NULL,
        period_num INT NOT NULL,
        period_label VARCHAR(50),
        sessions INT DEFAULT 0,
        web_views INT DEFAULT 0,
        mobile_views INT DEFAULT 0,
        avg_retention FLOAT DEFAULT 0,
        UNIQUE KEY u_path_period (page_path, period_type, period_num)
    )
";

if ($conn->query($createTableQuery)) {
    echo "✅ Tabla 'ga4_history_2025' lista y estructurada.<br>";
} else {
    die("❌ Error creando tabla: " . $conn->error);
}

// 2. Comprobar Credenciales
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query && $ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';
$autoload_path = __DIR__ . '/vendor/autoload.php';

if (!$property_id || !file_exists($autoload_path)) {
    die("❌ Configuración incompleta. Faltan credenciales o Vendor.");
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

// Asegurar columnas nuevas
$conn->query("ALTER TABLE ga4_history_2025 ADD COLUMN IF NOT EXISTS web_views INT DEFAULT 0");
$conn->query("ALTER TABLE ga4_history_2025 ADD COLUMN IF NOT EXISTS mobile_views INT DEFAULT 0");
$conn->query("ALTER TABLE ga4_history_2025 ADD COLUMN IF NOT EXISTS avg_retention FLOAT DEFAULT 0");

$conn->query("TRUNCATE TABLE ga4_history_2025");
echo "🧹 Tabla actualizada y despejada.<br>";

try {
    $client = new BetaAnalyticsDataClient(['credentials' => $credentials_path]);
    
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

    $filter_base = [
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

    $filter_pc = new FilterExpression([
        'and_group' => new FilterExpressionList([
            'expressions' => array_merge([
                new FilterExpression([
                    'filter' => new Filter([
                        'field_name' => 'pagePath',
                        'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
                    ])
                ])
            ], $filter_base)
        ])
    ]);

    $filter_total = new FilterExpression([
        'and_group' => new FilterExpressionList(['expressions' => $filter_base])
    ]);

    $dateRange = new DateRange(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);
    
    $stmt = $conn->prepare("INSERT INTO ga4_history_2025 (page_path, period_type, period_num, period_label, sessions, web_views, mobile_views, avg_retention) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE sessions = ?, web_views = ?, mobile_views = ?, avg_retention = ?, period_label = ?");
    
    $path = ""; $type = ""; $num = 0; $label = ""; $views = 0; $web = 0; $mob = 0; $avg = 0.0;
    $stmt->bind_param("ssisiiidiiiis", $path, $type, $num, $label, $views, $web, $mob, $avg, $views, $web, $mob, $avg, $label);

    // FUNCIÓN PARA PROCESAR Y GUARDAR (Para evitar duplicar lógica entre PC y Total)
    $processReport = function($rows, $type, $numPrefix, $monthNames, $stmt) use (&$path, &$num, &$label, &$views, &$web, &$mob, &$avg) {
        $data = [];
        foreach ($rows as $row) {
            $dims = $row->getDimensionValues();
            $metrics = $row->getMetricValues();
            
            $p = $dims[0]->getValue(); // pagePath o month
            $device = "";
            $m_val = 0;

            if (count($dims) == 3) { // pagePath, month/week/etc, device
                $p = $dims[0]->getValue();
                $m_val = (int)$dims[1]->getValue();
                $device = $dims[2]->getValue();
            } elseif (count($dims) == 2) { // pagePath, device OR month, device
                $p = $dims[0]->getValue();
                $device = $dims[1]->getValue();
            }

            if (!isset($data[$p][$m_val])) {
                $data[$p][$m_val] = ['total'=>0, 'web'=>0, 'mob'=>0, 'time'=>0, 'count'=>0];
            }

            $v = (int)$metrics[0]->getValue();
            $t = (float)$metrics[1]->getValue();

            $data[$p][$m_val]['total'] += $v;
            $data[$p][$m_val]['time'] += $t;
            $data[$p][$m_val]['count']++;

            if ($device === 'desktop') {
                $data[$p][$m_val]['web'] += $v;
            } elseif ($device === 'mobile' || $device === 'tablet') {
                $data[$p][$m_val]['mob'] += $v;
            }
        }

        foreach ($data as $p => $months) {
            foreach ($months as $m_val => $vals) {
                $path = $p;
                $num = ($type === 'month') ? $m_val : ($type === 'year' ? 2025 : $m_val);
                $label = ($type === 'month') ? ($monthNames[$m_val] ?? "Mes $m_val") : ($type === 'year' ? "Total 2025" : "Periodo $m_val");
                $views = $vals['total'];
                $web = $vals['web'];
                $mob = $vals['mob'];
                $avg = ($vals['count'] > 0) ? ($vals['time'] / $vals['count']) : 0;
                $stmt->execute();
            }
        }
    };

    $monthNames = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    // 1. ANUALES
    echo "<h3>📊 Extrayendo Datos Anuales 2025...</h3>";
    $resAnual = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'deviceCategory'])],
        'metrics' => [new Metric(['name' => 'screenPageViews']), new Metric(['name' => 'averageSessionDuration'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter_pc
    ]);
    $processReport($resAnual->getRows(), 'year', 2025, $monthNames, $stmt);

    $resAnualTotal = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'deviceCategory'])],
        'metrics' => [new Metric(['name' => 'screenPageViews']), new Metric(['name' => 'averageSessionDuration'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter_total
    ]);
    // Forzamos path TOTAL
    $rowsTotal = [];
    foreach($resAnualTotal->getRows() as $r) {
        $rowsTotal[] = $r; // Agregaremos "TOTAL" manualmente en el procesamiento si es necesario o ajustamos la función
    }
    // Ajuste rápido para TOTAL
    $totalData = ['total'=>0, 'web'=>0, 'mob'=>0, 'time'=>0, 'count'=>0];
    foreach($resAnualTotal->getRows() as $row) {
        $v = (int)$row->getMetricValues()[0]->getValue();
        $t = (float)$row->getMetricValues()[1]->getValue();
        $device = $row->getDimensionValues()[0]->getValue();
        $totalData['total'] += $v;
        $totalData['time'] += $t;
        $totalData['count']++;
        if ($device === 'desktop') $totalData['web'] += $v;
        else if ($device === 'mobile' || $device === 'tablet') $totalData['mob'] += $v;
    }
    $path = "TOTAL"; $num = 2025; $label = "Total 2025";
    $views = $totalData['total']; $web = $totalData['web']; $mob = $totalData['mob'];
    $avg = ($totalData['count'] > 0) ? ($totalData['time'] / $totalData['count']) : 0;
    $stmt->execute();

    // 2. MENSUALES
    echo "<h3>📅 Extrayendo Datos Mensuales 2025...</h3>";
    $resMensual = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'month']), new Dimension(['name' => 'deviceCategory'])],
        'metrics' => [new Metric(['name' => 'screenPageViews']), new Metric(['name' => 'averageSessionDuration'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter_pc
    ]);
    $processReport($resMensual->getRows(), 'month', 0, $monthNames, $stmt);

    $resMensualTotal = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'month']), new Dimension(['name' => 'deviceCategory'])],
        'metrics' => [new Metric(['name' => 'screenPageViews']), new Metric(['name' => 'averageSessionDuration'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter_total
    ]);
    
    $monthTotalData = [];
    foreach($resMensualTotal->getRows() as $row) {
        $m = (int)$row->getDimensionValues()[0]->getValue();
        $device = $row->getDimensionValues()[1]->getValue();
        $v = (int)$row->getMetricValues()[0]->getValue();
        $t = (float)$row->getMetricValues()[1]->getValue();
        if(!isset($monthTotalData[$m])) $monthTotalData[$m] = ['total'=>0, 'web'=>0, 'mob'=>0, 'time'=>0, 'count'=>0];
        $monthTotalData[$m]['total'] += $v;
        $monthTotalData[$m]['time'] += $t;
        $monthTotalData[$m]['count']++;
        if ($device === 'desktop') $monthTotalData[$m]['web'] += $v;
        else if ($device === 'mobile' || $device === 'tablet') $monthTotalData[$m]['mob'] += $v;
    }
    foreach($monthTotalData as $m => $vts) {
        $path = "TOTAL"; $num = $m; $label = $monthNames[$m];
        $views = $vts['total']; $web = $vts['web']; $mob = $vts['mob'];
        $avg = ($vts['count'] > 0) ? ($vts['time'] / $vts['count']) : 0;
        $stmt->execute();
    }

    echo "✔️ Migración parcial completada (Anual y Mensual con métricas extendidas).<br>";
    echo "<h2 style='color:green'>🎉 ¡Okey! Datos de Retención, Web y Móvil guardados para 2025.</h2>";

} catch (Throwable $e) {
    die("<h2 style='color:red'>❌ Error Fatal:</h2>" . $e->getMessage());
}
