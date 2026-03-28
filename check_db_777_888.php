<?php
require_once 'db.php';
header('Content-Type: text/plain');
$res = $conn->query("DESCRIBE leads");
while($row = $res->fetch_assoc()) {
    foreach($row as $k=>$v) echo "$k: $v | ";
    echo "\n";
}
