<?php
require_once 'db.php';
header('Content-Type: text/plain');

// CREDENCIALES ODOO
$url = 'https://marc-loic.odoo.com';
$db = 'marc-loic';
$username = 'contactomarc404@gmail.com';
$password = 'fcbacb311626c453a04dfc3b59a434391bc1c0f1';

echo "SINCRO CRM (MODO CURL) -> ODOO v1.1\n";
echo "Conectando sin XMLRPC (Usando cURL nativo)...\n\n";

function odoo_call($endpoint, $method, $params) {
    global $url;
    $xml = odoo_xml_request($method, $params);
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml']);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return odoo_parse_response($response);
}

function odoo_xml_request($method, $params) {
    $xml = "<?xml version='1.0'?><methodCall><methodName>$method</methodName><params>";
    foreach ($params as $p) {
        $xml .= "<param><value>" . odoo_val($p) . "</value></param>";
    }
    $xml .= "</params></methodCall>";
    return $xml;
}

function odoo_val($v) {
    if (is_int($v)) return "<int>$v</int>";
    if (is_float($v)) return "<double>$v</double>";
    if (is_bool($v)) return "<boolean>" . ($v ? '1' : '0') . "</boolean>";
    if (is_array($v)) {
        if (array_values($v) === $v) { // indexed array
            $xml = "<array><data>";
            foreach ($v as $item) $xml .= "<value>" . odoo_val($item) . "</value>";
            $xml .= "</data></array>";
            return $xml;
        } else { // associative
            $xml = "<struct>";
            foreach ($v as $k => $item) {
                $xml .= "<member><name>$k</name><value>" . odoo_val($item) . "</value></member>";
            }
            $xml .= "</struct>";
            return $xml;
        }
    }
    return "<string>" . htmlspecialchars((string)$v) . "</string>";
}

function odoo_parse_response($xml) {
    if (!$xml) return null;
    $dom = new DOMDocument();
    @$dom->loadXML($xml);
    $values = $dom->getElementsByTagName('value');
    if ($values->length == 0) return null;
    return odoo_parse_node($values->item(0));
}

function odoo_parse_node($node) {
    $child = $node->firstChild;
    while ($child && $child->nodeType != XML_ELEMENT_NODE) $child = $child->nextSibling;
    if (!$child) return (string)$node->nodeValue;
    
    switch ($child->tagName) {
        case 'int': case 'i4': return (int)$child->nodeValue;
        case 'double': return (float)$child->nodeValue;
        case 'boolean': return (bool)$child->nodeValue;
        case 'string': return (string)$child->nodeValue;
        case 'array':
            $arr = [];
            $vals = $child->getElementsByTagName('value');
            foreach ($vals as $v) $arr[] = odoo_parse_node($v);
            return $arr;
        case 'struct':
            $obj = [];
            $members = $child->getElementsByTagName('member');
            foreach ($members as $m) {
                $k = $m->getElementsByTagName('name')->item(0)->nodeValue;
                $v = $m->getElementsByTagName('value')->item(0);
                $obj[$k] = odoo_parse_node($v);
            }
            return $obj;
    }
    return (string)$child->nodeValue;
}

// 1. Autenticación
$uid = odoo_call("$url/xmlrpc/2/common", 'authenticate', [$db, $username, $password, []]);
if (!$uid) die("ERROR: Fallo de login en Odoo.\n");
echo "UID: $uid (OK)\n";

// 2. Buscar Oportunidades
$fields = ['id', 'name', 'contact_name', 'email_from', 'phone', 'planned_revenue', 'stage_id', 'create_date'];
$odooLeads = odoo_call("$url/xmlrpc/2/object", 'execute_kw', [$db, $uid, $password, 'crm.lead', 'search_read', [[]], ['fields' => $fields]]);

if (!is_array($odooLeads)) {
    die("Odoo devolvió un formato inesperado o error: " . print_r($odooLeads, true));
}

echo "Leads totales encontrados en Odoo: " . count($odooLeads) . "\n";
if(count($odooLeads) > 0) {
    echo "Primeros 3 nombres detectados en Odoo: \n";
    for($i=0; $i<min(3, count($odooLeads)); $i++) {
        echo "- " . ($odooLeads[$i]['contact_name'] ?? $odooLeads[$i]['name'] ?? 'Sin nombre') . "\n";
    }
}
echo "\nIniciando proceso de volcado...\n";

$inserted = 0;
foreach ($odooLeads as $ol) {
    if (!is_array($ol)) continue; // Saltar si no es un registro válido

    $contact = $ol['contact_name'] ?? ($ol['name'] ?? 'Odoo Lead');
    $email = $ol['email_from'] ?? '';
    $phone = $ol['phone'] ?? '';
    $revenue = (float)($ol['planned_revenue'] ?? 0);
    $created = $ol['create_date'] ?? date('Y-m-d H:i:s');
    
    // Mapeo de estados
    $stageName = 'nuevo';
    if (isset($ol['stage_id']) && is_array($ol['stage_id']) && isset($ol['stage_id'][1])) {
        $stageName = strtolower($ol['stage_id'][1]);
    }
    
    $status = 'nuevo';
    if (stripos($stageName, 'won') !== false || stripos($stageName, 'ganado') !== false) $status = 'ganado';
    if (stripos($stageName, 'lost') !== false || stripos($stageName, 'perdido') !== false) $status = 'perdido';

    // Anti-Duplicados
    $stmt = $conn->prepare("SELECT id FROM leads WHERE (email = ? AND email != '') OR name = ?");
    $stmt->bind_param("ss", $email, $contact);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) continue;

    $ins = $conn->prepare("INSERT INTO leads (name, email, phone, proposal_price, status, source, created_at) VALUES (?, ?, ?, ?, ?, 'organico', ?)");
    $ins->bind_param("sssdss", $contact, $email, $phone, $revenue, $status, $created);
    if ($ins->execute()) {
        echo "Importado: $contact\n";
        $inserted++;
    }
}
echo "\nSINCRO COMPLETADA. Total nuevos de Odoo: $inserted\n";
?>
