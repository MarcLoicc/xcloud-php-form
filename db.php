<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<?php
require_once 'env_loader.php';

// Cargamos de forma segura desde el .env
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

// CREACIÓN DE TABLA Y COLUMNAS EXTENDIDAS
$tableQuery = "CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    company VARCHAR(255),
    website VARCHAR(255),
    source ENUM('organico', 'pago') DEFAULT 'organico',
    tags VARCHAR(255),
    proposal_price DECIMAL(10, 2) DEFAULT 0.00,
    file_path VARCHAR(555),
    audio_path VARCHAR(555),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableQuery);

// Migración y Ajuste de esquema dinámico
$columns_to_add = [
    'company' => "VARCHAR(255)",
    'website' => "VARCHAR(255)",
    'source' => "ENUM('organico', 'pago') DEFAULT 'organico'",
    'tags' => "VARCHAR(255)",
    'proposal_price' => "DECIMAL(10, 2) DEFAULT 0.00",
    'file_path' => "VARCHAR(555)",
    'audio_path' => "VARCHAR(555)",
    'status' => "ENUM('nuevo','no_responde','llamar_tarde','enviar_propuesta','propuesta_enviada','ganado','perdido','no_cualificado','interesado_tarde') DEFAULT 'nuevo'"
];

foreach ($columns_to_add as $col => $type) {
    $check = $conn->query("SHOW COLUMNS FROM leads LIKE '$col'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE leads ADD COLUMN $col $type");
    }
}

// Asegurarse de que el email ya no sea obligatorio
$conn->query("ALTER TABLE leads MODIFY COLUMN email VARCHAR(255) NULL");

// TABLA DE CONFIGURACIÓN / SETTINGS
$conn->query("CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Inicializar GA4 Property ID si no existe
$conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('ga4_property_id', 'PROPIEDAD_AQUI')");

?>
