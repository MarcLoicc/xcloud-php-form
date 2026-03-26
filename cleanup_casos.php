<?php
require_once 'db.php';
$path = '/casos-de-exito-diseno-web/';
$sql = "DELETE FROM ga4_history_2025 WHERE page_path = '$path'";
if ($conn->query($sql)) {
    echo "Eliminados los registros de $path de la base de datos.<br>";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
