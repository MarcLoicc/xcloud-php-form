<?php require_once 'auth.php'; ?>
<?php
date_default_timezone_set('Europe/Madrid');
require_once 'db.php';
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");

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

function getStatusBadge($status) {
    $map = [
        'nuevo' => ['label' => 'Nuevo Lead', 'class' => 'bg-blue-50 text-blue-600 border-blue-100'],
        'no_responde' => ['label' => 'No Responde', 'class' => 'bg-slate-100 text-slate-500 border-slate-200'],
        'llamar_tarde' => ['label' => 'Llamar Tarde', 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
        'enviar_propuesta' => ['label' => 'Enviar Prop.', 'class' => 'bg-indigo-50 text-indigo-600 border-indigo-100'],
        'propuesta_enviada' => ['label' => 'Prop. Enviada', 'class' => 'bg-purple-50 text-purple-600 border-purple-100'],
        'ganado' => ['label' => 'Ganado', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100'],
        'perdido' => ['label' => 'Perdido', 'class' => 'bg-red-50 text-red-600 border-red-100'],
        'no_cualificado' => ['label' => 'No Cualificado', 'class' => 'bg-zinc-100 text-zinc-400 border-zinc-200'],
        'interesado_tarde' => ['label' => 'Interesado +', 'class' => 'bg-cyan-50 text-cyan-600 border-cyan-100'],
    ];
    return $map[$status] ?? ['label' => $status, 'class' => 'bg-slate-50 text-slate-400 border-slate-100'];
}
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
                <p class="text-slate-500 text-sm mt-1">Seguimiento y flujo de prospectos comerciales.</p>
            </div>
            <button onclick="toggleModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Lead
            </button>
        </header>

        <!-- Filtros Avanzados -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8 bg-white border border-slate-200 p-6 rounded-2xl items-end shadow-sm">
            <div class="md:col-span-1 space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Búsqueda</label>
                <input type="text" id="filterGlobal" placeholder="Nombre o empresa..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-sm outline-none">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Estado</label>
                <select id="filterStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold uppercase transition-all outline-none">
                    <option value="all">TODOS LOS ESTADOS</option>
                    <option value="nuevo">Nuevo lead</option>
                    <option value="no_responde">No responde</option>
                    <option value="llamar_tarde">Llamar tarde</option>
                    <option value="enviar_propuesta">Enviar prop.</option>
                    <option value="propuesta_enviada">Prop. enviada</option>
                    <option value="ganado">Ganado</option>
                    <option value="perdido">Perdido</option>
                    <option value="no_cualificado">No cualif.</option>
                    <option value="interesado_tarde">Interesado +</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Fuente</label>
                <select id="filterSource" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold uppercase outline-none">
                    <option value="all">TODAS</option>
                    <option value="pago">PAGO</option>
                    <option value="organico">ORGÁNICO</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Inversión Mín.</label>
                <input type="number" id="filterPrice" placeholder="0 €" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Fecha Entrada</label>
                <input type="date" id="filterDate" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none">
            </div>
            <div class="bg-indigo-50 rounded-xl p-3 text-center border border-indigo-100">
                <span id="visibleLeadsCount" class="text-2xl font-black text-indigo-600 tabular-nums leading-none"><?php echo $result->num_rows; ?></span>
                <span class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest block mt-1">Leads</span>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                            <th class="px-8 py-5">Lead / Empresa</th>
                            <th class="px-6 py-5 text-center">Estado Flujo</th>
                            <th class="px-6 py-5 text-center">Origen</th>
                            <th class="px-6 py-5 text-right">Fecha</th>
                            <th class="px-8 py-5 text-right font-bold">Inversión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $rowDate = date('Y-m-d', strtotime($row['created_at']));
                            $statusInfo = getStatusBadge($row['status'] ?? 'nuevo');
                        ?>
                        <tr class="lead-row hover:bg-slate-50/80 transition-all group cursor-pointer" 
                            onclick='showLeadDetails(<?php echo $json_data; ?>)'
                            data-source="<?php echo $row['source']; ?>"
                            data-price="<?php echo $row['proposal_price'] ?? 0; ?>"
                            data-date="<?php echo $rowDate; ?>"
                            data-status="<?php echo $row['status'] ?? 'nuevo'; ?>"
                            data-tags="<?php echo htmlspecialchars($row['tags'] ?? ''); ?>">
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors"><?php echo htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-xs text-slate-400 mt-0.5 font-medium">
                                        <?php echo htmlspecialchars($row['company'] ?: 'Particular', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="px-3 py-1.5 rounded-lg border text-[10px] font-black uppercase tracking-wider <?php echo $statusInfo['class']; ?>">
                                    <?php echo $statusInfo['label']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center uppercase text-[10px] font-black">
                                <span class="<?php echo $row['source'] == 'pago' ? 'text-amber-500' : 'text-indigo-400'; ?>">
                                    <?php echo $row['source'] == 'pago' ? 'PAGO ADS' : 'ORGÁNICO'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-xs text-slate-900 font-bold block"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></span>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-slate-900 text-sm">
                                <?php echo number_format($row['proposal_price'] ?? 0, 0, ',', '.'); ?>€
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
        <div class="bg-white border border-slate-200 w-full max-w-2xl rounded-[2.5rem] shadow-2xl p-10 transform transition-all animate-in zoom-in duration-200">
            <form id="editLeadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="det-id">
                
                <div class="flex justify-between items-start mb-8">
                    <div class="w-full mr-6">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Perfil del Cliente</label>
                        <input type="text" name="name" id="det-name" placeholder="Nombre completo" class="w-full bg-transparent border-b-2 border-slate-50 focus:border-indigo-600 focus:outline-none text-3xl font-black text-slate-900 transition-all px-0 py-2">
                        <input type="text" name="company" id="det-company" placeholder="Empresa u Organización" class="w-full bg-transparent border-b border-transparent hover:border-slate-50 focus:border-indigo-600 focus:outline-none text-slate-500 text-base mt-2 px-0 py-1 font-medium">
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-colors text-slate-400 hover:text-slate-900"><i data-lucide="x" class="w-6 h-6"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-8">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Estado de la Oportunidad</label>
                            <select name="status" id="det-status" class="w-full bg-indigo-600 border border-indigo-500 rounded-2xl py-4 px-5 text-xs text-white font-black uppercase outline-none focus:ring-4 focus:ring-indigo-100 transition-all shadow-lg shadow-indigo-100">
                                <option value="nuevo">Nuevo lead</option>
                                <option value="no_responde">No responde</option>
                                <option value="llamar_tarde">Llamar tarde</option>
                                <option value="enviar_propuesta">Enviar prop.</option>
                                <option value="propuesta_enviada">Prop. enviada</option>
                                <option value="ganado">Ganado</option>
                                <option value="perdido">Perdido</option>
                                <option value="no_cualificado">No cualif.</option>
                                <option value="interesado_tarde">Interesado +</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Datos de Contacto</label>
                            <div class="space-y-4">
                                <div class="relative">
                                    <i data-lucide="mail" class="w-4 h-4 absolute left-0 top-3 text-slate-300"></i>
                                    <input type="text" name="email" id="det-email" class="w-full pl-7 bg-transparent border-b border-slate-100 focus:border-indigo-600 focus:outline-none text-sm py-2.5 text-slate-600">
                                </div>
                                <div class="relative">
                                    <i data-lucide="phone" class="w-4 h-4 absolute left-0 top-3 text-slate-300"></i>
                                    <input type="text" name="phone" id="det-phone" class="w-full pl-7 bg-transparent border-b border-slate-100 focus:border-indigo-600 focus:outline-none text-sm py-2.5 text-slate-600">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Finanzas y Canal</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="relative">
                                    <span class="absolute left-4 top-4 text-indigo-300 font-bold">$</span>
                                    <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-4 pl-9 text-slate-900 font-black text-right outline-none focus:ring-2 focus:ring-indigo-500/10">
                                </div>
                                <select name="source" id="det-source" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-4 text-[10px] text-slate-600 font-black uppercase outline-none focus:ring-2 focus:ring-indigo-500/10">
                                    <option value="organico">Orgánico</option>
                                    <option value="pago">Pago Ads</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Etiquetado</label>
                            <input type="text" name="tags" id="det-tags" placeholder="Ej: VIP, Prioritario..." class="w-full bg-slate-50 border border-slate-200 rounded-xl text-xs py-3 px-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/20 outline-none mb-3">
                            <div class="flex flex-wrap gap-1.5 translate-x-1">
                                <?php foreach($existingTags as $tag): ?>
                                    <button type="button" onclick="addTagEdit('<?php echo $tag; ?>')" class="px-2.5 py-1 bg-white border border-slate-200 hover:bg-indigo-600 hover:text-white rounded-lg text-[9px] text-slate-500 font-black uppercase transition-all">+ <?php echo $tag; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50/30 border border-indigo-50 rounded-2xl p-6 mb-8">
                    <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest block mb-3 ml-1">Observaciones Estratégicas</label>
                    <textarea name="message" id="det-message" class="w-full bg-white border border-slate-100 rounded-xl p-5 text-sm text-slate-600 focus:ring-2 focus:ring-indigo-500/10 outline-none min-h-[100px] shadow-inner italic"></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" onclick="deleteLead()" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar Lead
                    </button>
                    <div class="flex gap-4 items-center">
                        <div id="saveStatus" class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 hidden animate-pulse mr-2">Sincronizando...</div>
                        <button type="button" onclick="closeDetailModal()" class="px-8 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-black rounded-2xl transition-all uppercase">Cerrar</button>
                        <button type="submit" class="px-8 py-3.5 bg-slate-900 hover:bg-black text-white text-[10px] font-black rounded-2xl transition-all uppercase shadow-xl shadow-slate-200">Guardar Cambios</button>
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

        const globalInp = document.getElementById('filterGlobal');
        const statusSel = document.getElementById('filterStatus');
        const sourceSel = document.getElementById('filterSource');
        const priceInp = document.getElementById('filterPrice');
        const dateInp = document.getElementById('filterDate');
        const rows = document.querySelectorAll('.lead-row');
        const countSpan = document.getElementById('visibleLeadsCount');

        function applyFilters() {
            const query = globalInp.value.toLowerCase();
            const status = statusSel.value;
            const source = sourceSel.value;
            const minPrice = parseFloat(priceInp.value) || 0;
            const targetDate = dateInp.value;

            let visible = 0;
            rows.forEach(r => {
                const text = r.innerText.toLowerCase();
                const rStatus = r.dataset.status;
                const rSrc = r.dataset.source;
                const rPrice = parseFloat(r.dataset.price) || 0;
                const rDate = r.dataset.date;

                const matchText = text.includes(query);
                const matchStatus = (status === 'all' || rStatus === status);
                const matchSrc = (source === 'all' || rSrc === source);
                const matchPrice = (rPrice >= minPrice);
                const matchDate = (!targetDate || rDate === targetDate);

                const isV = matchText && matchStatus && matchSrc && matchPrice && matchDate;
                r.style.display = isV ? '' : 'none';
                if(isV) visible++;
            });
            countSpan.textContent = visible;
        }

        [globalInp, statusSel, sourceSel, priceInp, dateInp].forEach(el => {
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
            document.getElementById('det-tags').value = data.tags || '';
            document.getElementById('det-source').value = data.source || 'organico';
            document.getElementById('det-status').value = data.status || 'nuevo';
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
            if(!confirm('¿Eliminar registro?')) return;
            const fd = new FormData();
            fd.append('id', id);
            fd.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            fetch('delete_lead', { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                if(res.success) location.reload();
            });
        }

        editLeadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const sts = document.getElementById('saveStatus');
            sts.classList.remove('hidden');
            const fd = new FormData(this);
            fetch('update_lead', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.success) location.reload();
                else sts.classList.add('hidden');
            });
        });

        window.onclick = e => { if(e.target == modalD) closeDetailModal(); }
    </script>
</body>
</html>
<?php $conn->close(); ?>
