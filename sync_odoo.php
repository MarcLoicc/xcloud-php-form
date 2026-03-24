<?php
require_once 'db.php';
header('Content-Type: text/plain');

// CREDENCIALES ODOO
$url = 'https://marc-loic.odoo.com';
$db = 'marc-loic';
$username = 'contactomarc404@gmail.com';
$password = 'fcbacb311626c453a04dfc3b59a434391bc1c0f1';

echo "SINCRO CRM -> ODOO v1.0\n";
echo "Conectando a Odoo en $url...\n\n";

if (!function_exists('xmlrpc_encode_request')) {
    die("ERROR: La extensión php-xmlrpc no está instalada en el servidor. Pide a soporte de xCloud que la active para usar Odoo.");
}

// 1. Autenticación (Obtener UID)
$common = "$url/xmlrpc/2/common";
$request = xmlrpc_encode_request('authenticate', [$db, $username, $password, []]);
$context = stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: text/xml", 'content' => $request]]);
$file = file_get_contents($common, false, $context);
$uid = xmlrpc_decode($file);

if (!$uid) {
    die("ERROR: Credenciales de Odoo incorrectas o denegadas.");
}
echo "Autenticado con éxito (UID: $uid)\n";

// 2. Buscar Oportunidades (crm.lead)
$models = "$url/xmlrpc/2/object";
$criteria = []; // Todos los leads por ahora
$fields = ['id', 'name', 'contact_name', 'email_from', 'phone', 'planned_revenue', 'stage_id', 'create_date'];
$request = xmlrpc_encode_request('execute_kw', [$db, $uid, $password, 'crm.lead', 'search_read', [$criteria], ['fields' => $fields]]);
$context = stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: text/xml", 'content' => $request]]);
$file = file_get_contents($models, false, $context);
$odooLeads = xmlrpc_decode($file);

if (empty($odooLeads)) {
    die("No se encontraron leads en Odoo.");
}

echo "Encontrados " . count($odooLeads) . " leads en Odoo. Iniciando sincronización...\n\n";

$inserted = 0;
$skipped = 0;

foreach ($odooLeads as $ol) {
    $name = $ol['name'] ?? 'Odoo Lead';
    $contact = $ol['contact_name'] ?? $name;
    $email = $ol['email_from'] ?? '';
    $phone = $ol['phone'] ?? '';
    $revenue = (float)($ol['planned_revenue'] ?? 0);
    $created = $ol['create_date'] ?? date('Y-m-d H:i:s');
    
    // Mapeo básico de estados (puedes ajustar esto luego)
    $stageName = is_array($ol['stage_id']) ? strtolower($ol['stage_id'][1]) : 'nuevo';
    $status = 'nuevo';
    if (strpos($stageName, 'won') !== false || strpos($stageName, 'ganado') !== false) $status = 'ganado';
    if (strpos($stageName, 'lost') !== false || strpos($stageName, 'perdido') !== false) $status = 'perdido';

    // Anti-Duplicados por Email o Nombre exacto
    $stmt = $conn->prepare("SELECT id FROM leads WHERE (email = ? AND email != '') OR name = ?");
    $stmt->bind_param("ss", $email, $contact);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "SALTADO (Duplicado): $contact\n";
        $skipped++;
        continue;
    }

    $ins = $conn->prepare("INSERT INTO leads (name, email, phone, proposal_price, status, source, created_at) VALUES (?, ?, ?, ?, ?, 'organico', ?)");
    $ins->bind_param("sssdss", $contact, $email, $phone, $revenue, $status, $created);
    
    if ($ins->execute()) {
        echo "IMPORTADO de ODOO: $contact (€$revenue)\n";
        $inserted++;
    }
}

echo "\n--- RESUMEN ODOO ---";
echo "\nLeads nuevos traídos: $inserted";
echo "\nLeads ya existentes (saltados): $skipped";
echo "\nSINCRO COMPLETADA";

$conn->close();
?>
