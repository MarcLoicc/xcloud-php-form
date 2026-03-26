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

// 1. Obtener Configuración de GA4
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query && $ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;

$credentials_path = __DIR__ . '/google-credentials.json';
$autoload_path = __DIR__ . '/vendor/autoload.php';

if (!$property_id || !file_exists($autoload_path)) {
    echo json_encode(['status' => 'mock', 'message' => 'Configuración incompleta', 'data' => []]);
    exit;
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

    // Definición de Rangos Maestro (7 Rangos)
    $today = date('Y-m-d');
    $first_day_month = date('Y-m-01');
    $first_day_year = date('Y-01-01');
    
    // Año anterior
    $last_year_today = date('Y-m-d', strtotime('-365 days'));
    $last_year_month_start = date('Y-m-01', strtotime('-365 days'));
    $last_year_year_start = date('Y-01-01', strtotime('-365 days'));

    $ranges = [
        new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today', 'name' => 'W_CURR']),
        new DateRange(['start_date' => '372daysAgo', 'end_date' => '365daysAgo', 'name' => 'W_YOY_PREV']),
        new DateRange(['start_date' => '14daysAgo', 'end_date' => '7daysAgo', 'name' => 'W_WOW_PREV']),
        new DateRange(['start_date' => $first_day_month, 'end_date' => 'today', 'name' => 'M_CURR']),
        new DateRange(['start_date' => $last_year_month_start, 'end_date' => $last_year_today, 'name' => 'M_YOY_PREV']),
        new DateRange(['start_date' => $first_day_year, 'end_date' => 'today', 'name' => 'Y_CURR']),
        new DateRange(['start_date' => $last_year_year_start, 'end_date' => $last_year_today, 'name' => 'Y_YOY_PREV']),
    ];

    $response = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dimensions' => [new Dimension(['name' => 'pagePath'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => $ranges,
        'dimensionFilter' => new FilterExpression([
            'filter' => new Filter([
                'field_name' => 'pagePath',
                'in_list_filter' => new InListFilter(['values' => array_keys($pc)])
            ])
        ])
    ]);

    $data_map = [];
    foreach ($response->getRows() as $row) {
        $path = $row->getDimensionValues()[0]->getValue();
        $mv = $row->getMetricValues();
        
        $data_map[$path] = [
            'w_curr' => (int)$mv[0]->getValue(),
            'w_yoy_prev' => (int)$mv[1]->getValue(),
            'w_wow_prev' => (int)$mv[2]->getValue(),
            'm_curr' => (int)$mv[3]->getValue(),
            'm_yoy_prev' => (int)$mv[4]->getValue(),
            'y_curr' => (int)$mv[5]->getValue(),
            'y_yoy_prev' => (int)$mv[6]->getValue()
        ];
    }

    $calc_perc = function($curr, $prev) {
        if ($prev <= 0) return 0;
        return round((($curr - $prev) / $prev) * 100, 1);
    };

    $results = [];
    foreach ($pc as $path => $name) {
        $d = $data_map[$path] ?? ['w_curr' => 0, 'w_yoy_prev' => 0, 'w_wow_prev' => 0, 'm_curr' => 0, 'm_yoy_prev' => 0, 'y_curr' => 0, 'y_yoy_prev' => 0];
        
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

    echo json_encode(['status' => 'success', 'data' => $results]);

} catch (Throwable $e) {
    send_json_error($e->getMessage());
}
