<?php require_once 'auth.php'; ?>
<?php
date_default_timezone_set('Europe/Madrid');
require_once 'db.php';
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");

// Recopilar etiquetas existentes para sugerencias
$existingTags = [];
$tagQuery = $conn->query("SELECT DISTINCT tags FROM leads WHERE tags IS NOT NULL AND tags != ''");
if ($tagQuery) {
    while ($tRow = $tagQuery->fetch_assoc()) {
        foreach (explode(',', $tRow['tags']) as $p) {
            $tag = trim($p);
            if (!empty($tag) && !in_array($tag, $existingTags)) $existingTags[] = $tag;
        }
    }
}
sort($existingTags);
?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads - CRM Blue Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
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

        <div class="bg-card border border-border p-4 rounded-xl flex items-center justify-between mb-6">
            <div class="relative w-full md:w-96 group">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-zinc-500 group-focus-within:text-primary transition-colors"></i>
                <input type="text" id="searchLeadInput" placeholder="Filtrar por nombre, etiquetas..." 
                       class="w-full pl-10 pr-4 py-2 bg-bg border border-border rounded-lg focus:ring-1 focus:ring-primary outline-none text-white text-sm placeholder-zinc-600 transition-all">
            </div>
            <div class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                <span id="visibleLeadsCount" class="text-white"><?php echo $result->num_rows; ?></span> Resultados
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
                        <tr class="lead-row hover:bg-zinc-900/40 transition-all group cursor-pointer" onclick='showLeadDetails(<?php echo $json_data; ?>)'>
                            <td class="px-6 py-4">
                                <div class="flex flex-col text-sm">
                                    <span class="font-semibold text-white group-hover:text-primary transition-colors"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="text-xs text-zinc-500 mt-0.5 flex items-center gap-1.5 uppercase tracking-tighter">
                                        <?php echo $row['company'] ?: 'Particular'; ?>
                                        <?php if($row['website']): ?>
                                            &middot; <?php echo parse_url($row['website'], PHP_URL_HOST) ?: $row['website']; ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center uppercase text-[10px] font-bold">
                                <span class="px-2 py-0.5 rounded-md border <?php echo $row['source'] == 'pago' ? 'bg-amber-500/10 text-amber-500 border-amber-500/10' : 'bg-primary/10 text-primary border-primary/10'; ?>">
                                    <?php echo $row['source'] == 'pago' ? 'PAGO' : 'ORGÁNICO'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-center gap-1.5">
                                    <?php foreach(explode(',', $row['tags'] ?? '') as $tag): if(!trim($tag)) continue; ?>
                                        <span class="px-2 py-0.5 bg-zinc-800 text-zinc-400 text-[9px] font-semibold rounded border border-border uppercase"><?php echo trim($tag); ?></span>
                                    <?php endforeach; if(empty($row['tags'])) echo '<span class="text-zinc-800 text-[10px]">&mdash;</span>'; ?>
                                 </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xs text-zinc-300 font-medium block"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></span>
                                <span class="text-[10px] text-zinc-600 block mt-0.5 uppercase tabular-nums font-bold"><?php echo date('H:i', strtotime($row['created_at'])); ?></span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-white text-sm">
                                <?php echo number_format($row['proposal_price'] ?? 0, 2, ',', '.'); ?>€
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Detalle/Edición -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-card border border-border w-full max-w-2xl rounded-2xl shadow-2xl p-8 transform transition-all animate-in zoom-in duration-200">
            <form id="editLeadForm">
                <input type="hidden" name="id" id="det-id">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-full mr-4">
                        <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-1 ml-1 leading-none">Identidad Comercial</label>
                        <input type="text" name="name" id="det-name" placeholder="Nombre" class="w-full bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-2xl font-bold text-white transition-all px-0 py-1">
                        <input type="text" name="company" id="det-company" placeholder="Empresa (opcional)" class="w-full bg-transparent border-b border-transparent hover:border-zinc-800 focus:border-primary focus:outline-none text-zinc-500 text-sm mt-1 px-0 py-1">
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-2 hover:bg-zinc-800 rounded-xl transition-colors text-zinc-500 hover:text-white"><i data-lucide="x" class="w-6 h-6"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-1 ml-1">Contacto Directo</label>
                            <div class="relative group mt-1">
                                <i data-lucide="mail" class="w-4 h-4 absolute left-0 top-2.5 text-zinc-500"></i>
                                <input type="text" name="email" id="det-email" class="w-full pl-6 bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-sm py-2 text-zinc-300">
                            </div>
                            <div class="relative group mt-2">
                                <i data-lucide="phone" class="w-4 h-4 absolute left-0 top-2.5 text-zinc-500"></i>
                                <input type="text" name="phone" id="det-phone" class="w-full pl-6 bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-sm py-2 text-zinc-300">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-1 ml-1">Presencia Digital</label>
                            <input type="text" name="website" id="det-website" placeholder="ejemplo.es" class="w-full bg-transparent border-b border-zinc-800 focus:border-primary focus:outline-none text-sm py-2 text-zinc-300">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-1 ml-1">Estrategia</label>
                            <input type="text" name="tags" id="det-tags" class="w-full bg-zinc-900 border border-border rounded-lg text-xs py-2 px-3 text-white focus:ring-1 focus:ring-primary focus:outline-none">
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <?php foreach($existingTags as $tag): ?>
                                    <button type="button" onclick="addTagEdit('<?php echo $tag; ?>')" class="px-2 py-0.5 bg-zinc-800/50 hover:bg-primary/20 hover:text-primary rounded border border-border text-[9px] text-zinc-500 font-bold uppercase transition-all">+ <?php echo $tag; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 uppercase font-bold text-[10px]">
                            <div>
                                <label class="text-zinc-600 tracking-widest mb-1 block">Fuente</label>
                                <select name="source" id="det-source" class="w-full bg-zinc-900 border border-border rounded-lg py-2 px-3 text-zinc-400 outline-none focus:ring-1 focus:ring-primary">
                                    <option value="organico">Orgánico</option>
                                    <option value="pago">Pago</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-zinc-600 tracking-widest mb-1 block">Inversión €</label>
                                <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-zinc-900 border border-border rounded-lg py-2 px-3 text-white font-bold outline-none focus:ring-1 focus:ring-primary">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-border pt-6">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-3 ml-1">Mensaje y Observaciones</label>
                    <textarea name="message" id="det-message" class="w-full bg-zinc-950/30 border border-border rounded-xl p-4 text-sm text-zinc-300 focus:ring-1 focus:ring-primary outline-none italic min-h-[100px]"></textarea>
                </div>

                <div class="mt-8 flex items-center justify-between">
                    <div id="saveStatus" class="text-[10px] font-black uppercase tracking-widest text-green-500 hidden animate-pulse underline underline-offset-4">Sincronizando...</div>
                    <div class="flex gap-3 ml-auto">
                        <button type="button" onclick="closeDetailModal()" class="px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white text-[10px] font-black rounded-xl transition-all uppercase tracking-[0.2em]">Cerrar</button>
                        <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-blue-500 text-white text-[10px] font-black rounded-xl transition-all uppercase tracking-[0.2em] shadow-lg shadow-primary/20">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        function addTagEdit(tag) {
            const el = document.getElementById('det-tags');
            let vals = el.value.split(',').map(v => v.trim()).filter(v => v !== "");
            if(!vals.includes(tag)) {
                vals.push(tag);
                el.value = vals.join(', ');
            }
        }

        const searchInput = document.getElementById('searchLeadInput');
        const rows = document.querySelectorAll('.lead-row');
        const countSpan = document.getElementById('visibleLeadsCount');

        if(searchInput) {
            searchInput.addEventListener('input', () => {
                const term = searchInput.value.toLowerCase();
                let visible = 0;
                rows.forEach(r => {
                    const txt = r.innerText.toLowerCase();
                    const isV = txt.includes(term);
                    r.style.display = isV ? '' : 'none';
                    if(isV) visible++;
                });
                if(countSpan) countSpan.textContent = visible;
            });
        }

        const modalD = document.getElementById('detailModal');
        const editLeadForm = document.getElementById('editLeadForm');
        
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
            modalD.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function closeDetailModal() {
            modalD.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        editLeadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const sts = document.getElementById('saveStatus');
            sts.classList.remove('hidden');
            const fd = new FormData(this);
            fetch('update_lead.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    sts.textContent = "COMPLETADO";
                    setTimeout(() => location.reload(), 600);
                } else alert('Error: ' + res.message);
            }).catch(() => { alert('Fallo de red'); sts.classList.add('hidden'); });
        });

        window.onclick = e => { if(e.target == modalD) closeDetailModal(); }
    </script>
</body>
</html>
<?php $conn->close(); ?>
