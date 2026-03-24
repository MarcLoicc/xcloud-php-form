<?php
require_once 'db.php';
header('Content-Type: text/plain');

$leads = [
    ['Anuskasolo', '', '+34648183730', 'anafexmacampoamor@gmail.com', '2026-03-22 10:41:15', 'metaads, arquitectos'],
    ['Gp3.', '', '+34609624485', 'gp3@gp3construcciones.es', '2026-03-22 09:18:17', 'metaads, arquitectos'],
    ['Jose angel', '', '+34651743880', 'jachavez10@hotmail.com', '2026-03-22 00:19:58', 'metaads, arquitectos'],
    ['Bruno', '', '+34696416199', 'brunogcuevas@yahoo.es', '2026-03-21 12:06:14', 'metaads, arquitectos'],
    ['Sergi', '', '+34669379533', 'sergig.arq@gmail.com', '2026-03-20 23:42:16', 'metaads, arquitectos'],
    ['Steve mauricio', '', '+34660635341', 'stevetorres82@hotmail.com', '2026-03-19 16:45:54', 'metaads, arquitectos'],
    ['Manuel', '', '+34649448463', 'm.herrea@muher.com', '2026-03-19 08:01:02', 'metaads, arquitectos'],
    ['Jordi', '', '+34629526040', 'jordirabassa@hotmail.com', '2026-03-19 07:21:01', 'metaads, arquitectos'],
    ['María martín', '', '+34646879451', 'may_1988_5@hotmail.com', '2026-03-19 06:02:14', 'metaads, arquitectos'],
    ['Fernando de', '', '+34615988207', 'artextpaisajismo@yahoo.es', '2026-03-16 17:29:08', 'metaads, arquitectos'],
    ['Palmeral', '', '+34676158050', 'info@palmeralcarpinteria.com', '2026-03-15 11:40:08', 'metaads, arquitectos'],
    ['Eze', '', '+34647412086', 'ezevicente@gmail.com', '2026-03-15 09:40:51', 'metaads, clinicas'],
    ['Alberto', '', '+34662313252', 'proyectos@fordesigner.net', '2026-03-14 17:20:12', 'metaads, arquitectos'],
    ['Gina', '', '+34635211987', 'facturas_grh@mac.com', '2026-03-13 08:25:12', 'metaads, arquitectos'],
    ['Joan', '', '+34622786404', 'joantoledo@gmail.com', '2026-03-13 02:26:05', 'metaads, arquitectos'],
    ['Paco galiñanes', '', '+34626562247', 'fgali.arquitectura@gmail.com', '2026-03-11 08:23:03', 'metaads'],
    ['Antonio', '', '+34667410542', 'anieto@moveglobaltravel.com', '2026-02-02 22:28:56', 'metaads'],
    ['Geometrías altacostura. victoria', '', '+34609655586', 'geometriasaltacostura@gmail.com', '2026-01-31 15:08:35', 'metaads'],
    ['Cesar ruiz', '', '+34667241485', 'cesar1981leganes@gmail.com', '2026-01-28 14:46:19', 'metaads'],
    ['Pepe', '', '+34952667066', 'teleservices.es@gmail.com', '2026-01-22 22:16:47', 'metaads'],
    ['Petar', '', '+34697897807', 'info@oldnewbuilding.com', '2026-01-21 17:57:02', 'metaads'],
    ['Desconocido', '', '', 'info@marcloic.es', '2026-01-19 15:27:44', ''],
    ['Pepa', '', '+34609624805', 'gcagapito@gmail.com', '2026-01-19 15:27:42', 'metaads'],
];

$stmt = $conn->prepare("INSERT INTO leads (name, phone, email, tags, created_at, source, status) VALUES (?, ?, ?, ?, ?, 'pago', 'nuevo')");

foreach ($leads as $l) {
    $fullName = trim($l[0] . ' ' . $l[1]);
    $stmt->bind_param("sssss", $fullName, $l[2], $l[3], $l[5], $l[4]);
    if ($stmt->execute()) {
        echo "Importado: $fullName\n";
    } else {
        echo "Error en $fullName: " . $conn->error . "\n";
    }
}
$stmt->close();
$conn->close();
echo "IMPORTACION FINALIZADA";
?>
