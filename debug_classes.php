<?php
require_once 'vendor/autoload.php';
$classes = array_filter(get_declared_classes(), function($c) {
    return strpos($c, 'Google\\Analytics\\Data\\V1beta\\') === 0;
});
echo "Declared classes:\n";
foreach($classes as $c) echo "$c\n";
?>
