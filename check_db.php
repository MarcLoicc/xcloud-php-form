<?php
require_once 'db.php';
$res = $conn->query("SHOW COLUMNS FROM leads");
$cols = [];
while($row = $res->fetch_assoc()) {
    $cols[] = $row['Field'];
}
echo json_encode($cols);
$conn->close();
