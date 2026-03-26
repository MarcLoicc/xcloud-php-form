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
        period_type ENUM('week', 'month', 'year') NOT NULL,
        period_num INT NOT NULL, /* 1-52, 1-12, 2025 */
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

    $filter = new FilterExpression([
        'filter' => new Filter([
            'field_name' => 'pagePath',
            'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
        ])
    ]);

    $dateRange = new DateRange(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);
    
    // Preparar INSERT SQL con UPDATE en caso de duplicado
    $stmt = $conn->prepare("INSERT INTO ga4_history_2025 (page_path, period_type, period_num, sessions) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE sessions = ?");

    // ---------------------------------------------------------
    // 1. EXTRAER POR AÑO (Total Anual)
    // ---------------------------------------------------------
    echo "<h3>📊 Consultando Totales Anuales 2025...</h3>";
    $resYear = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])], // Si no pones dimensión de tiempo, es el total del periodo
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter
    ]);

    foreach ($resYear->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $sessions = (int)$row->getMetricValues()[0]->getValue();
        
        $type = 'year';
        $num = 2025;
        $stmt->bind_param("ssiii", $path, $type, $num, $sessions, $sessions);
        $stmt->execute();
    }
    echo "✔️ Totales agregados.<br>";

    // ---------------------------------------------------------
    // 2. EXTRAER POR MES (1 al 12)
    // ---------------------------------------------------------
    echo "<h3>📅 Consultando Totales Mensuales 2025...</h3>";
    $resMonth = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'month'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter
    ]);

    foreach ($resMonth->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $month = (int)$row->getDimensionValues()[1]->getValue(); // Devuelve '01', '02', convertido a num 1, 2
        $sessions = (int)$row->getMetricValues()[0]->getValue();
        
        $type = 'month';
        $stmt->bind_param("ssiii", $path, $type, $month, $sessions, $sessions);
        $stmt->execute();
    }
    echo "✔️ Meses agregados.<br>";

    // ---------------------------------------------------------
    // 3. EXTRAER POR SEMANA (1 al 52/53) (ISO Week)
    // ---------------------------------------------------------
    echo "<h3>📆 Consultando Totales Semanales 2025...</h3>";
    $resWeek = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath']), new Dimension(['name' => 'isoWeek'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => [$dateRange],
        'dimensionFilter' => $filter
    ]);

    foreach ($resWeek->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $week = (int)$row->getDimensionValues()[1]->getValue(); // Devuelve ISO week number
        $sessions = (int)$row->getMetricValues()[0]->getValue();
        
        $type = 'week';
        $stmt->bind_param("ssiii", $path, $type, $week, $sessions, $sessions);
        $stmt->execute();
    }
    echo "✔️ Semanas agregadas.<br>";

    echo "<h2 style='color:green'>🎉 ¡Extracción COMPLETADA! Todos los datos de 2025 han quedado permanentemente almacenados en MySQL.</h2>";

} catch (Throwable $e) {
    die("<h2 style='color:red'>❌ Error Fatal conectando con Google API:</h2>" . $e->getMessage());
}
