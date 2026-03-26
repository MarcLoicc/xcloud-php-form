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

// 0. SISTEMA DE CACHÉ ULTRA-RÁPIDO (Carga en 0.01 segundos)
$cache_file = __DIR__ . '/ga_cache.json';
$cache_time = 3600 * 12; // 12 horas de duración de la caché

// Si el usuario fuerza la refresco (Botón Sincronizar)
$force_refresh = isset($_GET['refresh']) && $_GET['refresh'] === 'true';

if ($force_refresh && file_exists($cache_file)) {
    unlink($cache_file);
}

// Si existe y es válida, la devolvemos inmediatamente SIN esperar a Google
if (!$force_refresh && file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    echo file_get_contents($cache_file);
    exit;
}

// Si no hay caché, capturamos posibles errores fatales por timeout para que no se congele la web en gris
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean(); // Limpiar salidas a medias
        echo json_encode(['status' => 'error', 'message' => 'El servidor tardó más de 30 segundos en contactar con Google Analytics (Timeout). Por favor reintentar en un minuto.']);
    }
});

// 1. Obtener Configuración
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query && $ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';
$autoload_path = __DIR__ . '/vendor/autoload.php';

if (!$property_id || !file_exists($autoload_path)) {
    send_json_error('Configuración incompleta: Faltan credenciales de Google.');
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

    $first_day_month = date('Y-m-01');
    $first_day_year = date('Y-01-01');
    $last_year_today = date('Y-m-d', strtotime('-365 days'));
    $last_year_month_start = date('Y-m-01', strtotime('-365 days'));
    $last_year_year_start = date('Y-01-01', strtotime('-365 days'));

    $ranges1 = [
        new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']), 
        new DateRange(['start_date' => '372daysAgo', 'end_date' => '365daysAgo']), 
        new DateRange(['start_date' => '14daysAgo', 'end_date' => '7daysAgo']), 
        new DateRange(['start_date' => $first_day_month, 'end_date' => 'today']), 
    ];

    $ranges2 = [
        new DateRange(['start_date' => $last_year_month_start, 'end_date' => $last_year_today]), 
        new DateRange(['start_date' => $first_day_year, 'end_date' => 'today']), 
        new DateRange(['start_date' => $last_year_year_start, 'end_date' => $last_year_today]), 
    ];

    $filter = new FilterExpression([
        'filter' => new Filter([
            'field_name' => 'pagePath',
            'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
        ])
    ]);

    // Opciones para evitar el Freeze infinito
    $options = ['timeoutMillis' => 20000]; 

    $response1 = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => $ranges1,
        'dimensionFilter' => $filter
    ], $options);

    $response2 = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => $ranges2,
        'dimensionFilter' => $filter
    ], $options);

    $data_map = [];
    foreach ($pc as $path => $name) {
        $data_map[$path] = [
            'w_curr' => 0, 'w_yoy_prev' => 0, 'w_wow_prev' => 0, 'm_curr' => 0,
            'm_yoy_prev' => 0, 'y_curr' => 0, 'y_yoy_prev' => 0
        ];
    }

    foreach ($response1->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $mv = $row->getMetricValues();
        if (isset($data_map[$path])) {
            $data_map[$path]['w_curr'] = (int)$mv[0]->getValue();
            $data_map[$path]['w_yoy_prev'] = isset($mv[1]) ? (int)$mv[1]->getValue() : 0;
            $data_map[$path]['w_wow_prev'] = isset($mv[2]) ? (int)$mv[2]->getValue() : 0;
            $data_map[$path]['m_curr'] = isset($mv[3]) ? (int)$mv[3]->getValue() : 0;
        }
    }

    foreach ($response2->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $mv = $row->getMetricValues();
        if (isset($data_map[$path])) {
            $data_map[$path]['m_yoy_prev'] = (int)$mv[0]->getValue();
            $data_map[$path]['y_curr'] = isset($mv[1]) ? (int)$mv[1]->getValue() : 0;
            $data_map[$path]['y_yoy_prev'] = isset($mv[2]) ? (int)$mv[2]->getValue() : 0;
        }
    }

    $calc_perc = function($curr, $prev) {
        if ($prev <= 0) return 0;
        return round((($curr - $prev) / $prev) * 100, 1);
    };

    $results = [];
    foreach ($pc as $path => $name) {
        $d = $data_map[$path];
        $results[] = [
            'product' => $name,
            'semana_yoy' => [
                'curr' => $d['w_curr'],
                'prev' => $d['w_yoy_prev'],
                'perc' => $calc_perc($d['w_curr'], $d['w_yoy_prev'])
            ],
            'semana_wow' => [
                'curr' => $d['w_curr'],
                'prev' => $d['w_wow_prev'],
                'perc' => $calc_perc($d['w_curr'], $d['w_wow_prev'])
            ],
            'mes_yoy' => [
                'curr' => $d['m_curr'],
                'prev' => $d['m_yoy_prev'],
                'perc' => $calc_perc($d['m_curr'], $d['m_yoy_prev'])
            ],
            'anual_yoy' => [
                'curr' => $d['y_curr'],
                'prev' => $d['y_yoy_prev'],
                'perc' => $calc_perc($d['y_curr'], $d['y_yoy_prev'])
            ]
        ];
    }

    $final_json_payload = json_encode(['status' => 'success', 'data' => $results]);
    
    // GUARDAR LA TABLA COMPLETA EN CACHÉ LIGERA PARA QUE EL SIGUIENTE CLIC SEA INSTANTANEO
    file_put_contents($cache_file, $final_json_payload);
    echo $final_json_payload;

} catch (Throwable $e) {
    send_json_error("CRITICAL GA4 ERROR: " . escapeshellcmd($e->getMessage()));
}
