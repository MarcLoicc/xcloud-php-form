<?php
require_once 'db.php';
header('Content-Type: text/plain');

echo "MANTENIMIENTO: Organizando archivos adjuntos...\n";

$res = $conn->query("SELECT id, name, file_path, audio_path, created_at FROM leads WHERE file_path != '' OR audio_path != ''");
$total = 0;

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777, true);

while ($lead = $res->fetch_assoc()) {
    $id = $lead['id'];
    $cleanName = preg_replace('/[^A-Za-z0-9]/', '_', $lead['name']);
    $datePart = date('Y-m-d', strtotime($lead['created_at']));
    
    // 1. Renombrar ARCHIVO/DOCUMENTO
    if (!empty($lead['file_path']) && file_exists($lead['file_path'])) {
        $ext = strtolower(pathinfo($lead['file_path'], PATHINFO_EXTENSION));
        $newName = "DOC_{$cleanName}_{$datePart}_{$id}.{$ext}";
        $newPath = $upload_dir . $newName;
        
        if ($lead['file_path'] !== $newPath) {
            if (rename($lead['file_path'], $newPath)) {
                $conn->query("UPDATE leads SET file_path = '$newPath' WHERE id = $id");
                echo "DOC: {$lead['file_path']} -> $newName (ID $id)\n";
                $total++;
            }
        }
    }

    // 2. Renombrar AUDIO
    if (!empty($lead['audio_path']) && file_exists($lead['audio_path'])) {
        $ext = strtolower(pathinfo($lead['audio_path'], PATHINFO_EXTENSION));
        $newName = "AUDIO_{$cleanName}_{$datePart}_{$id}.{$ext}";
        $newPath = $upload_dir . $newName;
        
        if ($lead['audio_path'] !== $newPath) {
            if (rename($lead['audio_path'], $newPath)) {
                $conn->query("UPDATE leads SET audio_path = '$newPath' WHERE id = $id");
                echo "AUDIO: {$lead['audio_path']} -> $newName (ID $id)\n";
                $total++;
            }
        }
    }
}

echo "\nTRABAJO COMPLETADO. Archivos organizados: $total";
$conn->close();
?>
