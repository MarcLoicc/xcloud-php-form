<?php
require_once 'auth.php';
require_once 'db.php';

$result = $conn->query("SELECT id, name FROM leads");
$updated = 0;
$errors = 0;

echo "<pre>";
while ($row = $result->fetch_assoc()) {
    $newName = mb_convert_case(trim($row['name']), MB_CASE_TITLE, 'UTF-8');
    if ($newName !== $row['name']) {
        $stmt = $conn->prepare("UPDATE leads SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $newName, $row['id']);
        if ($stmt->execute()) {
            echo "✅ [{$row['id']}] '{$row['name']}' → '{$newName}'\n";
            $updated++;
        } else {
            echo "❌ Error en ID {$row['id']}: " . $conn->error . "\n";
            $errors++;
        }
        $stmt->close();
    }
}

echo "\n--- Resumen ---\n";
echo "Actualizados: $updated\n";
echo "Errores: $errors\n";
echo "</pre>";
$conn->close();
