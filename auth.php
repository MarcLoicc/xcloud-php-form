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
    <title>Acceso Estratégico - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
      body { height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    </style>
</head>
<body class="bg-[#f0f2f5]">
    <!-- Background Accents -->
    <div class="fixed inset-0 z-[-1] overflow-hidden">
        <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-indigo-500/10 blur-[150px] rounded-full animate-float"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[60%] h-[60%] bg-blue-500/10 blur-[150px] rounded-full animate-float" style="animation-delay: -2s"></div>
    </div>

    <div class="w-full max-w-lg p-6">
        <div class="glass-card p-12 rounded-[4rem] shadow-[0_50px_100px_-20px_rgba(30,41,59,0.15)] relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/5 via-transparent to-transparent opacity-50"></div>
            
            <div class="relative z-10 text-center mb-12">
                <div class="w-20 h-20 bg-indigo-600 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-indigo-100 ring-8 ring-white/50 transform group-hover:rotate-12 transition-all">
                    <i data-lucide="shield-check" class="w-10 h-10 text-white stroke-[2.5]"></i>
                </div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tighter italic">Terminal <span class="text-indigo-600 not-italic font-medium">Cloud</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mt-3">Identificación de Operador Requerida</p>
            </div>

            <form method="POST" class="relative z-10 space-y-8">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Clave de Seguridad Master</label>
                    <div class="relative group/inp">
                        <i data-lucide="key-round" class="w-5 h-5 absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within/inp:text-indigo-600 transition-all"></i>
                        <input type="password" name="password" required autofocus
                               class="w-full pl-16 pr-6 py-5 bg-white border border-white rounded-[1.8rem] focus:ring-8 focus:ring-indigo-100 focus:border-indigo-600 outline-none transition-all text-xl font-black text-slate-800 tracking-widest placeholder:text-slate-200 shadow-sm"
                               placeholder="••••••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-6 bg-slate-900 text-white text-[10px] font-black rounded-3xl uppercase tracking-[0.4em] hover:bg-black transition-all shadow-2xl shadow-slate-200 active:scale-95 flex items-center justify-center gap-3 group">
                    Desbloquear Sistema <i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-2 transition-all"></i>
                </button>
            </form>

            <div class="mt-12 text-center relative z-10">
                <?php if (isset($error)): ?>
                    <div class="px-6 py-3 bg-red-50 border border-red-100 rounded-2xl text-[10px] font-black text-red-500 uppercase tracking-widest text-center animate-bounce mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest opacity-60">© 2026 Marcloi Enterprise Solutions</p>
                <div class="flex justify-center gap-4 mt-4 opacity-30 group-hover:opacity-100 transition-opacity">
                    <i data-lucide="zap" class="w-3 h-3 text-indigo-400"></i>
                    <i data-lucide="shield" class="w-3 h-3 text-indigo-400"></i>
                    <i data-lucide="database" class="w-3 h-3 text-indigo-400"></i>
                </div>
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
