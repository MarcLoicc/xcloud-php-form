<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bloqueo total para buscadores e IA -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <!-- Evitar caché en el navegador -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Nuevo Lead - xCloud PHP</title>
    <!-- Versionado del CSS para forzar actualización -->
    <link rel="stylesheet" href="style.css?v=1.0.1">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="container animate-in">
        <header>
            <div class="logo">
                <i data-lucide="database" class="icon-primary"></i>
            </div>
            <h1>Nuevo Lead</h1>
            <p>Registra prospectos directamente en MariaDB (xCloud)</p>
        </header>

        <form id="leadForm">
            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <div class="input-wrapper">
                    <i data-lucide="user"></i>
                    <input type="text" id="name" name="name" placeholder="Ej: Marc Loic" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <div class="input-wrapper">
                    <i data-lucide="mail"></i>
                    <input type="email" id="email" name="email" placeholder="nombre@ejemplo.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Teléfono</label>
                <div class="input-wrapper">
                    <i data-lucide="phone"></i>
                    <input type="tel" id="phone" name="phone" placeholder="+34 600 000 000">
                </div>
            </div>

            <div class="form-group">
                <label for="message">Mensaje (Opcional)</label>
                <div class="input-wrapper">
                    <i data-lucide="message-square"></i>
                    <textarea id="message" name="message" rows="3" placeholder="Alguna nota..."></textarea>
                </div>
            </div>

            <button type="submit" id="submitBtn">
                <span class="btn-text">Enviar Datos</span>
                <i data-lucide="send" class="btn-icon"></i>
            </button>
        </form>

        <div id="statusMessage" class="status-message"></div>
        
        <div class="footer">
            <a href="leads.php" class="view-leads-link">📊 Ver Lista de Leads</a>
            <a href="?logout=1" class="logout-btn">Salir 🔒</a>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const form = document.getElementById('leadForm');
        const submitBtn = document.getElementById('submitBtn');
        const statusMessage = document.getElementById('statusMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // UI Feedback
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Enviando...';
            statusMessage.style.display = 'none';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('insert.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (result.status === 'success') {
                    statusMessage.textContent = '✅ ' + result.message;
                    statusMessage.className = 'status-message status-success';
                    form.reset();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                statusMessage.textContent = '❌ Error: ' + error.message;
                statusMessage.className = 'status-message status-error';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Enviar Datos</span><i data-lucide="send"></i>';
                lucide.createIcons();
                statusMessage.style.display = 'block';
            }
        });
    </script>

    <style>
        .icon-primary { color: var(--primary-color); width: 48px; height: 48px; margin: 0 auto 1.5rem; display: block; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-wrapper i { position: absolute; left: 1rem; color: var(--text-muted); width: 18px; height: 18px; }
        .input-wrapper input, .input-wrapper textarea { padding-left: 2.8rem; }
        .view-leads-link { color: var(--text-muted); text-decoration: none; font-size: 0.9rem; margin-top: 1.5rem; display: block; text-align: center; }
        .view-leads-link:hover { color: white; }
        .status-error { background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; }
        .animate-in { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</body>
</html>
