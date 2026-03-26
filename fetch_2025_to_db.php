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
        period_num INT NOT NULL, /* YYYYMMDD para day, 1-52, 1-12, 2025 */
        sessions INT DEFAULT 0,
        UNIQUE KEY u_path_period (page_path, period_type, period_num)
    )
";

if ($conn->query($createTableQuery)) {
    echo "✅ Tabla 'ga4_history_2025' lista en la base de datos.<br>";
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

// 0. Asegurar estructura de tabla con etiquetas descriptivas
$conn->query("ALTER TABLE ga4_history_2025 ADD COLUMN IF NOT EXISTS period_label VARCHAR(50) AFTER period_num");
$conn->query("TRUNCATE TABLE ga4_history_2025");
echo "🧹 Tabla despejada y columna de etiquetas lista.<br>";

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

    // FILTRO: Solo España y EXCLUIR Guadalajara (Compatibilidad 0.9.1)
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

    $dateRange = new DateRange(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);
    
    // Preparar INSERT SQL con etiqueta descriptiva
    $stmt = $conn->prepare("INSERT INTO ga4_history_2025 (page_path, period_type, period_num, period_label, sessions) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE sessions = ?, period_label = ?");
    
    // Variables para binding (se actualizan en los loops)
    $path = ""; $type = ""; $num = 0; $label = ""; $views = 0;
    $stmt->bind_param("ssisiis", $path, $type, $num, $label, $views, $views, $label);

    // 1. EXTRAER POR AÑO (Total Anual)
    echo "<h3>📊 Consultando Visitas Anuales 2025 (España, sin Guadalajara)...</h3>";
    $resYear = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'screenPageViews'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter
    ]);

    $type = 'year';
    $num = 2025;
    $label = "Total 2025";
    foreach ($resYear->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $views = (int)$row->getMetricValues()[0]->getValue();
        $stmt->execute();
    }
    echo "✔️ Totales anuales agregados.<br>";

    // 2. EXTRAER POR MES
    echo "<h3>📅 Consultando Visitas Mensuales 2025...</h3>";
    $resMonth = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'month'])],
        'metrics' => [new Metric(['name' => 'screenPageViews'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter
    ]);

    $monthNames = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    $proccessedMonths = [];
    $type = 'month';
    foreach ($resMonth->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $month = (int)$row->getDimensionValues()[1]->getValue();
        $views = (int)$row->getMetricValues()[0]->getValue();
        
        $num = $month;
        $label = $monthNames[$month];
        $stmt->execute();
        
        if (!isset($proccessedMonths[$month])) {
            echo "✔️ Mes $month ($label) procesado.<br>";
            $proccessedMonths[$month] = true;
        }
    }

    // 3. EXTRAER POR SEMANA
    echo "<h3>📆 Consultando Visitas Semanales 2025...</h3>";
    $resWeek = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'isoWeek'])],
        'metrics' => [new Metric(['name' => 'screenPageViews'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter
    ]);

    $proccessedWeeks = [];
    $type = 'week';
    foreach ($resWeek->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $week = (int)$row->getDimensionValues()[1]->getValue();
        $views = (int)$row->getMetricValues()[0]->getValue();
        
        $num = $week;
        $label = "Semana $week";
        $stmt->execute();

        if (!isset($proccessedWeeks[$week])) {
            echo "✔️ $label procesada.<br>";
            $proccessedWeeks[$week] = true;
        }
    }

    // 4. EXTRAER POR DÍA (GRANULARIDAD MÁXIMA PARA MTD EXACTO)
    echo "<h3>📆 Consultando Visitas Diarias 2025 (Paciencia, esto descarga el año entero)...</h3>";
    $resDay = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'date'])],
        'metrics' => [new Metric(['name' => 'screenPageViews'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter
    ]);

    $proccessedDays = 0;
    $type = 'day';
    foreach ($resDay->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $dateStr = $row->getDimensionValues()[1]->getValue(); // YYYYMMDD
        $views = (int)$row->getMetricValues()[0]->getValue();
        
        $num = (int)$dateStr;
        $label = date('d-m-Y', strtotime($dateStr));
        $stmt->execute();
        $proccessedDays++;
    }
    echo "✔️ $proccessedDays registros diarios agregados.<br>";

    echo "<h2 style='color:green'>🎉 ¡Re-importación COMPLETADA! Granularidad diaria (MTD/YTD exacto) guardada en MySQL.</h2>";

} catch (Throwable $e) {
    die("<h2 style='color:red'>❌ Error Fatal conectando con Google API:</h2>" . $e->getMessage());
}
