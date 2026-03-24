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
        'nuevo' => ['label' => 'NUEVO LOG', 'class' => 'bg-indigo-950 text-indigo-400 border-indigo-900 shadow-xl'],
        'no_responde' => ['label' => 'SIN RESPUESTA', 'class' => 'bg-slate-900 text-slate-500 border-slate-800'],
        'llamar_tarde' => ['label' => 'SEGUIMIENTO', 'class' => 'bg-amber-950 text-amber-500 border-amber-900 shadow-xl'],
        'enviar_propuesta' => ['label' => 'PROPUESTA', 'class' => 'bg-white text-slate-950 border-white shadow-2xl'],
        'propuesta_enviada' => ['label' => 'ENVIADA', 'class' => 'bg-purple-950 text-purple-400 border-purple-900 shadow-xl'],
        'ganado' => ['label' => 'GANADO', 'class' => 'bg-emerald-950 text-emerald-400 border-emerald-900 shadow-xl'],
        'perdido' => ['label' => 'PERDIDO', 'class' => 'bg-red-950 text-red-500 border-red-900 shadow-xl'],
        'no_cualificado' => ['label' => 'BAJA CALIDAD', 'class' => 'bg-slate-900 text-slate-600 border-slate-800'],
        'interesado_tarde' => ['label' => 'INTERÉS TARDÍO', 'class' => 'bg-cyan-950 text-cyan-400 border-cyan-900 shadow-xl'],
    ];
    return $map[$status] ?? ['label' => $status, 'class' => 'bg-slate-900 text-slate-500 border-slate-800'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Master Leads (Dark) | Console v5</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-slate-950 min-h-screen text-slate-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-14 space-y-12">
        <!-- Leads Header Dark -->
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-12 pb-12 border-b border-slate-800 group">
            <div>
                <div class="flex items-center gap-5 mb-5">
                    <div class="w-12 h-12 bg-white text-slate-950 rounded-lg flex items-center justify-center text-slate-600 shadow-2xl border border-white group-hover:rotate-12 transition-all">
                        <i data-lucide="users" class="w-7 h-7 stroke-[3]"></i>
                    </div>
                    <h1 class="text-4xl font-black text-white tracking-widest uppercase italic leading-none">GESTIÓN <span class="text-indigo-500 not-italic">MASTER</span></h1>
                </div>
                <p class="text-slate-500 font-bold max-w-md mt-4 text-[14px] leading-relaxed italic uppercase opacity-60">Control jerárquico de prospectos comerciales y flujo de capital.</p>
            </div>
            
            <div class="flex gap-4">
                <button class="px-7 py-4 bg-slate-900 border border-slate-800 rounded-lg text-[12px] font-black text-slate-500 hover:text-white hover:border-slate-600 transition-all shadow-xl flex items-center gap-3 group uppercase tracking-widest active:scale-95">
                    <i data-lucide="download" class="w-5 h-5 group-hover:translate-y-1 transition-all text-indigo-500"></i> EXPORT LOGS
                </button>
                <button onclick="toggleModal()" class="px-10 py-5 bg-white rounded-lg text-[12px] font-black text-slate-950 hover:bg-slate-200 transition-all shadow-2xl flex items-center gap-4 active:scale-95 uppercase tracking-[0.2em]">
                    <i data-lucide="plus" class="w-6 h-6"></i> ALTA REGISTRO
                </button>
            </div>
        </header>

        <!-- Search Controls Dark -->
        <section class="flex flex-col lg:flex-row items-center justify-between gap-8 p-8 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl">
            <div class="flex items-center gap-4 w-full lg:w-auto">
                <div class="relative w-full lg:w-[500px] group">
                    <i data-lucide="search" class="w-6 h-6 absolute left-6 top-1/2 -translate-y-1/2 text-slate-700 group-focus-within:text-white transition-all"></i>
                    <input type="text" id="filterGlobal" placeholder="SCANNING UID / EMPRESA / NOMBRE..." class="w-full pl-16 pr-8 py-5 bg-slate-950 border border-slate-800 rounded-lg focus:ring-4 focus:ring-slate-800 focus:border-indigo-500 focus:outline-none transition-all text-[15px] font-black text-white placeholder-slate-800 shadow-inner italic uppercase">
                </div>
            </div>

            <div class="flex items-center gap-6 w-full lg:w-auto overflow-x-auto pb-4 lg:pb-0 scrollbar-none">
                <select id="filterStatus" class="bg-slate-950 border border-slate-800 rounded-lg px-6 py-3 text-[12px] font-black text-slate-400 uppercase tracking-widest outline-none focus:ring-4 focus:ring-slate-800 appearance-none cursor-pointer h-14 min-w-[200px] text-center italic group hover:border-slate-600 transition-colors">
                    <option value="all">TODOS LOS STATUS</option>
                    <option value="nuevo">NUEVOS</option>
                    <option value="ganado">GANADOS</option>
                    <option value="perdido">PERDIDOS</option>
                </select>
                <select id="filterSource" class="bg-slate-950 border border-slate-800 rounded-lg px-6 py-3 text-[12px] font-black text-slate-400 uppercase tracking-widest outline-none focus:ring-4 focus:ring-slate-800 appearance-none cursor-pointer h-14 min-w-[200px] text-center italic group hover:border-slate-600 transition-colors">
                    <option value="all">TODAS LAS FUENTES</option>
                    <option value="pago">PAID ADS</option>
                    <option value="organico">ORGANIC</option>
                </select>
                <div class="bg-white rounded-lg px-8 py-3 text-center shadow-2xl flex flex-col justify-center min-w-[100px] border border-white">
                    <span id="visibleLeadsCount" class="text-2xl font-black text-slate-950 tabular-nums leading-none italic"><?php echo $result->num_rows; ?></span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mt-2 opacity-80">Sync</span>
                </div>
            </div>
        </section>

        <!-- Leads Table Dark -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl shadow-2xl overflow-hidden group">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-950 text-[10px] font-black uppercase tracking-[0.4em] text-slate-600 border-b border-slate-800 italic">
                            <th class="px-12 py-7 w-[40%]">MASTER IDENTIFIER</th>
                            <th class="px-8 py-7 text-center w-[15%]">AUDIT STATUS</th>
                            <th class="px-8 py-7 text-center w-[15%]">ORIGIN</th>
                            <th class="px-8 py-7 text-right w-[15%]">CAPITAL</th>
                            <th class="px-12 py-7 text-right w-[15%]">CMD</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <?php while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $rowDate = date('Y-m-d', strtotime($row['created_at']));
                            $statusInfo = getStatusBadge($row['status'] ?? 'nuevo');
                        ?>
                        <tr class="lead-row hover:bg-slate-800/50 transition-all group/row cursor-pointer" 
                            onclick='showLeadDetails(<?php echo $json_data; ?>)'
                            data-source="<?php echo $row['source']; ?>"
                            data-price="<?php echo $row['proposal_price'] ?? 0; ?>"
                            data-date="<?php echo $rowDate; ?>"
                            data-status="<?php echo $row['status'] ?? 'nuevo'; ?>">
                            <td class="px-12 py-8">
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 bg-slate-950 text-white rounded-lg border border-slate-800 flex items-center justify-center font-black text-xl shadow-xl group-hover/row:bg-white group-hover/row:text-slate-950 transition-all overflow-hidden uppercase italic">
                                        <?php echo substr($row['name'], 0, 1); ?>
                                    </div>
                                    <div class="flex flex-col overflow-hidden">
                                        <span class="text-xl font-black text-white group-hover/row:text-indigo-400 transition-all tracking-tighter leading-none italic uppercase truncate"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <div class="flex items-center gap-3 mt-3">
                                            <span class="text-[11px] font-black text-slate-600 uppercase tracking-widest truncate max-w-[150px] opacity-80"><?php echo htmlspecialchars($row['company'] ?: 'ENTITY_NULL'); ?></span>
                                            <span class="w-1.5 h-1.5 bg-slate-800 rounded-full"></span>
                                            <span class="text-[11px] font-black text-slate-700 tabular-nums italic"><?php echo date('d.m.Y', strtotime($row['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 text-center">
                                <span class="px-5 py-2 rounded-lg border text-[10px] font-black uppercase tracking-widest transition-all shadow-xl group-hover/row:scale-105 <?php echo $statusInfo['class']; ?>">
                                    <?php echo $statusInfo['label']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-8 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <?php if($row['source'] == 'pago'): ?>
                                        <i data-lucide="zap" class="w-4 h-4 text-amber-500 fill-amber-500/20"></i>
                                        <span class="text-[10px] font-black uppercase tracking-[0.25em] text-amber-600 italic">PAID_ADS</span>
                                    <?php else: ?>
                                        <i data-lucide="leaf" class="w-4 h-4 text-emerald-500"></i>
                                        <span class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-600 italic">ORGANIC_LOG</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <span class="text-2xl font-black text-white tracking-tighter tabular-nums italic group-hover/row:text-indigo-400 transition-all leading-none"><?php echo number_format($row['proposal_price'] ?? 0, 0, ',', '.'); ?>€</span>
                            </td>
                            <td class="px-12 py-8 text-right">
                                <button class="p-3 bg-slate-950 border border-slate-800 rounded-lg text-slate-700 hover:text-white hover:border-slate-600 transition-all active:scale-95 shadow-lg">
                                    <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Dark -->
            <div class="p-8 bg-slate-950/50 border-t border-slate-800 flex justify-between items-center">
                <span class="text-[10px] font-black text-slate-700 uppercase tracking-[0.4em] ml-6 italic opacity-60">AUDIT_LOG_SYNC_STATE: OK :: xCLOUD_DB ACTIVE</span>
                <div class="flex gap-4">
                    <button class="px-6 py-2.5 bg-slate-900 border border-slate-800 rounded text-[11px] font-black text-slate-600 hover:text-white transition-all uppercase tracking-widest">PREV_SET</button>
                    <button class="px-6 py-2.5 bg-slate-900 border border-slate-800 rounded text-[11px] font-black text-slate-600 hover:text-white transition-all uppercase tracking-widest">NEXT_SET</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Detalle Dark Maestro -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 overflow-y-auto">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl shadow-[0_50px_100px_-20px_#000000] p-12 transform transition-all animate-in zoom-in duration-200">
            <form id="editLeadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="det-id">
                
                <div class="flex justify-between items-start mb-14 pb-8 border-b border-slate-800">
                    <div class="w-full mr-12 group">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-[0.4em] block mb-4 ml-1 italic opacity-80">Master UID Identity</label>
                        <input type="text" name="name" id="det-name" class="w-full bg-transparent border-0 focus:ring-0 text-4xl font-black text-white transition-all px-0 italic uppercase tracking-tighter outline-none leading-none shadow-text-indigo-900">
                        <input type="text" name="company" id="det-company" class="w-full bg-transparent border-0 focus:ring-0 text-slate-500 text-xl mt-3 px-0 font-black outline-none block uppercase italic tracking-widest opacity-80">
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-4 bg-slate-950 hover:bg-red-950 hover:text-red-500 border border-slate-800 rounded-xl transition-all text-slate-700 active:scale-90 shadow-2xl flex items-center justify-center"><i data-lucide="x" class="w-6 h-6"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-14">
                    <div class="space-y-12">
                        <div>
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] block mb-6 ml-1 italic">Audit Operational Status</label>
                            <select name="status" id="det-status" class="w-full bg-white border-0 rounded-lg py-5 px-6 text-[12px] text-slate-950 font-black uppercase outline-none focus:ring-8 focus:ring-indigo-600/30 transition-all shadow-3xl appearance-none tracking-widest cursor-pointer text-center h-16 italic">
                                <option value="nuevo">NUEVO LOG</option>
                                <option value="no_responde">SIN RESPUESTA</option>
                                <option value="enviar_propuesta">EXPEDIR PROPUESTA</option>
                                <option value="propuesta_enviada">LOG ENVIADO</option>
                                <option value="ganado">MASTER WIN</option>
                                <option value="perdido">MASTER LOSS</option>
                            </select>
                        </div>
                        <div class="space-y-6">
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] block mb-2 ml-1 italic">Contact Endpoint Metadata</label>
                            <div class="relative group/field">
                                <i data-lucide="mail" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-800 group-focus-within/field:text-indigo-400 transition-all"></i>
                                <input type="text" name="email" id="det-email" class="w-full pl-14 pr-6 bg-slate-950 border border-slate-800 rounded-lg focus:border-indigo-600 focus:outline-none text-[15px] py-5 font-black text-white shadow-inner uppercase italic">
                            </div>
                            <div class="relative group/field">
                                <i data-lucide="phone" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-800 group-focus-within/field:text-indigo-400 transition-all"></i>
                                <input type="text" name="phone" id="det-phone" class="w-full pl-14 pr-6 bg-slate-950 border border-slate-800 rounded-lg focus:border-indigo-600 focus:outline-none text-[15px] py-5 font-black text-white shadow-inner tabular-nums italic">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-12">
                        <div>
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] block mb-6 ml-1 italic">Financial Capital Asset</label>
                            <div class="grid grid-cols-2 gap-5">
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-indigo-500 font-black text-xl italic">€</span>
                                    <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-slate-950 border border-slate-800 rounded-lg py-5 px-6 pl-10 text-white font-black text-right outline-none focus:ring-4 focus:ring-indigo-900 focus:border-indigo-600 text-2xl shadow-inner tabular-nums italic placeholder:text-slate-800">
                                </div>
                                <select name="source" id="det-source" class="w-full bg-slate-800 border border-slate-700 rounded-lg py-5 px-4 text-[11px] text-slate-300 font-black uppercase tracking-widest outline-none focus:ring-4 focus:ring-slate-700 appearance-none text-center cursor-pointer h-16 shadow-lg">
                                    <option value="organico">ORGANIC_LOG</option>
                                    <option value="pago">PAID_ADS_MASTER</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] block mb-6 ml-1 italic">Master Logic Segments</label>
                            <input type="text" name="tags" id="det-tags" placeholder="VIP_CLIENT, URGENT_FLOW..." class="w-full bg-slate-950 border border-slate-800 rounded-lg text-[11px] py-5 px-6 text-white focus:ring-4 focus:ring-indigo-900 focus:border-indigo-600 focus:outline-none mb-6 font-black shadow-inner uppercase tracking-[0.2em] italic">
                            <div class="flex flex-wrap gap-3">
                                <?php foreach($existingTags as $tag): ?>
                                    <button type="button" onclick="addTagEdit('<?php echo $tag; ?>')" class="px-3.5 py-2.5 bg-slate-950 border border-slate-800 hover:bg-white hover:text-slate-950 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all shadow-xl">+ <?php echo $tag; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950 border border-slate-800 rounded-xl p-10 mb-14 shadow-inner">
                    <label class="text-[10px] font-black text-slate-700 uppercase tracking-[0.4em] block mb-6 ml-1 italic opacity-60">Technical Operational Logs</label>
                    <textarea name="message" id="det-message" class="w-full bg-transparent border-0 focus:ring-0 outline-none min-h-[120px] text-[16px] font-semibold text-slate-400 placeholder-slate-800 italic uppercase leading-relaxed" placeholder="ADD SYSTEM NOTES..."></textarea>
                </div>

                <div class="flex items-center justify-between mt-14 pt-10 border-t border-slate-800">
                    <button type="button" onclick="deleteLead()" class="text-slate-700 hover:text-red-500 text-[10px] font-black uppercase tracking-[0.3em] transition-all flex items-center gap-3 active:scale-95 group">
                        <i data-lucide="trash-2" class="w-5 h-5 text-red-900 group-hover:text-red-500"></i> PURGE_RECORD
                    </button>
                    <div class="flex gap-4 items-center">
                        <button type="button" onclick="closeDetailModal()" class="px-8 py-4 text-slate-600 text-[11px] font-black rounded-lg uppercase tracking-widest hover:text-white transition-colors">ABORT_SYNC</button>
                        <button type="submit" class="px-12 py-5 bg-white hover:bg-slate-200 text-slate-950 text-[12px] font-black rounded-lg uppercase tracking-[0.3em] shadow-3xl active:scale-95 border border-white">SYNC & SAVE</button>
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
            if(!confirm('¿CONFIRMAR PURGA PERMANENTE DEL REGISTRO?')) return;
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
