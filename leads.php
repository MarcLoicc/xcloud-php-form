<?php require_once 'auth.php'; ?>
<?php
date_default_timezone_set('Europe/Madrid');
require_once 'db.php';
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Datos Leads - CRM Blue Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-8 min-h-screen bg-bg">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-white tracking-tight">Leads</h1>
                <p class="text-zinc-500 text-sm mt-1">Gestión y seguimiento de prospectos comerciales.</p>
            </div>
            <button onclick="toggleModal()" class="px-5 py-2.5 bg-primary hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Crear Lead
            </button>
        </header>

        <!-- Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
            <div class="bg-card border border-border p-4 rounded-xl flex items-center justify-between">
                <div class="relative w-full md:w-96 group">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-zinc-500 group-focus-within:text-primary transition-colors"></i>
                    <input type="text" id="searchLeadInput" placeholder="Buscar por nombre, etiquetas..." 
                           class="w-full pl-10 pr-4 py-2 bg-bg border border-border rounded-lg focus:ring-1 focus:ring-primary focus:border-primary text-white text-sm placeholder-zinc-600 transition-all outline-none">
                </div>
                <div class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                    <span id="visibleLeadsCount" class="text-white"><?php echo $result->num_rows; ?></span> Resultados
                </div>
            </div>
        </div>

        <div class="bg-card border border-border rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-900/50 text-[11px] font-semibold uppercase tracking-wider text-zinc-500 border-b border-border">
                            <th class="px-6 py-4">Lead / Empresa</th>
                            <th class="px-6 py-4 text-center">Fuente</th>
                            <th class="px-6 py-4 text-center">Etiquetas</th>
                            <th class="px-6 py-4 text-right">Fecha</th>
                            <th class="px-6 py-4 text-right">Propuesta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <?php while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="lead-row hover:bg-zinc-900/40 transition-all group cursor-pointer" onclick="showLeadDetails(<?php echo $json_data; ?>)">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-white group-hover:text-primary transition-colors"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="text-xs text-zinc-500 mt-0.5 flex items-center gap-1.5 uppercase tracking-tighter">
                                        <?php echo $row['company'] ? htmlspecialchars($row['company']) : 'Particular'; ?>
                                        <?php if($row['website']): ?>
                                            &middot; <?php echo parse_url($row['website'], PHP_URL_HOST) ?: $row['website']; ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <?php if($row['source'] == 'pago'): ?>
                                    <span class="px-2 py-0.5 bg-amber-500/10 text-amber-500 text-[10px] font-bold rounded-md border border-amber-500/10">PAGO</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold rounded-md border border-primary/10">ORGÁNICO</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-wrap justify-center gap-1.5">
                                    <?php 
                                    $tags = !empty($row['tags']) ? explode(', ', $row['tags']) : [];
                                    foreach($tags as $tag): 
                                        $tagName = ($tag == 'metaads') ? 'Metaads' : ucfirst($tag);
                                    ?>
                                        <span class="px-2 py-0.5 bg-zinc-800 text-zinc-400 text-[9px] font-semibold rounded border border-border uppercase"><?php echo $tagName; ?></span>
                                    <?php endforeach; if(empty($tags)) echo '<span class="text-zinc-800 text-[10px]">&mdash;</span>'; ?>
                                 </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="text-xs text-zinc-300 font-medium block tracking-tight"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></span>
                                <span class="text-[10px] text-zinc-600 block mt-0.5 uppercase tracking-tighter"><?php echo date('H:i', strtotime($row['created_at'])); ?></span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-white tracking-tight">
                                    <?php echo number_format($row['proposal_price'], 2, ',', '.'); ?>€
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if ($result->num_rows == 0): ?>
                    <div class="p-24 text-center text-zinc-700 select-none">
                        <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 opacity-10"></i>
                        <p class="font-bold text-sm uppercase tracking-widest">Sin resultados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Detalle/Edición Lead -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-card border border-border w-full max-w-2xl rounded-2xl shadow-2xl p-8 transform transition-all animate-in zoom-in duration-200">
            <form id="editLeadForm">
                <input type="hidden" name="id" id="det-id">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-full mr-4">
                        <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Nombre completo / Empresa</label>
                        <input type="text" name="name" id="det-name" class="w-full bg-transparent border-b border-transparent hover:border-zinc-800 focus:border-primary focus:outline-none text-2xl font-bold text-white transition-all px-0 py-1">
                        <input type="text" name="company" id="det-company" class="w-full bg-transparent border-b border-transparent hover:border-zinc-800 focus:border-primary focus:outline-none text-zinc-500 text-sm mt-1 px-0 py-1" placeholder="Empresa (opcional)">
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-2 hover:bg-zinc-800 rounded-xl transition-colors text-zinc-500 hover:text-white">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Email de contacto</label>
                            <div class="relative group">
                                <i data-lucide="mail" class="w-4 h-4 absolute left-0 top-3 text-zinc-500 group-focus-within:text-primary transition-colors"></i>
                                <input type="email" name="email" id="det-email" class="w-full pl-6 bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-sm py-2 text-zinc-300">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Teléfono</label>
                            <div class="relative group">
                                <i data-lucide="phone" class="w-4 h-4 absolute left-0 top-3 text-zinc-500 group-focus-within:text-primary transition-colors"></i>
                                <input type="text" name="phone" id="det-phone" class="w-full pl-6 bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-sm py-2 text-zinc-300">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Sitio Web</label>
                            <div class="relative group">
                                <i data-lucide="link-2" class="w-4 h-4 absolute left-0 top-3 text-zinc-500 group-focus-within:text-primary transition-colors"></i>
                                <input type="text" name="website" id="det-website" class="w-full pl-6 bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-sm py-2 text-zinc-300">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Etiquetas (separadas por comas)</label>
                            <input type="text" name="tags" id="det-tags" class="w-full bg-zinc-900 border border-border rounded-lg text-xs py-2 px-3 text-zinc-400 focus:border-primary focus:outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Fuente</label>
                            <select name="source" id="det-source" class="w-full bg-zinc-900 border border-border rounded-lg text-xs py-2 px-3 text-zinc-400 focus:border-primary focus:outline-none">
                                <option value="organico">Orgánico</option>
                                <option value="pago">Pago</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Inversión / Presupuesto</label>
                            <div class="relative group">
                                <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full pl-2 bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-2xl font-bold text-white py-1">
                                <span class="absolute right-0 bottom-1 text-zinc-500 font-bold">€</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-border pt-6">
                    <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-3">Mensaje Adicional</label>
                    <textarea name="message" id="det-message" class="w-full bg-zinc-900/50 border border-border rounded-xl p-4 text-sm text-zinc-300 focus:border-primary focus:outline-none italic min-h-[100px]"></textarea>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <span id="saveStatus" class="text-xs text-green-500 hidden animate-pulse font-bold tracking-widest uppercase">Guardando...</span>
                    <div class="flex gap-3 ml-auto">
                        <button type="button" onclick="closeDetailModal()" class="px-6 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-lg transition-all uppercase tracking-widest">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2 bg-primary hover:bg-blue-500 text-white text-xs font-bold rounded-lg transition-all uppercase tracking-widest shadow-lg shadow-primary/20">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        // Lógica de filtrado
        const searchInput = document.getElementById('searchLeadInput');
        const rows = document.querySelectorAll('.lead-row');
        const counter = document.getElementById('visibleLeadsCount');

        if(searchInput) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.toLowerCase();
                let visibleCount = 0;
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const isVisible = text.includes(query);
                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) visibleCount++;
                });
                if(counter) counter.textContent = visibleCount;
            });
        }

        // Funciones del Modal
        const modalDetail = document.getElementById('detailModal');
        const editForm = document.getElementById('editLeadForm');
        const saveStatus = document.getElementById('saveStatus');

        function showLeadDetails(data) {
            document.getElementById('det-id').value = data.id;
            document.getElementById('det-name').value = data.name;
            document.getElementById('det-company').value = data.company || '';
            document.getElementById('det-email').value = data.email || '';
            document.getElementById('det-phone').value = data.phone || '';
            document.getElementById('det-website').value = data.website || '';
            document.getElementById('det-tags').value = data.tags || '';
            document.getElementById('det-source').value = data.source || 'organico';
            document.getElementById('det-price').value = data.proposal_price || 0;
            document.getElementById('det-message').value = data.message || '';

            modalDetail.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function closeDetailModal() {
            modalDetail.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveStatus.classList.remove('hidden');
            
            const formData = new FormData(this);
            fetch('update_lead.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    saveStatus.textContent = "¡LISTO!";
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('Error: ' + data.message);
                    saveStatus.classList.add('hidden');
                }
            })
            .catch(err => {
                alert('Error de conexión');
                saveStatus.classList.add('hidden');
            });
        });

        modalDetail.addEventListener('click', (e) => {
            if (e.target === modalDetail) closeDetailModal();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
