<?php
session_start();

// --- CONFIGURACIÓN DE SEGURIDAD ---
$APP_PASSWORD = 'crm_marcloi_2024';
$DEV_MODE = true; // CAMBIAR A 'false' PARA ACTIVAR PASSWORD NUEVAMENTE
// ----------------------------------

// Si está en modo desarrollo, forzamos la sesión autenticada
if ($DEV_MODE) {
    $_SESSION['authenticated'] = true;
}

// Manejar el logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Manejar el login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $APP_PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        $error = "Contraseña incorrecta";
    }
}

// Si no está autenticado, mostramos la pantalla de login
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Acceso Privado - CRM Marcloic</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #6366f1; --bg-color: #0f172a; --card-bg: #1e293b; --text: #f8fafc; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg-color); color: var(--text); display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: var(--card-bg); padding: 2.5rem; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); width: 100%; max-width: 400px; text-align: center; border: 1px solid rgba(255,255,255,0.1); }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        p { color: #94a3b8; font-size: 0.9rem; margin-bottom: 2rem; }
        input[type="password"] { width: 100%; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #334155; background: #0f172a; color: white; margin-bottom: 1rem; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: var(--primary-color); border: none; border-radius: 0.5rem; color: white; font-weight: 600; cursor: pointer; transition: opacity 0.2s; }
        button:hover { opacity: 0.9; }
        .error { color: #ef4444; font-size: 0.85rem; margin-top: 1rem; }
        .lock-icon { width: 50px; height: 50px; background: rgba(99, 102, 241, 0.1); color: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 24px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="lock-icon">🔒</div>
        <h1>Acceso Privado</h1>
        <p>Solo personal autorizado puede acceder a este CRM.</p>
        <form method="POST">
            <input type="password" name="password" placeholder="Introduce la contraseña" required autofocus>
            <button type="submit">Entrar</button>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}
?>
