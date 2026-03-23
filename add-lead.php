<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Lead - CRM MarcLoic</title>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <h1>Registrar Nuevo Lead</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Añade un nuevo prospecto a tu base de datos central de xCloud.</p>

        <div class="glass-panel" style="max-width: 600px;">
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
                        <textarea id="message" name="message" rows="3" placeholder="Descripción o nota del contacto..."></textarea>
                    </div>
                </div>

                <button type="submit" id="submitBtn">
                    <span class="btn-text">Guardar en Sistema</span>
                    <i data-lucide="save" class="btn-icon"></i>
                </button>
            </form>

            <div id="statusMessage" class="status-message"></div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const form = document.getElementById('leadForm');
        const submitBtn = document.getElementById('submitBtn');
        const statusMessage = document.getElementById('statusMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Guardando...';
            statusMessage.style.display = 'none';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('insert.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
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
                submitBtn.innerHTML = '<span>Guardar en Sistema</span><i data-lucide="save"></i>';
                lucide.createIcons();
                statusMessage.style.display = 'block';
            }
        });
    </script>
</body>
</html>
