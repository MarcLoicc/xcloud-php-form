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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Leads - CRM Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-8 min-h-screen">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 pb-6 border-b border-slate-200">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Gestión de <span class="text-indigo-600">Leads</span></h1>
                <p class="text-slate-500 text-sm mt-1">Seguimiento y edición de prospectos comerciales.</p>
            </div>
            <button onclick="toggleModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Lead
            </button>
        </header>

        <!-- Filtros Avanzados -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8 bg-white border border-slate-200 p-6 rounded-2xl items-end shadow-sm">
            <div class="md:col-span-1 space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Búsqueda</label>
                <div class="relative group">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-slate-400"></i>
                    <input type="text" id="filterGlobal" placeholder="Nombre o empresa..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm transition-all">
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Fuente</label>
                <select id="filterSource" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 appearance-none cursor-pointer">
                    <option value="all">TODAS</option>
                    <option value="pago">PAGO</option>
                    <option value="organico">ORGÁNICO</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Presupuesto Min.</label>
                <input type="number" id="filterPrice" placeholder="0.00 €" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Fecha Entrada</label>
                <input type="date" id="filterDate" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <div class="bg-indigo-50 rounded-xl p-3 text-center border border-indigo-100">
                <span class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest block mb-1">Total Filtrados</span>
                <span id="visibleLeadsCount" class="text-2xl font-black text-indigo-600 tabular-nums"><?php echo $result->num_rows; ?></span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                            <th class="px-8 py-5">Lead / Empresa</th>
                            <th class="px-6 py-5 text-center">Fuente</th>
                            <th class="px-6 py-5 text-center">Etiquetas</th>
                            <th class="px-6 py-5 text-right">Fecha</th>
                            <th class="px-8 py-5 text-right font-bold">Inversión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $rowDate = date('Y-m-d', strtotime($row['created_at']));
                        ?>
                        <tr class="lead-row hover:bg-slate-50/80 transition-all group cursor-pointer" 
                            onclick='showLeadDetails(<?php echo $json_data; ?>)'
                            data-source="<?php echo $row['source']; ?>"
                            data-price="<?php echo $row['proposal_price'] ?? 0; ?>"
                            data-date="<?php echo $rowDate; ?>"
                            data-tags="<?php echo htmlspecialchars($row['tags'] ?? ''); ?>">
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors"><?php echo htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-xs text-slate-400 mt-0.5 font-medium">
                                        <?php echo htmlspecialchars($row['company'] ?: 'Particular', ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if($row['website']): ?>
                                            &middot; <span class="text-indigo-400"><?php echo htmlspecialchars(parse_url($row['website'], PHP_URL_HOST) ?: $row['website'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center uppercase text-[10px] font-black">
                                <span class="px-3 py-1 rounded-full border <?php echo $row['source'] == 'pago' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100'; ?>">
                                    <?php echo $row['source'] == 'pago' ? 'PAGO' : 'ORGÁNICO'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap justify-center gap-1.5">
                                    <?php foreach(explode(',', $row['tags'] ?? '') as $tag): if(!trim($tag)) continue; ?>
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-bold rounded border border-slate-200 uppercase"><?php echo htmlspecialchars(trim($tag), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endforeach; if(empty($row['tags'])) echo '<span class="text-slate-200 text-xs">&mdash;</span>'; ?>
                                 </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-xs text-slate-900 font-bold block"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-medium"><?php echo date('H:i', strtotime($row['created_at'])); ?> h</span>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-slate-900 text-sm">
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
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white border border-slate-200 w-full max-w-2xl rounded-3xl shadow-2xl p-10 transform transition-all animate-in zoom-in duration-200">
            <form id="editLeadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="det-id">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-full mr-6">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Identificación del Lead</label>
                        <input type="text" name="name" id="det-name" placeholder="Nombre completo" class="w-full bg-transparent border-b-2 border-slate-100 focus:border-indigo-600 focus:outline-none text-3xl font-black text-slate-900 transition-all px-0 py-2">
                        <input type="text" name="company" id="det-company" placeholder="Empresa u Organización" class="w-full bg-transparent border-b border-transparent hover:border-slate-100 focus:border-indigo-600 focus:outline-none text-slate-500 text-base mt-2 px-0 py-1">
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-colors text-slate-400 hover:text-slate-900"><i data-lucide="x" class="w-6 h-6"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                    <div class="space-y-8">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Contacto Directo</label>
                            <div class="space-y-4">
                                <div class="relative group">
                                    <i data-lucide="mail" class="w-4 h-4 absolute left-0 top-3 text-slate-300"></i>
                                    <input type="text" name="email" id="det-email" class="w-full pl-7 bg-transparent border-b border-slate-100 focus:border-indigo-600 focus:outline-none text-sm py-2.5 text-slate-600">
                                </div>
                                <div class="relative group">
                                    <i data-lucide="phone" class="w-4 h-4 absolute left-0 top-3 text-slate-300"></i>
                                    <input type="text" name="phone" id="det-phone" class="w-full pl-7 bg-transparent border-b border-slate-100 focus:border-indigo-600 focus:outline-none text-sm py-2.5 text-slate-600">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Plataforma Web</label>
                            <div class="relative group">
                                <i data-lucide="globe" class="w-4 h-4 absolute left-0 top-3 text-slate-300"></i>
                                <input type="text" name="website" id="det-website" placeholder="www.ejemploweb.com" class="w-full pl-7 bg-transparent border-b border-slate-100 focus:border-indigo-600 focus:outline-none text-sm py-2.5 text-slate-600 font-medium">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Etiquetas del Perfil</label>
                            <input type="text" name="tags" id="det-tags" placeholder="Ej: VIP, Prioritario..." class="w-full bg-slate-50 border border-slate-200 rounded-xl text-xs py-3 px-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            <div class="flex flex-wrap gap-2 mt-4">
                                <?php foreach($existingTags as $tag): ?>
                                    <button type="button" onclick="addTagEdit('<?php echo $tag; ?>')" class="px-3 py-1 bg-slate-50 hover:bg-indigo-600 hover:text-white rounded-lg border border-slate-200 text-[10px] text-slate-500 font-bold uppercase transition-all">+ <?php echo $tag; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Procedencia</label>
                                <select name="source" id="det-source" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm text-slate-600 font-bold outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    <option value="organico">Orgánico</option>
                                    <option value="pago">Pago Ads</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block text-right">Presupuesto</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-indigo-50 border border-indigo-100 rounded-xl py-3 px-4 text-indigo-700 font-black text-right outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    <span class="absolute left-3 top-3.5 text-indigo-300 font-bold">€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-4 ml-1">Mensaje y Observaciones del Cliente</label>
                    <textarea name="message" id="det-message" class="w-full bg-white border border-slate-200 rounded-xl p-5 text-sm text-slate-600 focus:ring-2 focus:ring-indigo-500/20 outline-none min-h-[120px] shadow-inner font-medium italic"></textarea>
                </div>

                <div class="mt-10 flex items-center justify-between">
                    <button type="button" onclick="deleteLead()" class="px-5 py-3 border border-red-100 bg-red-50 hover:bg-red-100 text-red-600 text-[10px] font-black rounded-xl transition-all uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar Registro
                    </button>
                    <div class="flex gap-4 ml-auto items-center">
                        <div id="saveStatus" class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 hidden animate-pulse mr-4">Sincronizando...</div>
                        <button type="button" onclick="closeDetailModal()" class="px-8 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-black rounded-xl transition-all uppercase tracking-widest">Descartar</button>
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black rounded-xl transition-all uppercase tracking-widest shadow-xl shadow-indigo-100">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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

        const globalInp = document.getElementById('filterGlobal');
        const sourceSel = document.getElementById('filterSource');
        const priceInp = document.getElementById('filterPrice');
        const dateInp = document.getElementById('filterDate');
        const rows = document.querySelectorAll('.lead-row');
        const countSpan = document.getElementById('visibleLeadsCount');

        function applyFilters() {
            const query = globalInp.value.toLowerCase();
            const source = sourceSel.value;
            const minPrice = parseFloat(priceInp.value) || 0;
            const targetDate = dateInp.value;

            let visible = 0;
            rows.forEach(r => {
                const text = r.innerText.toLowerCase();
                const rSrc = r.dataset.source;
                const rPrice = parseFloat(r.dataset.price) || 0;
                const rDate = r.dataset.date;

                const matchText = text.includes(query);
                const matchSrc = (source === 'all' || rSrc === source);
                const matchPrice = (rPrice >= minPrice);
                const matchDate = (!targetDate || rDate === targetDate);

                const isV = matchText && matchSrc && matchPrice && matchDate;
                r.style.display = isV ? '' : 'none';
                if(isV) visible++;
            });
            countSpan.textContent = visible;
        }

        [globalInp, sourceSel, priceInp, dateInp].forEach(el => {
            el.addEventListener('input', applyFilters);
        });

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

        function deleteLead() {
            const id = document.getElementById('det-id').value;
            if(!confirm('¿Estás seguro de eliminar este lead para siempre?')) return;
            
            const fd = new FormData();
            fd.append('id', id);
            fd.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

            fetch('delete_lead', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.success) location.reload();
                else alert('Error: ' + res.message);
            }).catch(() => alert('Fallo de red'));
        }

        editLeadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const sts = document.getElementById('saveStatus');
            sts.classList.remove('hidden');
            const fd = new FormData(this);
            fetch('update_lead', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    sts.textContent = "¡GUARDADO!";
                    setTimeout(() => location.reload(), 600);
                } else alert('Error: ' + res.message);
            }).catch(() => { alert('Fallo de red'); sts.classList.add('hidden'); });
        });

        window.onclick = e => { if(e.target == modalD) closeDetailModal(); }
    </script>
</body>
</html>
<?php $conn->close(); ?>
