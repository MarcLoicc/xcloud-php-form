<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Lead - CRM Pro</title>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen">
        <div class="mb-10 max-w-2xl">
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-500 mb-2">Registrar Nuevo Lead</h1>
            <p class="text-gray-400 text-lg">Añade un nuevo prospecto a tu base de datos central en xCloud.</p>
        </div>

        <div class="max-w-2xl bg-dark-card border border-dark-border p-8 rounded-3xl shadow-2xl backdrop-blur-sm">
            <form id="leadForm" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-400 mb-2 ml-1">Nombre Completo</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-indigo-500 text-gray-500 transition-colors">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <input type="text" id="name" name="name" placeholder="Ej: Marc Loic" required
                               class="block w-full pl-12 pr-4 py-4 bg-dark/50 border border-dark-border rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-gray-600 transition-all">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-400 mb-2 ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-indigo-500 text-gray-500 transition-colors">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <input type="email" id="email" name="email" placeholder="nombre@ejemplo.com" required
                               class="block w-full pl-12 pr-4 py-4 bg-dark/50 border border-dark-border rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-gray-600 transition-all">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-400 mb-2 ml-1">Teléfono</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-indigo-500 text-gray-500 transition-colors">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <input type="tel" id="phone" name="phone" placeholder="+34 600 000 000"
                               class="block w-full pl-12 pr-4 py-4 bg-dark/50 border border-dark-border rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-gray-600 transition-all">
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-gray-400 mb-2 ml-1">Mensaje o Nota</label>
                    <div class="relative group">
                        <div class="absolute top-4 left-4 pointer-events-none group-focus-within:text-indigo-500 text-gray-500 transition-colors">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </div>
                        <textarea id="message" name="message" rows="4" placeholder="Alguna nota o detalle del contacto..."
                                  class="block w-full pl-12 pr-4 py-4 bg-dark/50 border border-dark-border rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-gray-600 transition-all"></textarea>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="w-full py-4 px-6 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/20 flex items-center justify-center gap-3 transform active:scale-95 transition-all">
                    <span class="btn-text">Guardar en el Sistema</span>
                    <i data-lucide="save" class="w-5 h-5"></i>
                </button>
            </form>

            <div id="statusMessage" class="hidden mt-6 p-4 rounded-2xl text-center font-semibold"></div>
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
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Guardando...';
            lucide.createIcons();
            statusMessage.classList.add('hidden');

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
                    statusMessage.className = 'mt-6 p-4 rounded-2xl text-center font-semibold bg-green-500/10 text-green-500 border border-green-500/20';
                    form.reset();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                statusMessage.textContent = '❌ Error: ' + error.message;
                statusMessage.className = 'mt-6 p-4 rounded-2xl text-center font-semibold bg-red-500/10 text-red-400 border border-red-500/20';
            } finally {
                statusMessage.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Guardar en el Sistema</span><i data-lucide="save" class="w-5 h-5"></i>';
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
