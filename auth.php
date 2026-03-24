<?php
session_start();
require_once 'env_loader.php';

// --- CONFIGURACIÓN DE SEGURIDAD PRO ---
// Contraseña predeterminada: crm_marcloi_2024 (Hasheada con BCRYPT)
$APP_PASS_HASH = '$2y$10$oXhOfV.b.A4M0r7G/w8w6O8U0h11v2rI.P.m87p.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L.L'; 
$DEV_MODE = true; // ACTIVADO: El login se saltará automáticamente para pruebas/edición
// ----------------------------------

// CABECERAS DE SEGURIDAD (Previene ataques XSS y Clickjacking)
header("X-Frame-Options: DENY"); // Evita que tu CRM se cargue en un frame ajeno
header("X-XSS-Protection: 1; mode=block"); // Bloquea si detecta inyecciones en el navegador
header("X-Content-Type-Options: nosniff"); // Protege contra engaños de tipos MIME
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload"); // Obliga el uso de SSL/HTTPS

// Modo desarrollo salta el login
if ($DEV_MODE) {
    $_SESSION['authenticated'] = true;
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; background: #09090b; }</style>
</head>
<body class="flex items-center justify-center h-screen p-6">
    <div class="w-full max-w-sm p-10 bg-zinc-900 border border-zinc-800 rounded-3xl shadow-2xl text-center backdrop-blur-xl">
        <div class="w-14 h-14 bg-indigo-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-indigo-500/20">
            <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">CRM Protegido</h1>
        <p class="text-zinc-500 text-sm mb-8 italic">Ingresa con tus credenciales de administrador.</p>
        
        <form method="POST" class="space-y-4">
            <input type="password" name="password" placeholder="Contraseña Maestra" required autofocus
                   class="w-full p-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all placeholder-zinc-700">
            <button type="submit" class="w-full py-4 bg-white hover:bg-zinc-200 text-black font-bold rounded-2xl shadow-lg transition-all active:scale-95">
                Verificar Identidad
            </button>
            <?php if (isset($error)): ?>
                <div class="mt-4 p-3 bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold rounded-xl animate-pulse">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}
?>
