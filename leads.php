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
        'nuevo' => ['label' => 'Nuevo Lead', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-100 shadow-sm'],
        'no_responde' => ['label' => 'No Responde', 'class' => 'bg-slate-50 text-slate-500 border-slate-200'],
        'llamar_tarde' => ['label' => 'Llamar Tarde', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm'],
        'enviar_propuesta' => ['label' => 'Enviar Prop.', 'class' => 'bg-indigo-600 text-white border-transparent shadow-md'],
        'propuesta_enviada' => ['label' => 'Prop. Enviada', 'class' => 'bg-purple-50 text-purple-700 border-purple-200 shadow-sm'],
        'ganado' => ['label' => 'Ganado', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-sm'],
        'perdido' => ['label' => 'Perdido', 'class' => 'bg-red-50 text-red-700 border-red-200 shadow-sm'],
        'no_cualificado' => ['label' => 'No Cualificado', 'class' => 'bg-slate-50 text-slate-400 border-slate-200'],
        'interesado_tarde' => ['label' => 'Interesado +', 'class' => 'bg-cyan-50 text-cyan-700 border-cyan-200 shadow-sm'],
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
    <title>Leads Manager | Console v5</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-14 space-y-10">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-10 pb-10 border-b border-slate-200 group">
            <div>
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-600 shadow-sm border border-slate-200 group-hover:bg-slate-900 group-hover:text-white transition-all">
                        <i data-lucide="users" class="w-6 h-6 stroke-[2.5]"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight uppercase italic">Gestión <span class="text-indigo-600 not-italic font-black">Corporativa</span></h1>
                </div>
                <p class="text-slate-400 font-medium max-w-sm mt-3 ml-1 text-sm">Control centralizado de prospectos comerciales y flujo de adquisición.</p>
            </div>
            
            <div class="flex gap-3">
                <button class="px-5 py-3 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-400 hover:text-slate-900 hover:border-slate-800 transition-all shadow-sm flex items-center gap-2 group uppercase tracking-widest active:scale-95">
                    <i data-lucide="download" class="w-4 h-4 group-hover:translate-y-0.5 transition-all text-indigo-400"></i> EXPORTAR
                </button>
                <button onclick="toggleModal()" class="px-8 py-4 bg-slate-900 rounded-lg text-xs font-bold text-white hover:bg-black transition-all shadow-lg flex items-center gap-3 active:scale-95 uppercase tracking-widest">
                    <i data-lucide="plus" class="w-5 h-5"></i> Registrar Lead
                </button>
            </div>
        </header>

        <!-- Serious Data Filtering -->
        <section class="flex flex-col lg:flex-row items-center justify-between gap-6 p-6 bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <div class="relative w-full lg:w-96 group">
                    <i data-lucide="search" class="w-4.5 h-4.5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-all"></i>
                    <input type="text" id="filterGlobal" placeholder="Búsqueda técnica de UID, Empresa o Nombre..." class="w-full pl-14 pr-6 py-3.5 bg-slate-50/50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-slate-100 focus:border-slate-800 outline-none transition-all text-[13px] font-bold text-slate-800 placeholder-slate-300 shadow-inner">
                </div>
            </div>

            <div class="flex items-center gap-4 w-full lg:w-auto overflow-x-auto pb-2 lg:pb-0 scrollbar-none">
                <select id="filterStatus" class="bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-[10px] font-bold text-slate-600 uppercase tracking-widest outline-none focus:ring-4 focus:ring-slate-100 appearance-none cursor-pointer h-11 min-w-[160px] text-center italic">
                    <option value="all">TODOS LOS STATUS</option>
                    <option value="nuevo">NUEVOS</option>
                    <option value="ganado">GANADOS</option>
                    <option value="perdido">PERDIDOS</option>
                </select>
                <select id="filterSource" class="bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-[10px] font-bold text-slate-600 uppercase tracking-widest outline-none focus:ring-4 focus:ring-slate-100 appearance-none cursor-pointer h-11 min-w-[160px] text-center italic">
                    <option value="all">TODAS LAS FUENTES</option>
                    <option value="pago">PAID ADS</option>
                    <option value="organico">ORGANIC</option>
                </select>
                <div class="bg-slate-900 rounded-lg px-5 py-2.5 text-center shadow-md flex flex-col justify-center min-w-[80px]">
                    <span id="visibleLeadsCount" class="text-xl font-black text-white tabular-nums leading-none italic"><?php echo $result->num_rows; ?></span>
                    <span class="text-[7px] font-bold text-slate-400 uppercase tracking-[0.2em] block mt-1 opacity-80">Total</span>
                </div>
            </div>
        </section>

        <!-- Serious Lead Table -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden group">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-50 text-[9px] font-bold uppercase tracking-[0.3em] text-slate-400 border-b border-slate-100">
                            <th class="px-10 py-5 w-[35%]">Lead Principal</th>
                            <th class="px-6 py-5 text-center w-[15%]">Operación</th>
                            <th class="px-6 py-5 text-center w-[15%]">Origen</th>
                            <th class="px-6 py-5 text-right w-[20%]">Valor Económico</th>
                            <th class="px-10 py-5 text-right w-[15%]">...</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $rowDate = date('Y-m-d', strtotime($row['created_at']));
                            $statusInfo = getStatusBadge($row['status'] ?? 'nuevo');
                        ?>
                        <tr class="lead-row hover:bg-slate-50/50 transition-all group/row cursor-pointer" 
                            onclick='showLeadDetails(<?php echo $json_data; ?>)'
                            data-source="<?php echo $row['source']; ?>"
                            data-price="<?php echo $row['proposal_price'] ?? 0; ?>"
                            data-date="<?php echo $rowDate; ?>"
                            data-status="<?php echo $row['status'] ?? 'nuevo'; ?>">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 bg-slate-900 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-md group-hover/row:scale-105 transition-all overflow-hidden uppercase">
                                        <?php echo substr($row['name'], 0, 1); ?>
                                    </div>
                                    <div class="flex flex-col overflow-hidden">
                                        <span class="text-[15px] font-bold text-slate-900 group-hover/row:text-indigo-600 transition-all tracking-tight leading-none italic uppercase truncate"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate"><?php echo htmlspecialchars($row['company'] ?: 'Particular'); ?></span>
                                            <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                            <span class="text-[9px] font-bold text-slate-400 tabular-nums"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-4 py-2 rounded-lg border text-[8px] font-black uppercase tracking-widest transition-all shadow-sm group-hover/row:scale-105 <?php echo $statusInfo['class']; ?>">
                                    <?php echo $statusInfo['label']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <?php if($row['source'] == 'pago'): ?>
                                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500 fill-amber-500/10"></i>
                                        <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-amber-600">ADS</span>
                                    <?php else: ?>
                                        <i data-lucide="leaf" class="w-3.5 h-3.5 text-emerald-500"></i>
                                        <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-emerald-600">ORG</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-right">
                                <span class="text-xl font-black text-slate-900 tracking-tighter tabular-nums italic group-hover/row:text-indigo-600 transition-all"><?php echo number_format($row['proposal_price'] ?? 0, 0, ',', '.'); ?>€</span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <button class="p-2.5 text-slate-300 hover:text-slate-900 transition-all active:scale-95">
                                    <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Context Info -->
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] ml-4 italic opacity-80">Sync OK :: Mostrando logs de auditoría comercial filtrados</span>
                <div class="flex gap-2">
                    <button class="px-4 py-2 text-[10px] font-bold text-slate-400 hover:text-slate-900 transition-all uppercase tracking-widest">Prev</button>
                    <button class="px-4 py-2 text-[10px] font-bold text-slate-900 hover:text-indigo-600 transition-all uppercase tracking-widest">Next</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Detalle Profesional -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white border border-slate-200 w-full max-w-2xl rounded-xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] p-10 transform transition-all animate-in zoom-in duration-200">
            <form id="editLeadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="det-id">
                
                <div class="flex justify-between items-start mb-10 pb-6 border-b border-slate-100">
                    <div class="w-full mr-10 group">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-3 ml-1">Identidad Comercial</label>
                        <input type="text" name="name" id="det-name" placeholder="Ficha de cliente" class="w-full bg-transparent border-0 focus:ring-0 text-3xl font-black text-slate-900 transition-all px-0 italic uppercase tracking-tighter outline-none">
                        <input type="text" name="company" id="det-company" placeholder="Empresa S.L." class="w-full bg-transparent border-0 focus:ring-0 text-slate-400 text-lg mt-1 px-0 font-bold outline-none block">
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-lg transition-all text-slate-300 hover:text-slate-900 active:scale-90 shadow-sm"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                    <div class="space-y-10">
                        <div>
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] block mb-4 ml-1">Estado de Operación</label>
                            <select name="status" id="det-status" class="w-full bg-slate-900 border-0 rounded-lg py-4 px-5 text-[10px] text-white font-bold uppercase outline-none focus:ring-8 focus:ring-slate-100 transition-all shadow-xl appearance-none tracking-widest cursor-pointer text-center h-14 italic">
                                <option value="nuevo">Nuevo lead</option>
                                <option value="no_responde">No responde</option>
                                <option value="enviar_propuesta">Enviar prop.</option>
                                <option value="propuesta_enviada">Prop. enviada</option>
                                <option value="ganado">Ganado</option>
                                <option value="perdido">Perdido</option>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] block mb-1 ml-1">Contact Metadata</label>
                            <div class="relative">
                                <i data-lucide="mail" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                <input type="text" name="email" id="det-email" class="w-full pl-12 pr-4 bg-slate-50 border border-slate-100 focus:border-indigo-600 focus:bg-white focus:outline-none text-[12px] py-4 rounded-lg font-bold text-slate-700 shadow-inner">
                            </div>
                            <div class="relative">
                                <i data-lucide="phone" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                <input type="text" name="phone" id="det-phone" class="w-full pl-12 pr-4 bg-slate-50 border border-slate-100 focus:border-indigo-600 focus:bg-white focus:outline-none text-[12px] py-4 rounded-lg font-bold text-slate-700 shadow-inner">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-10">
                        <div>
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] block mb-4 ml-1">Valoración / Adquisición</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400 font-bold text-sm">€</span>
                                    <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-slate-50 border border-slate-100 rounded-lg py-4 px-4 pl-8 text-slate-900 font-black text-right outline-none focus:ring-4 focus:ring-indigo-100/50 text-xl shadow-inner">
                                </div>
                                <select name="source" id="det-source" class="w-full bg-white border border-slate-200 rounded-lg py-4 px-3 text-[9px] text-slate-600 font-bold uppercase tracking-widest outline-none focus:ring-4 focus:ring-slate-100 appearance-none text-center cursor-pointer h-14">
                                    <option value="organico">ORGÁNICO</option>
                                    <option value="pago">PAID ADS</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] block mb-4 ml-1">Segmentación Master</label>
                            <input type="text" name="tags" id="det-tags" placeholder="VIP, URGENTE..." class="w-full bg-slate-50 border border-slate-100 rounded-lg text-[10px] py-4 px-4 text-slate-900 focus:ring-4 focus:ring-indigo-100 focus:bg-white outline-none mb-4 font-bold shadow-inner uppercase tracking-wider">
                            <div class="flex flex-wrap gap-2">
                                <?php foreach($existingTags as $tag): ?>
                                    <button type="button" onclick="addTagEdit('<?php echo $tag; ?>')" class="px-2.5 py-1.5 bg-slate-100 border border-slate-200 hover:bg-slate-900 hover:text-white rounded-md text-[8px] font-bold uppercase tracking-widest transition-all">+ <?php echo $tag; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 mb-10 shadow-inner">
                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] block mb-5 ml-1">Log de Observaciones Técnicas</label>
                    <textarea name="message" id="det-message" class="w-full bg-transparent border-0 focus:ring-0 outline-none min-h-[100px] text-[14px] font-medium text-slate-600 placeholder-slate-300 italic" placeholder="Añade notas sobre el cierre..."></textarea>
                </div>

                <div class="flex items-center justify-between mt-12 pt-8 border-t border-slate-100">
                    <button type="button" onclick="deleteLead()" class="text-slate-300 hover:text-red-600 text-[9px] font-bold uppercase tracking-widest transition-all flex items-center gap-2 group active:scale-95">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar Registro
                    </button>
                    <div class="flex gap-3 items-center">
                        <button type="button" onclick="closeDetailModal()" class="px-6 py-3.5 text-slate-400 text-[10px] font-bold rounded-lg uppercase tracking-widest hover:text-slate-900">Cancelar</button>
                        <button type="submit" class="px-10 py-3.5 bg-slate-900 hover:bg-black text-white text-[10px] font-bold rounded-lg uppercase tracking-widest shadow-xl active:scale-95">Sync & Guardar</button>
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
        const rows = document.querySelectorAll('.lead-row');
        const countSpan = document.getElementById('visibleLeadsCount');

        function applyFilters() {
            const query = globalInp.value.toLowerCase();
            const status = statusSel.value;
            const source = sourceSel.value;

            let visible = 0;
            rows.forEach(r => {
                const text = r.innerText.toLowerCase();
                const rStatus = r.dataset.status;
                const rSrc = r.dataset.source;

                const matchText = text.includes(query);
                const matchStatus = (status === 'all' || rStatus === status);
                const matchSrc = (source === 'all' || rSrc === source);

                const isV = matchText && matchStatus && matchSrc;
                r.style.display = isV ? '' : 'none';
                if(isV) visible++;
            });
            countSpan.textContent = visible;
        }

        [globalInp, statusSel, sourceSel].forEach(el => {
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
            if(!confirm('¿Eliminar prospecto permanentemente?')) return;
            const fd = new FormData();
            fd.append('id', id);
            fd.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            fetch('delete_lead.php', { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                if(res.success) location.reload();
            });
        }

        editLeadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fetch('update_lead.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.success) location.reload();
            });
        });

        window.onclick = e => { if(e.target == modalD) closeDetailModal(); }
    </script>
</body>
</html>
<?php $conn->close(); ?>
