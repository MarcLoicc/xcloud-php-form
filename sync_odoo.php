<?php
require_once 'db.php';
header('Content-Type: text/plain; charset=UTF-8');

// CREDENCIALES ODOO
$url = 'https://marc-loic.odoo.com';
$db = 'marc-loic';
$username = 'contactomarc404@gmail.com';
$password = 'fcbacb311626c453a04dfc3b59a434391bc1c0f1';

echo "SINCRO CRM -> ODOO (V2.0 - REFINADO)\n";
echo "Filtrando por 'Mi Flujo' (Oportunidades Activas)...\n\n";

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
    if (!$node) return null;
    $child = null;
    foreach ($node->childNodes as $cn) {
        if ($cn->nodeType === XML_ELEMENT_NODE) { $child = $cn; break; }
    }
    if (!$child) return (string)$node->nodeValue;
    
    switch ($child->tagName) {
        case 'int': case 'i4': return (int)$child->nodeValue;
        case 'double': return (float)$child->nodeValue;
        case 'boolean': return (bool)$child->nodeValue;
        case 'string': return (string)$child->nodeValue;
        case 'array':
            $arr = [];
            $dataNode = null;
            foreach ($child->childNodes as $cn) { if ($cn->nodeName === 'data') { $dataNode = $cn; break; } }
            if ($dataNode) {
                foreach ($dataNode->childNodes as $cn) {
                    if ($cn->nodeName === 'value') $arr[] = odoo_parse_node($cn);
                }
            }
            return $arr;
        case 'struct':
            $obj = [];
            foreach ($child->childNodes as $m) {
                if ($m->nodeName === 'member') {
                    $k = ''; $vNode = null;
                    foreach ($m->childNodes as $cn) {
                        if ($cn->nodeName === 'name') $k = $cn->nodeValue;
                        if ($cn->nodeName === 'value') $vNode = $cn;
                    }
                    if ($k !== '' && $vNode) $obj[$k] = odoo_parse_node($vNode);
                }
            }
            return $obj;
    }
    return (string)$child->nodeValue;
}

// 1. Autenticación
$uid = odoo_call("$url/xmlrpc/2/common", 'authenticate', [$db, $username, $password, []]);
if (!$uid) die("ERROR: Fallo de login en Odoo.\n");
echo "UID: $uid (Conectado con Odoo)\n";

// Directorio para audios
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// 2. Buscar Oportunidades con campos estándar
$fields = ['id', 'name', 'contact_name', 'email_from', 'phone', 'expected_revenue', 'stage_id', 'create_date', 'description'];
$domain = [
    ['type', '=', 'opportunity'],
    ['active', '=', true]
];

$odooLeads = odoo_call("$url/xmlrpc/2/object", 'execute_kw', [$db, $uid, $password, 'crm.lead', 'search_read', [$domain], ['fields' => $fields]]);

if (!is_array($odooLeads) || isset($odooLeads['faultCode'])) {
    die("Odoo Error: " . ($odooLeads['faultString'] ?? 'Error desconocido'));
}

echo "Procesando " . count($odooLeads) . " registros encontrados...\n\n";

$inserted = 0; $skipped = 0; $audios_dl = 0;

foreach ($odooLeads as $ol) {
    if (!is_array($ol)) continue;

    $leadId = (int)($ol['id'] ?? 0);
    if (!$leadId) continue;

    $contact = !empty($ol['contact_name']) ? (string)$ol['contact_name'] : (string)($ol['name'] ?? "Lead Odoo #$leadId");
    $email = (string)($ol['email_from'] ?? '');
    
    // Solo Phone por ahora para evitar errores de modelo en Odoo 19
    $phone = (string)($ol['phone'] ?? '');
    
    $revenue = (float)($ol['expected_revenue'] ?? 0);
    $created = (string)($ol['create_date'] ?? date('Y-m-d H:i:s'));
    $desc = (string)($ol['description'] ?? '');
    
    // Mapeo detallado de estados solicitado por el usuario
    $stageName = isset($ol['stage_id']) && is_array($ol['stage_id']) ? strtolower($ol['stage_id'][1]) : 'nuevo';
    $status = 'nuevo';

    if (stripos($stageName, 'ganado') !== false || stripos($stageName, 'colaboracion') !== false) {
        $status = 'ganado';
    } elseif (stripos($stageName, 'perdido') !== false || stripos($stageName, 'rechazada') !== false) {
        $status = 'perdido';
    } elseif (stripos($stageName, 'propuesta enviada') !== false || stripos($stageName, 'seguimiento') !== false || stripos($stageName, 'revisar propuesta') !== false) {
        $status = 'propuesta_enviada';
    } elseif (stripos($stageName, 'enviar propuesta') !== false) {
        $status = 'enviar_propuesta';
    } elseif (stripos($stageName, 'sin respuesta') !== false) {
        $status = 'sin_respuesta';
    } elseif (stripos($stageName, 'nuevo lead') !== false || stripos($stageName, 'llamar') !== false) {
        $status = 'nuevo';
    }

    // Lógica inteligente: Si ya existe, actualizamos descripción, audio, TELÉFONO y ESTADO (overwrite status now).
    $stmt = $conn->prepare("SELECT id, audio_path, message, phone FROM leads WHERE (email = ? AND email != '') OR (name = ? AND ? != '') LIMIT 1");
    $stmt->bind_param("sss", $email, $contact, $contact);
    $stmt->execute();
    $res = $stmt->get_result();
    $existing = $res->fetch_assoc();

    // --- PROCESAR AUDIOS ADJUNTOS SIEMPRE ---
    $audioPath = $existing['audio_path'] ?? null;
    if (!$audioPath) { 
        $attachments = odoo_call("$url/xmlrpc/2/object", 'execute_kw', [$db, $uid, $password, 'ir.attachment', 'search_read', [
            [['res_model', '=', 'crm.lead'], ['res_id', '=', $leadId]]
        ], ['fields' => ['name', 'datas', 'mimetype']]]);

        if (is_array($attachments)) {
            foreach ($attachments as $att) {
                $mimetype = $att['mimetype'] ?? '';
                $filename = $att['name'] ?? '';
                if (stripos($mimetype, 'audio') !== false || stripos($filename, '.webm') !== false) {
                    if (!empty($att['datas'])) {
                        $audioData = base64_decode($att['datas']);
                        $safeName = 'odoo_' . $leadId . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                        if (file_put_contents($uploadDir . $safeName, $audioData)) {
                            $audioPath = 'uploads/' . $safeName;
                            $audios_dl++; break;
                        }
                    }
                }
            }
        }
    }

    if ($existing) {
        // ACTUALIZAR EXISTENTE (ahora también forzamos el estado para que coincida con Odoo)
        $upd = $conn->prepare("UPDATE leads SET message = IF(message IS NULL OR message = '', ?, message), audio_path = IF(audio_path IS NULL OR audio_path = '', ?, audio_path), phone = IF(phone IS NULL OR phone = '', ?, phone), status = ? WHERE id = ?");
        $upd->bind_param("ssssi", $desc, $audioPath, $phone, $status, $existing['id']);
        $upd->execute();
        echo "Actualizado: $contact" . ($audioPath ? " [AUDIO OK]" : "") . " [ESTADO: $status]\n";
        $skipped++;
    } else {
        // INSERTAR NUEVO
        $ins = $conn->prepare("INSERT INTO leads (name, email, phone, proposal_price, status, source, message, audio_path, created_at) VALUES (?, ?, ?, ?, ?, 'organico', ?, ?, ?)");
        $ins->bind_param("sssdssss", $contact, $email, $phone, $revenue, $status, $desc, $audioPath, $created);
        if ($ins->execute()) {
            echo "Importado: $contact (€$revenue)\n";
            $inserted++;
        }
    }
}

echo "\n--- RESUMEN FINAL ---";
echo "\nNuevos importados: $inserted";
echo "\nAudios descargados: $audios_dl";
echo "\nYa existentes: $skipped\n";
$conn->close();
