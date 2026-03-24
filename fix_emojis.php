<?php
require_once 'db.php';
header('Content-Type: text/plain');

// Arreglar la tabla para soportar Emojis (utf8mb4)
$queries = [
    "ALTER DATABASE {$_ENV['DB_NAME']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "ALTER TABLE leads CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "ALTER TABLE leads MODIFY message TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "ÉXITO: $q\n";
    } else {
        echo "ERROR: " . $conn->error . " en la consulta: $q\n";
    }
}

echo "\nTABLA ACTUALIZADA PARA EMOJIS";
$conn->close();
?>
