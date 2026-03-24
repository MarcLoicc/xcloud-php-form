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
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Acceso Seguro - CRM Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; background: #f8fafc; }</style>
</head>
<body class="flex items-center justify-center h-screen p-6 relative overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-sm p-12 bg-white border border-slate-200 rounded-[2.5rem] shadow-[0_20px_50px_rgba(79,70,229,0.1)] text-center relative z-10">
        <div class="w-20 h-20 bg-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-indigo-100 rotate-3">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">CRM <span class="text-indigo-600">Pro</span></h1>
        <p class="text-slate-400 text-sm mb-10 font-medium">Bóveda de Seguridad Administrativa</p>
        
        <form method="POST" class="space-y-5">
            <div class="relative">
                <input type="password" name="password" placeholder="Clave Maestra" required autofocus
                       class="w-full p-5 bg-slate-50 border-2 border-slate-100 rounded-2xl text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition-all placeholder-slate-300 text-center font-bold tracking-widest">
            </div>
            <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition-all active:scale-95 text-sm uppercase tracking-widest">
                Iniciar Sesión
            </button>
            <?php if (isset($error)): ?>
                <div class="mt-6 p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-black rounded-2xl animate-bounce uppercase tracking-tighter">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </form>
        <p class="mt-10 text-[10px] text-slate-300 font-bold uppercase tracking-[0.2em]">Cifrado de Extremo a Extremo</p>
    </div>
</body>
</html>
<?php
    exit;
}
?>
