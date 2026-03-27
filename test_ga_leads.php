<?php
require 'vendor/autoload.php';
require_once 'db.php';
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\OrderBy;

// 1. Obtener Property ID
$resId = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id' LIMIT 1");
$propertyId = $resId->fetch_assoc()['setting_value'] ?? '450593597';

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/google-credentials.json');
$client = new BetaAnalyticsDataClient();

function getDiagnosticLeads($client, $propertyId, $startDate, $endDate) {
    echo "<h3>Diagnosticando $startDate → $endDate</h3>";
    
    $request = [
        'property' => 'properties/' . $propertyId,
        'dimensions' => [new Dimension(['name' => 'pageReferrer'])],
        'metrics' => [new Metric(['name' => 'eventCount'])],
        'dimensionFilter' => new FilterExpression([
            'filter' => new Filter([
                'field_name' => 'eventName',
                'string_filter' => new Filter\StringFilter(['value' => 'form_submit'])
            ])
        ]),
        'dateRanges' => [new DateRange(['start_date' => $startDate, 'end_date' => $endDate])],
        'keepEmptyRows' => false
    ];

    $resp = $client->runReport($request);
    $data = [];
    foreach ($resp->getRows() as $row) {
        $ref = $row->getDimensionValues()[0]->getValue();
        $cnt = (int)$row->getMetricValues()[0]->getValue();
        $data[] = ['referrer' => $ref, 'count' => $cnt];
    }
    
    // Si no hay datos, tal vez el evento se llama distinto. Repetir listando TOP eventos.
    if (empty($data)) {
        echo "<p style='color:orange;'>⚠️ No hay 'form_submit' con Referrer. Listando TOP 10 eventos por volumen:</p>";
        $req2 = [
            'property' => 'properties/' . $propertyId,
            'dimensions' => [new Dimension(['name' => 'eventName'])],
            'metrics' => [new Metric(['name' => 'eventCount'])],
            'dateRanges' => [new DateRange(['start_date' => $startDate, 'end_date' => $endDate])],
            'limit' => 10,
            'order_bys' => [new OrderBy(['metric' => new OrderBy\MetricOrderBy(['metric_name' => 'eventCount']), 'desc' => true])]
        ];
        $resp2 = $client->runReport($req2);
        foreach ($resp2->getRows() as $r) {
            echo "- " . $r->getDimensionValues()[0]->getValue() . ": " . $r->getMetricValues()[0]->getValue() . "<br>";
        }
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%; border-color:#444; color:#fff; background:#18181b;'>";
        echo "<tr style='background:#27272a;'><th>Referrer (Página de Origen)</th><th>Cantidad (form_submit)</th></tr>";
        foreach ($data as $d) {
            echo "<tr><td>{$d['referrer']}</td><td align='center'>{$d['count']}</td></tr>";
        }
        echo "</table>";
    }
}

echo "<body style='background:#09090b; color:#a1a1aa; font-family:sans-serif;'>";
echo "<h1>Diagnóstico de Leads GA4 (Opción B: Referrer)</h1>";

getDiagnosticLeads($client, $propertyId, '2025-01-01', '2025-12-31');
getDiagnosticLeads($client, $propertyId, '2026-01-01', 'today');

echo "</body>";
