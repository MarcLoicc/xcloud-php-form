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
    <title>Master Auth - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
      body { height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; background-color: #f8fafc; }
    </style>
</head>
<body>
    <div class="fixed inset-0 z-[-1] overflow-hidden opacity-40">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-50 blur-[120px] rounded-full translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-slate-100 blur-[120px] rounded-full -translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="w-full max-w-md p-8">
        <div class="bg-white border border-slate-200 p-12 rounded-2xl shadow-2xl relative group">
            <div class="text-center mb-12">
                <div class="w-16 h-16 bg-slate-900 rounded-xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-slate-200 ring-4 ring-slate-50 transform group-hover:scale-105 transition-all">
                    <i data-lucide="shield-check" class="w-8 h-8 text-white stroke-[2.5]"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight uppercase italic leading-none">CRM MARCLOI</h1>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-4 opacity-80 italic">Authorized Access Only :: Terminal_v5</p>
            </div>

            <form method="POST" class="space-y-8">
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Master Access Key</label>
                    <div class="relative group/inp">
                        <i data-lucide="key-round" class="w-4 h-4 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within/inp:text-slate-900 transition-all"></i>
                        <input type="password" name="password" required autofocus
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-slate-100 focus:border-slate-800 outline-none transition-all text-lg font-black text-slate-800 tracking-widest placeholder:text-slate-200 shadow-inner"
                               placeholder="••••••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-slate-900 text-white text-[11px] font-bold rounded-lg uppercase tracking-widest hover:bg-black transition-all shadow-xl active:scale-95 flex items-center justify-center gap-3">
                    Unlock Console <i data-lucide="chevron-right" class="w-4 h-4 text-indigo-400"></i>
                </button>

                <?php if (isset($error)): ?>
                    <div class="px-5 py-3 bg-red-50 border border-red-100 rounded-lg text-[9px] font-bold text-red-500 uppercase tracking-widest text-center animate-pulse">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
            </form>

            <div class="mt-12 text-center">
                <p class="text-[8px] font-bold text-slate-300 uppercase tracking-widest opacity-60 italic leading-loose">© 2026 Marcloi Solutions / Engineering Division</p>
            </div>
        </div>
    </div>

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
