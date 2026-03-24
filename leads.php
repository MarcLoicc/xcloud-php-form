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
        'nuevo' => ['label' => 'Nuevo Lead', 'class' => 'bg-blue-100/50 text-blue-600 border-blue-200 shadow-blue-50'],
        'no_responde' => ['label' => 'No Responde', 'class' => 'bg-slate-100/50 text-slate-500 border-slate-200'],
        'llamar_tarde' => ['label' => 'Llamar Tarde', 'class' => 'bg-amber-100/50 text-amber-600 border-amber-200 shadow-amber-50'],
        'enviar_propuesta' => ['label' => 'Enviar Prop.', 'class' => 'bg-indigo-100/50 text-indigo-600 border-indigo-200 shadow-indigo-50'],
        'propuesta_enviada' => ['label' => 'Prop. Enviada', 'class' => 'bg-purple-100/50 text-purple-600 border-purple-200 shadow-purple-50'],
        'ganado' => ['label' => 'Ganado', 'class' => 'bg-emerald-100/50 text-emerald-600 border-emerald-200 shadow-emerald-50'],
        'perdido' => ['label' => 'Perdido', 'class' => 'bg-red-100/50 text-red-600 border-red-200 shadow-red-50'],
        'no_cualificado' => ['label' => 'No Cualificado', 'class' => 'bg-zinc-100/50 text-zinc-400 border-zinc-200'],
        'interesado_tarde' => ['label' => 'Interesado +', 'class' => 'bg-cyan-100/50 text-cyan-600 border-cyan-200 shadow-cyan-50'],
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
    <title>Leads Premium - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-[#f0f2f5] min-h-screen text-[#1e293b]">
    <!-- Decorative Blurs -->
    <div class="fixed inset-0 z-[-1] pointer-events-none opacity-40">
      <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/20 blur-[120px] rounded-full"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-400/20 blur-[120px] rounded-full"></div>
    </div>

    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-72 min-h-screen p-8 lg:p-14 space-y-12">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-10">
            <div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-xl shadow-indigo-50 border border-white">
                        <i data-lucide="users" class="w-7 h-7 stroke-[2.5]"></i>
                    </div>
                    <h1 class="text-4xl font-black text-slate-800 tracking-tighter italic">Gestión de <span class="text-indigo-600 not-italic">Leads</span></h1>
                </div>
                <p class="text-slate-500 font-medium max-w-sm mt-3 ml-1">Filtra, analiza y evoluciona tus prospectos con el nuevo motor de flujo comercial.</p>
            </div>
            
            <div class="flex gap-4">
                <button class="px-6 py-4 bg-white border border-white/80 rounded-3xl text-[10px] font-black text-slate-400 hover:text-slate-900 transition-all shadow-xl shadow-slate-100 flex items-center gap-2 group uppercase tracking-widest active:scale-95">
                    <i data-lucide="download" class="w-4 h-4 group-hover:translate-y-0.5 transition-all"></i> EXPORTAR
                </button>
                <button onclick="toggleModal()" class="px-10 py-4 bg-indigo-600 rounded-3xl text-sm font-black text-white hover:bg-slate-900 transition-all shadow-2xl shadow-indigo-100 flex items-center gap-3 active:scale-95 uppercase tracking-widest">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i> Nuevo Lead
                </button>
            </div>
        </header>

        <!-- Premium Filter Bar -->
        <section class="flex flex-col lg:flex-row items-center justify-between gap-6 p-6 glass-card rounded-[3rem] shadow-2xl shadow-slate-200/50">
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <div class="relative w-full lg:w-96 group">
                    <i data-lucide="search" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-all"></i>
                    <input type="text" id="filterGlobal" placeholder="Búsqueda estratégica..." class="w-full pl-14 pr-6 py-4 bg-white/50 border border-white rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-600 outline-none transition-all text-sm font-bold text-slate-800 placeholder-slate-400 shadow-sm">
                </div>
            </div>

            <div class="flex items-center gap-4 w-full lg:w-auto overflow-x-auto pb-2 lg:pb-0 scrollbar-none">
                <select id="filterStatus" class="bg-white/60 border border-white rounded-2xl px-5 py-3 text-[10px] font-black text-slate-600 uppercase tracking-widest outline-none focus:ring-4 focus:ring-indigo-100 appearance-none cursor-pointer h-12">
                    <option value="all">TODOS LOS ESTADOS</option>
                    <option value="nuevo">Nuevo</option>
                    <option value="no_responde">No Responde</option>
                    <option value="ganado">Ganado</option>
                    <option value="perdido">Perdido</option>
                </select>
                <select id="filterSource" class="bg-white/60 border border-white rounded-2xl px-5 py-3 text-[10px] font-black text-slate-600 uppercase tracking-widest outline-none focus:ring-4 focus:ring-indigo-100 appearance-none cursor-pointer h-12">
                    <option value="all">TODAS LAS FUENTES</option>
                    <option value="pago">PAGO ADS</option>
                    <option value="organico">ORGÁNICO</option>
                </select>
                <div class="bg-indigo-600 rounded-2xl px-6 py-2.5 text-center shadow-lg shadow-indigo-100 flex flex-col justify-center min-w-[80px]">
                    <span id="visibleLeadsCount" class="text-xl font-black text-white tabular-nums leading-none"><?php echo $result->num_rows; ?></span>
                    <span class="text-[7px] font-black text-indigo-200 uppercase tracking-widest block mt-1">Total</span>
                </div>
            </div>
        </section>

        <!-- Premium Table Container -->
        <div class="relative glass-card rounded-[3.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden group">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-indigo-50/20 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-white/60">
                            <th class="px-10 py-7">Lead / Organización</th>
                            <th class="px-6 py-7 text-center">Estado Flujo</th>
                            <th class="px-6 py-7 text-center">Origen Adquisición</th>
                            <th class="px-6 py-7 text-right">Valor Negocio</th>
                            <th class="px-10 py-7 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/40">
                        <?php while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $rowDate = date('Y-m-d', strtotime($row['created_at']));
                            $statusInfo = getStatusBadge($row['status'] ?? 'nuevo');
                        ?>
                        <tr class="lead-row hover:bg-white/80 transition-all group/row cursor-pointer" 
                            onclick='showLeadDetails(<?php echo $json_data; ?>)'
                            data-source="<?php echo $row['source']; ?>"
                            data-price="<?php echo $row['proposal_price'] ?? 0; ?>"
                            data-date="<?php echo $rowDate; ?>"
                            data-status="<?php echo $row['status'] ?? 'nuevo'; ?>">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 bg-indigo-50 border-2 border-white rounded-[1.8rem] flex items-center justify-center text-indigo-600 font-black text-xl shadow-sm group-hover/row:scale-110 group-hover/row:bg-indigo-600 group-hover/row:text-white transition-all uppercase">
                                        <?php echo substr($row['name'], 0, 1); ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-lg font-black text-slate-800 group-hover/row:text-indigo-600 transition-all tracking-tight leading-none italic uppercase"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <div class="flex items-center gap-3 mt-2.5">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo htmlspecialchars($row['company'] ?: 'Particular'); ?></span>
                                            <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                            <span class="text-[9px] font-black text-indigo-400 tabular-nums"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-8 text-center">
                                <span class="px-4 py-2 rounded-2xl border text-[9px] font-black uppercase tracking-widest shadow-sm <?php echo $statusInfo['class']; ?>">
                                    <?php echo $statusInfo['label']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-8 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <?php if($row['source'] == 'pago'): ?>
                                        <i data-lucide="zap" class="w-4 h-4 text-amber-500 fill-amber-300/30 font-bold"></i>
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-500">PAID ADS</span>
                                    <?php else: ?>
                                        <i data-lucide="leaf" class="w-4 h-4 text-emerald-500 fill-emerald-300/30"></i>
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-500">ORGÁNICO</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-8 text-right">
                                <span class="text-2xl font-black text-slate-800 tracking-tighter tabular-nums italic group-hover/row:text-indigo-600 transition-all"><?php echo number_format($row['proposal_price'], 0, ',', '.'); ?>€</span>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <button class="p-4 bg-slate-50 hover:bg-slate-900 hover:text-white rounded-[1.5rem] transition-all shadow-sm group-hover/row:shadow-xl group-hover/row:shadow-indigo-50/50 group/btn">
                                    <i data-lucide="more-horizontal" class="w-6 h-6 group-hover/btn:scale-125 transition-all"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="p-8 bg-white/20 border-t border-white/60 flex justify-between items-center">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic px-2">Mostrando <span class="text-slate-900 not-italic"><?php echo $result->num_rows; ?></span> prospectos de alto valor monitorizados</span>
                <div class="flex gap-2">
                    <button class="px-6 py-3 bg-white border border-white rounded-2xl text-[10px] font-black text-slate-400 hover:text-slate-900 hover:border-slate-300 transition-all active:scale-95 uppercase tracking-widest">Atrás</button>
                    <button class="px-6 py-3 bg-slate-900 border border-slate-800 text-[10px] font-black text-white rounded-2xl hover:bg-black transition-all active:scale-95 uppercase tracking-widest shadow-xl shadow-slate-200">Siguiente</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Detalle/Edición -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xl p-4 overflow-y-auto">
        <div class="bg-white/90 border border-white w-full max-w-2xl rounded-[3rem] shadow-4xl p-12 transform transition-all animate-in zoom-in duration-300 shadow-[0_50px_100px_-20px_rgba(30,41,59,0.3)]">
            <form id="editLeadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="det-id">
                
                <div class="flex justify-between items-start mb-10">
                    <div class="w-full mr-10 group">
                        <label class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] block mb-3 ml-1">Perfil Corporativo</label>
                        <input type="text" name="name" id="det-name" placeholder="Nombre completo" class="w-full bg-transparent border-b-2 border-slate-50 focus:border-indigo-600 focus:outline-none text-4xl font-black text-slate-900 transition-all px-0 py-2 uppercase tracking-tighter italic">
                        <input type="text" name="company" id="det-company" placeholder="Empresa u Organización" class="w-full bg-transparent border-b border-transparent hover:border-slate-50 focus:border-indigo-600 focus:outline-none text-slate-500 text-lg mt-3 px-0 py-1 font-bold group-hover:text-slate-700">
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="p-4 bg-slate-50 hover:bg-slate-100 rounded-[2rem] transition-all text-slate-300 hover:text-slate-900 active:scale-90"><i data-lucide="x" class="w-6 h-6"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                    <div class="space-y-10">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-4 ml-1">Estado Operativo</label>
                            <select name="status" id="det-status" class="w-full bg-indigo-600 border border-indigo-500 rounded-[2rem] py-5 px-6 text-[10px] text-white font-black uppercase outline-none focus:ring-8 focus:ring-indigo-100 transition-all shadow-2xl shadow-indigo-100/50 appearance-none tracking-widest cursor-pointer">
                                <option value="nuevo">Nuevo lead</option>
                                <option value="no_responde">No responde</option>
                                <option value="enviar_propuesta">Enviar prop.</option>
                                <option value="propuesta_enviada">Prop. enviada</option>
                                <option value="ganado">Ganado</option>
                                <option value="perdido">Perdido</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-4 ml-1">Canales de Contacto</label>
                            <div class="space-y-5">
                                <div class="relative group">
                                    <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-4 text-slate-300 group-focus-within:text-indigo-400 transition-all"></i>
                                    <input type="text" name="email" id="det-email" class="w-full pl-10 pr-4 bg-slate-50 border border-slate-100 focus:border-indigo-600 focus:bg-white focus:outline-none text-[13px] py-4 rounded-2xl font-bold text-slate-700 shadow-inner">
                                </div>
                                <div class="relative group">
                                    <i data-lucide="phone" class="w-4 h-4 absolute left-3 top-4 text-slate-300 group-focus-within:text-indigo-400 transition-all"></i>
                                    <input type="text" name="phone" id="det-phone" class="w-full pl-10 pr-4 bg-slate-50 border border-slate-100 focus:border-indigo-600 focus:bg-white focus:outline-none text-[13px] py-4 rounded-2xl font-bold text-slate-700 shadow-inner">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-10">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-4 ml-1">Cierre Estratégico</label>
                            <div class="grid grid-cols-2 gap-5">
                                <div class="relative group">
                                    <span class="absolute left-5 top-5 text-indigo-300 font-black text-sm">€</span>
                                    <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-slate-900 border border-slate-800 rounded-[1.8rem] py-5 px-6 pl-10 text-white font-black text-right outline-none focus:ring-4 focus:ring-indigo-100/10 text-xl transition-all shadow-xl shadow-slate-200">
                                </div>
                                <select name="source" id="det-source" class="w-full bg-slate-100 border border-slate-200 rounded-[1.8rem] py-5 px-5 text-[9px] text-slate-600 font-black uppercase tracking-widest outline-none focus:ring-4 focus:ring-indigo-100/5 transition-all text-center h-[68px] cursor-pointer">
                                    <option value="organico">Orgánico</option>
                                    <option value="pago">Pago Ads</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-4 ml-1">Segmentación de Etiquetas</label>
                            <input type="text" name="tags" id="det-tags" placeholder="VIP, Prioritario..." class="w-full bg-slate-50 border border-slate-100 rounded-2xl text-[11px] py-4 px-5 text-slate-900 focus:ring-4 focus:ring-indigo-100 focus:bg-white outline-none mb-4 font-bold shadow-inner uppercase tracking-wider">
                            <div class="flex flex-wrap gap-2 px-1">
                                <?php foreach($existingTags as $tag): ?>
                                    <button type="button" onclick="addTagEdit('<?php echo $tag; ?>')" class="px-3 py-1.5 bg-white border border-slate-100 hover:bg-slate-900 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-sm hover:shadow-lg">+ <?php echo $tag; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50/20 border border-indigo-100/50 rounded-[2.5rem] p-8 mb-10 overflow-hidden relative group/obs">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover/obs:opacity-40 transition-opacity"></div>
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] block mb-5 ml-1 relative z-10">Análisis y Notas Estratégicas</label>
                    <textarea name="message" id="det-message" class="w-full bg-transparent border-0 focus:ring-0 outline-none min-h-[120px] text-[15px] font-bold italic text-slate-600 placeholder-slate-300 relative z-10" placeholder="Añade observaciones sobre el seguimiento..."></textarea>
                </div>

                <div class="flex items-center justify-between mt-12">
                    <button type="button" onclick="deleteLead()" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-[0.3em] transition-all flex items-center gap-3 active:scale-95 group">
                        <i data-lucide="trash-2" class="w-4 h-4 group-hover:rotate-12 transition-all"></i> Eliminar Lead
                    </button>
                    <div class="flex gap-4 items-center">
                        <div id="saveStatus" class="text-[10px] font-black uppercase tracking-widest text-emerald-400 hidden animate-pulse mr-4 italic">Sincronizando Cloud...</div>
                        <button type="button" onclick="closeDetailModal()" class="px-10 py-4.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-black rounded-3xl transition-all uppercase tracking-widest">Cerrar</button>
                        <button type="submit" class="px-12 py-4.5 bg-slate-900 hover:bg-black text-white text-[10px] font-black rounded-3xl transition-all uppercase tracking-widest shadow-2xl shadow-slate-300 active:scale-95">Guardar Cambios</button>
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
            if(!confirm('¿Eliminar prospecto del historial?')) return;
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
            const sts = document.getElementById('saveStatus');
            sts.classList.remove('hidden');
            const fd = new FormData(this);
            fetch('update_lead.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.success) location.reload();
                else sts.classList.add('hidden');
            }).catch(() => sts.classList.add('hidden'));
        });

        window.onclick = e => { if(e.target == modalD) closeDetailModal(); }
    </script>
</body>
</html>
<?php $conn->close(); ?>
