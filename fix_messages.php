<?php
require_once 'db.php';

function cleanHtmlMessage($html) {
    if (!$html) return '';
    $search = ['</h3>', '</tr>', '</td>', '</div>', '<br>', '<br/>', '<br />'];
    $replace = ["\n", "\n", " ", "\n", "\n", "\n", "\n"];
    $text = str_replace($search, $replace, $html);
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace("/\n\s+/", "\n", $text); 
    $text = preg_replace("/\n{3,}/", "\n\n", $text); 
    return trim($text);
}

echo "Limpiando mensajes de la base de datos...\n";

$res = $conn->query("SELECT id, message FROM leads");
$count = 0;

while ($row = $res->fetch_assoc()) {
    $clean = cleanHtmlMessage($row['message']);
    if ($clean !== $row['message']) {
        $stmt = $conn->prepare("UPDATE leads SET message = ? WHERE id = ?");
        $stmt->bind_param("si", $clean, $row['id']);
        $stmt->execute();
        $count++;
    }
}

echo "¡Listo! Se han limpiado $count mensajes.\n";
