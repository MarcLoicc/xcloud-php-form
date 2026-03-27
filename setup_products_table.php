<?php
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: text/plain; charset=utf-8');

// 1. Crear tabla
$conn->query("CREATE TABLE IF NOT EXISTS ga4_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_path VARCHAR(500) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    has_2025_history TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

echo "✅ Tabla ga4_products creada o ya existente.\n";

// 2. Insertar productos existentes (hardcoded → DB)
$products = [
    ['/', 'Home / General', 1],
    ['/contacto/', 'Contacto', 1],
    ['/diseno-web-mostoles/', 'Móstoles', 1],
    ['/diseno-web-para-clinicas-en-madrid/diseno-web-para-clinicas-capilares/', 'Clínicas Capilares', 1],
    ['/diseno-web-para-clinicas-en-madrid/diseno-web-para-dentistas-y-clinicas-dentales-en-madrid/', 'Dentistas Madrid', 1],
    ['/diseno-web-para-abogados/', 'Abogados', 1],
    ['/diseno-web-para-escuelas-y-centros-educativos-en-madrid/', 'Escuelas', 1],
    ['/diseno-web-para-concesionarios-en-madrid/', 'Concesionarios', 1],
    ['/diseno-web-para-gimnasios-y-estudios-de-yoga-en-madrid/', 'Gimnasios', 1],
    ['/diseno-de-paginas-web-para-restaurantes/', 'Restaurantes', 1],
    ['/diseno-web-para-farmacias-en-madrid/', 'Farmacias', 1],
    ['/diseno-web-en-alcobendas/', 'Alcobendas', 1],
    ['/diseno-web-en-villaviciosa-de-odon/', 'Villaviciosa', 1],
    ['/diseno-web-en-tres-cantos/', 'Tres Cantos', 1],
    ['/diseno-web-en-collado-de-villalba/', 'Collado Villalba', 1],
    ['/diseno-web-aranjuez/', 'Aranjuez', 1],
    ['/diseno-web-en-arganda-del-rey/', 'Arganda', 1],
    ['/diseno-web-en-leganes/', 'Leganés', 1],
    ['/diseno-web-en-alcorcon/', 'Alcorcón', 1],
    ['/diseno-web-en-alcala-de-henares/', 'Alcalá Henares', 1],
    ['/diseno-web-para-clinicas-en-madrid/', 'Clínicas Madrid', 1],
    ['/diseno-tienda-online-madrid/', 'Tienda Online', 1],
    ['/calculadora-precio-web-online/', 'Calculadora Precio', 1],
];

$stmt = $conn->prepare("INSERT IGNORE INTO ga4_products (page_path, name, has_2025_history) VALUES (?, ?, ?)");
$inserted = 0;
foreach ($products as [$path, $name, $hist]) {
    $stmt->bind_param('ssi', $path, $name, $hist);
    $stmt->execute();
    if ($conn->affected_rows > 0) $inserted++;
}

echo "✅ $inserted productos insertados (los duplicados fueron ignorados).\n";
echo "\n🎉 Migración completada. Ya puedes eliminar este archivo del servidor.\n";
