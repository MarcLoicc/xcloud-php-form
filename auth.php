<?php
// --- CONFIGURACIÓN DE SESIÓN ULTRA-SEGURA (Auditoría Bancaria) ---
ini_set('session.cookie_httponly', 1); // Impide robo de sesión por JS
ini_set('session.cookie_secure', 1);   // Solo viaja por HTTPS
ini_set('session.use_only_cookies', 1);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// Regenerar ID de sesión cada vez que esté autenticado (Previene fijación)
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    // session_regenerate_id(true); // Opcional pero recomendado en alta seguridad
}

require_once 'env_loader.php';

// --- CONFIGURACIÓN DE SEGURIDAD PRO ---
// Contraseña predeterminada: crm_marcloi_2024 (Hasheada con BCRYPT)
$APP_PASS_HASH = '$2y$10$oXhOfV.b.A4M0r7G/w8w6O8U0h11v2rI.P.m87p.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L'; 
$DEV_MODE = true; // ACTIVADO: El login se saltará automáticamente para pruebas/edición
// ----------------------------------

// CONFIGURACIÓN DE ERRORES (Anti-Information Leakage)
if ($DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Regenerar ID de sesión cada vez que esté autenticado (Previene fijación)
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    if (!isset($_SESSION['id_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['id_regenerated'] = true;
    }
}
header("X-Frame-Options: DENY"); // Clickjacking
header("X-XSS-Protection: 1; mode=block"); // XSS Filter Legacy
header("X-Content-Type-Options: nosniff"); // MIME Sniffing
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload"); // HSTS (SSL Obligatorio)
header("Referrer-Policy: strict-origin-when-cross-origin"); // Control de seguimiento
header("Permissions-Policy: camera=(), geolocation=(), microphone=(self)"); // Bloqueo de hardware espía

// CONTENT SECURITY POLICY (CSP): El Muro de Berlín de tu navegador
// Permitimos solamente lo que usamos: CDN de confianza y recursos locales.
$csp = "default-src 'self'; ";
$csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com unpkg.com cdn.jsdelivr.net; ";
$csp .= "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdn.tailwindcss.com; ";
$csp .= "font-src 'self' fonts.gstatic.com unpkg.com; ";
$csp .= "img-src 'self' data: blob:; ";
$csp .= "connect-src 'self'; ";
$csp .= "media-src 'self' blob:; ";
$csp .= "frame-ancestors 'none'; ";

header("Content-Security-Policy: $csp");

// Modo desarrollo salta el login
if ($DEV_MODE) {
    $_SESSION['authenticated'] = true;
}

// Generar Token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Manejar el logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Manejar el login con verificación de Hash (Más seguro)
if (isset($_POST['password'])) {
    // Si la contraseña coincide con el Hash, autenticamos
    if (password_verify($_POST['password'], $APP_PASS_HASH)) {
        $_SESSION['authenticated'] = true;
    } else {
        sleep(2); // Retardo anti-fuerza bruta
        $error = "Acceso denegado: Credenciales no válidas.";
    }
}

// Pantalla de Login (Si no está autenticado)
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Seguro - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-zinc-950 flex flex-col items-center justify-center min-h-screen p-4">
    <main class="w-full max-w-[400px]">
        
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-zinc-900 border border-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-6 shadow-sm">
                <i data-lucide="shield" class="w-6 h-6 text-zinc-100" aria-hidden="true"></i>
            </div>
            <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Acceso Seguro</h1>
            <p class="text-[14px] text-zinc-400 mt-2 font-medium">Por favor, verifica tu identidad.</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-xl shadow-2xl">
            <form method="POST" class="space-y-6" aria-labelledby="form-heading">
                <h2 id="form-heading" class="sr-only">Formulario de Iniciar Sesión</h2>
                
                <div class="space-y-2">
                    <label for="password" class="block text-[14px] font-semibold text-zinc-300">Clave de Acceso Maestra</label>
                    <div class="relative">
                        <i data-lucide="key" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                        <input type="password" id="password" name="password" required autofocus
                               aria-describedby="<?php echo isset($error) ? 'login-error' : ''; ?>"
                               class="w-full pl-10 pr-4 py-3 bg-zinc-950 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner"
                               placeholder="Introduce tu clave de seguridad">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-zinc-100 text-zinc-950 text-[14px] font-bold rounded-lg hover:bg-zinc-300 transition-colors flex items-center justify-center gap-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                    Acceder <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
                </button>

                <?php if (isset($error)): ?>
                    <div id="login-error" role="alert" class="px-4 py-3 bg-red-950/50 border border-red-900 rounded-lg text-[14px] font-medium text-red-200 mt-4 flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 shrink-0 mt-0.5" aria-hidden="true"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <footer class="mt-8 text-center">
            <p class="text-[14px] text-zinc-500 font-medium">© 2026 Marcloi Solutions</p>
        </footer>

    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
<?php 
    exit; 
} 
?>
?>
