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
                    </thead>                    <tbody class="divide-y divide-border">
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

    <!-- Modal Detalle Lead -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-card border border-border w-full max-w-2xl rounded-2xl shadow-2xl p-8 transform transition-all animate-in zoom-in duration-200">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 id="det-name" class="text-2xl font-bold text-white"></h2>
                    <p id="det-company" class="text-zinc-500 text-sm mt-1"></p>
                </div>
                <button onclick="closeDetailModal()" class="p-2 hover:bg-zinc-800 rounded-xl transition-colors text-zinc-500 hover:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Contacto</label>
                        <div class="space-y-2">
                            <a id="det-email-link" href="#" class="flex items-center gap-3 text-sm text-zinc-300 hover:text-primary transition-colors">
                                <i data-lucide="mail" class="w-4 h-4 text-zinc-500"></i> <span id="det-email"></span>
                            </a>
                            <a id="det-phone-link" href="#" class="flex items-center gap-3 text-sm text-zinc-300 hover:text-primary transition-colors">
                                <i data-lucide="phone" class="w-4 h-4 text-zinc-500"></i> <span id="det-phone"></span>
                            </a>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Web</label>
                        <a id="det-web-link" href="#" target="_blank" class="flex items-center gap-3 text-sm text-zinc-300 hover:text-primary transition-colors">
                            <i data-lucide="link" class="w-4 h-4 text-zinc-500"></i> <span id="det-website"></span>
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Metadatos</label>
                        <div class="flex flex-wrap gap-2" id="det-tags"></div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-1">Inversión Estimada</label>
                        <div class="text-2xl font-bold text-white"><span id="det-price"></span>€</div>
                    </div>
                </div>
            </div>

            <div class="border-t border-border pt-6">
                <label class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest block mb-3">Mensaje Adicional</label>
                <div id="det-message" class="text-zinc-400 text-sm leading-relaxed italic bg-zinc-900/50 p-4 rounded-xl border border-border min-h-[80px]"></div>
            </div>

            <div class="mt-8 flex justify-end">
                <button onclick="closeDetailModal()" class="px-6 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-lg transition-all uppercase tracking-widest">
                    Cerrar
                </button>
            </div>
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

        // Funciones del Modal de Detalle
        const modalDetail = document.getElementById('detailModal');

        function showLeadDetails(data) {
            document.getElementById('det-name').textContent = data.name;
            document.getElementById('det-company').textContent = data.company ? data.company : 'Cliente Particular';
            
            document.getElementById('det-email').textContent = data.email;
            document.getElementById('det-email-link').href = 'mailto:' + data.email;
            
            document.getElementById('det-phone').textContent = data.phone;
            document.getElementById('det-phone-link').href = 'tel:' + data.phone;
            
            document.getElementById('det-website').textContent = data.website ? data.website : 'N/A';
            document.getElementById('det-web-link').href = data.website ? (data.website.startsWith('http') ? data.website : 'https://' + data.website) : '#';
            
            const price = parseFloat(data.proposal_price || 0).toLocaleString('es-ES', { minimumFractionDigits: 2 });
            document.getElementById('det-price').textContent = price;
            
            document.getElementById('det-message').textContent = (data.message && data.message.trim() !== '') ? data.message : 'Sin mensaje adicional.';

            const tagsCont = document.getElementById('det-tags');
            tagsCont.innerHTML = '';
            if (data.tags) {
                data.tags.split(',').forEach(tag => {
                    const span = document.createElement('span');
                    span.className = 'px-2 py-0.5 bg-zinc-800 text-zinc-400 text-[9px] font-semibold rounded border border-border uppercase';
                    span.textContent = tag.trim();
                    tagsCont.appendChild(span);
                });
            } else {
                tagsCont.innerHTML = '<span class="text-zinc-700 text-[10px]">Sin etiquetas</span>';
            }

            modalDetail.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function closeDetailModal() {
            modalDetail.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        modalDetail.addEventListener('click', (e) => {
            if (e.target === modalDetail) closeDetailModal();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
