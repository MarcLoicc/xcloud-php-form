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
        'nuevo' => ['label' => 'New', 'class' => 'bg-indigo-900/30 text-indigo-300 ring-1 ring-inset ring-indigo-500/30'],
        'no_responde' => ['label' => 'No Response', 'class' => 'bg-zinc-800 text-zinc-300 ring-1 ring-inset ring-zinc-600'],
        'llamar_tarde' => ['label' => 'Follow Up', 'class' => 'bg-amber-900/30 text-amber-300 ring-1 ring-inset ring-amber-500/30'],
        'enviar_propuesta' => ['label' => 'Proposal', 'class' => 'bg-blue-900/30 text-blue-300 ring-1 ring-inset ring-blue-500/30'],
        'propuesta_enviada' => ['label' => 'Sent', 'class' => 'bg-purple-900/30 text-purple-300 ring-1 ring-inset ring-purple-500/30'],
        'ganado' => ['label' => 'Won', 'class' => 'bg-emerald-900/30 text-emerald-300 ring-1 ring-inset ring-emerald-500/30'],
        'perdido' => ['label' => 'Lost', 'class' => 'bg-red-900/30 text-red-300 ring-1 ring-inset ring-red-500/30'],
        'no_cualificado' => ['label' => 'Unqualified', 'class' => 'bg-zinc-800 text-zinc-400 ring-1 ring-inset ring-zinc-700'],
        'interesado_tarde' => ['label' => 'Postponed', 'class' => 'bg-cyan-900/30 text-cyan-300 ring-1 ring-inset ring-cyan-500/30'],
    ];
    return $map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'class' => 'bg-zinc-800 text-zinc-300 ring-1 ring-inset ring-zinc-600'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Acme SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12 mb-20" id="main-content">
        <!-- Leads Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Customers</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Manage your client registry and track statuses.</p>
            </div>
            
            <div class="flex gap-3">
                <button class="px-4 py-2 bg-zinc-900 border border-zinc-800 rounded-md text-[14px] font-semibold text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800 transition-colors flex items-center gap-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                    <i data-lucide="download" class="w-4 h-4" aria-hidden="true"></i> Export
                </button>
                <button onclick="toggleModal()" aria-haspopup="dialog" aria-expanded="false" aria-controls="addLeadModal" class="px-4 py-2 bg-zinc-100 rounded-md text-[14px] font-bold text-zinc-950 hover:bg-zinc-300 transition-colors flex items-center gap-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                    <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i> Create New
                </button>
            </div>
        </header>

        <!-- Search Controls -->
        <section aria-labelledby="filters-heading" class="mb-6 flex flex-col lg:flex-row items-center justify-between gap-4">
            <h2 id="filters-heading" class="sr-only">Table Filters</h2>
            
            <div class="w-full lg:w-1/3 relative">
                <label for="filterGlobal" class="sr-only">Search customers</label>
                <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                <input type="text" id="filterGlobal" placeholder="Search by name or company..." class="w-full pl-10 pr-4 py-2 bg-zinc-900 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-500">
            </div>

            <div class="flex items-center gap-3 w-full lg:w-auto overflow-x-auto pb-2 lg:pb-0 scrollbar-none">
                <div class="relative">
                    <label for="filterStatus" class="sr-only">Filter by status</label>
                    <select id="filterStatus" class="bg-zinc-900 border border-zinc-800 rounded-md pl-4 pr-10 py-2 text-[14px] font-medium text-zinc-300 focus:ring-2 focus:ring-indigo-500 transition-colors appearance-none cursor-pointer">
                        <option value="all">Every status</option>
                        <option value="nuevo">New</option>
                        <option value="ganado">Won</option>
                        <option value="perdido">Lost</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                </div>
                
                <div class="relative">
                    <label for="filterSource" class="sr-only">Filter by source</label>
                    <select id="filterSource" class="bg-zinc-900 border border-zinc-800 rounded-md pl-4 pr-10 py-2 text-[14px] font-medium text-zinc-300 focus:ring-2 focus:ring-indigo-500 transition-colors appearance-none cursor-pointer">
                        <option value="all">Every source</option>
                        <option value="pago">Paid Ads</option>
                        <option value="organico">Organic</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-md px-4 py-2 flex items-center gap-2" aria-live="polite" aria-atomic="true">
                    <span id="visibleLeadsCount" class="text-[14px] font-bold text-zinc-100"><?php echo $result->num_rows; ?></span>
                    <span class="text-[14px] text-zinc-500">records</span>
                </div>
            </div>
        </section>

        <!-- Data Table -->
        <section aria-labelledby="table-heading" class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <h2 id="table-heading" class="sr-only">Customer Database</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-950/50 border-b border-zinc-800">
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Source</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Value</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Added</th>
                            <th scope="col" class="sr-only">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        <?php while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $rowDate = date('Y-m-d', strtotime($row['created_at']));
                            $statusInfo = getStatusBadge($row['status'] ?? 'nuevo');
                        ?>
                        <tr class="lead-row hover:bg-zinc-800/30 transition-colors group" 
                            data-source="<?php echo $row['source']; ?>"
                            data-price="<?php echo $row['proposal_price'] ?? 0; ?>"
                            data-date="<?php echo $rowDate; ?>"
                            data-status="<?php echo $row['status'] ?? 'nuevo'; ?>">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center text-[12px] font-bold text-zinc-300" aria-hidden="true">
                                        <?php echo substr($row['name'], 0, 1); ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[14px] font-bold text-zinc-100"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <span class="text-[12px] text-zinc-500"><?php echo htmlspecialchars($row['company'] ?: 'Individual'); ?></span>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[12px] font-medium <?php echo $statusInfo['class']; ?>">
                                    <?php echo $statusInfo['label']; ?>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <?php if($row['source'] == 'pago'): ?>
                                        <i data-lucide="target" class="w-3.5 h-3.5 text-zinc-400" aria-hidden="true"></i>
                                        <span class="text-[13px] text-zinc-300">Paid</span>
                                    <?php else: ?>
                                        <i data-lucide="globe" class="w-3.5 h-3.5 text-zinc-400" aria-hidden="true"></i>
                                        <span class="text-[13px] text-zinc-300">Organic</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <span class="text-[14px] font-medium text-zinc-200">€<?php echo number_format($row['proposal_price'] ?? 0, 2, '.', ','); ?></span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="text-[13px] text-zinc-400"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <button onclick='showLeadDetails(<?php echo $json_data; ?>)' aria-label="Edit <?php echo htmlspecialchars($row['name']); ?>" class="p-2 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                                    <i data-lucide="edit-2" class="w-4 h-4" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-zinc-800 flex items-center justify-between text-[13px] text-zinc-500 border-t border-zinc-800">
                <p>End of results.</p>
            </div>
        </section>
    </main>

    <!-- Modal Detail SaaS Accessible -->
    <div id="detailModal" role="dialog" aria-modal="true" aria-labelledby="modal-title" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-xl shadow-2xl p-8 transform transition-all animate-in zoom-in duration-200" id="detailModalContent" tabindex="-1">
            <form id="editLeadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="det-id">
                
                <div class="flex justify-between items-start mb-8 pb-6 border-b border-zinc-800">
                    <div class="w-full mr-8">
                        <label for="det-name" class="sr-only">Customer Name</label>
                        <input type="text" name="name" id="det-name" class="w-full bg-transparent border-0 focus:ring-2 focus:ring-indigo-500 rounded-md text-2xl font-bold text-zinc-100 transition-colors px-2 py-1 -ml-2" required>
                        
                        <label for="det-company" class="sr-only">Company</label>
                        <input type="text" name="company" id="det-company" class="w-full bg-transparent border-0 focus:ring-2 focus:ring-indigo-500 rounded-md text-zinc-400 text-[15px] mt-1 px-2 py-1 -ml-2 transition-colors">
                    </div>
                    <button type="button" onclick="closeDetailModal()" aria-label="Close modal" class="p-2 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                        <i data-lucide="x" class="w-5 h-5" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-6">
                        <div>
                            <label for="det-status" class="block text-[13px] font-semibold text-zinc-300 mb-2">Stage</label>
                            <select name="status" id="det-status" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 px-3 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors">
                                <option value="nuevo">New</option>
                                <option value="no_responde">No Response</option>
                                <option value="enviar_propuesta">Proposal</option>
                                <option value="propuesta_enviada">Sent</option>
                                <option value="ganado">Won</option>
                                <option value="perdido">Lost</option>
                            </select>
                        </div>
                        
                        <fieldset class="space-y-4">
                            <legend class="block text-[13px] font-semibold text-zinc-300 mb-2">Contact Info</legend>
                            <div class="relative">
                                <label for="det-email" class="sr-only">Email address</label>
                                <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                                <input type="email" name="email" id="det-email" class="w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600">
                            </div>
                            <div class="relative">
                                <label for="det-phone" class="sr-only">Phone number</label>
                                <i data-lucide="phone" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                                <input type="tel" name="phone" id="det-phone" class="w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600">
                            </div>
                        </fieldset>
                    </div>

                    <div class="space-y-6">
                        <fieldset>
                            <legend class="block text-[13px] font-semibold text-zinc-300 mb-2">Deal Details</legend>
                            <div class="flex gap-3">
                                <div class="relative flex-1">
                                    <label for="det-price" class="sr-only">Proposal Price</label>
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-[14px]" aria-hidden="true">€</span>
                                    <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 pl-8 pr-3 text-zinc-100 font-medium focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px]">
                                </div>
                                <div class="w-1/3">
                                    <label for="det-source" class="sr-only">Source</label>
                                    <select name="source" id="det-source" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 px-3 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors">
                                        <option value="organico">Organic</option>
                                        <option value="pago">Paid</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <div>
                            <label for="det-tags" class="block text-[13px] font-semibold text-zinc-300 mb-2">Tags</label>
                            <input type="text" name="tags" id="det-tags" placeholder="e.g. vip, pending" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 px-3 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors mb-3">
                            <div class="flex flex-wrap gap-2" role="group" aria-label="Suggested Tags">
                                <?php foreach($existingTags as $tag): ?>
                                    <button type="button" onclick="addTagEdit('<?php echo htmlspecialchars($tag); ?>')" class="px-2.5 py-1 bg-zinc-800 border border-zinc-700 hover:bg-zinc-700 rounded text-[12px] font-medium text-zinc-300 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                                        + <?php echo htmlspecialchars($tag); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <label for="det-message" class="block text-[13px] font-semibold text-zinc-300 mb-2">Notes</label>
                    <textarea name="message" id="det-message" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-3 px-3 focus:ring-2 focus:ring-indigo-500 outline-none min-h-[100px] text-[14px] text-zinc-100 transition-colors placeholder:text-zinc-600"></textarea>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-zinc-800">
                    <button type="button" onclick="deleteLead()" class="text-red-400 hover:text-red-300 text-[14px] font-semibold transition-colors flex items-center gap-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-red-500 rounded-md px-2 py-1">
                        <i data-lucide="trash-2" class="w-4 h-4" aria-hidden="true"></i> Delete
                    </button>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-zinc-400 text-[14px] font-semibold rounded-md hover:text-zinc-100 hover:bg-zinc-800 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-zinc-500">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-zinc-100 hover:bg-zinc-300 text-zinc-950 text-[14px] font-bold rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">Save Changes</button>
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

        /* Accessibile Filtering via JS */
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

        /* Accessible Modal Focus Trapping / Handling */
        const modalD = document.getElementById('detailModal');
        const modalContent = document.getElementById('detailModalContent');
        const editLeadForm = document.getElementById('editLeadForm');
        let previousActiveElement;
        
        function showLeadDetails(data) {
            previousActiveElement = document.activeElement; // Save previously focused element
            
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
            
            // Set focus inside modal
            setTimeout(() => { document.getElementById('det-name').focus(); }, 50);
        }

        function closeDetailModal() {
            modalD.classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (previousActiveElement) {
                previousActiveElement.focus();
            }
        }

        function deleteLead() {
            const id = document.getElementById('det-id').value;
            if(!confirm('Are you sure you want to permanently delete this record?')) return;
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

        // Close on ESC and backdrop click
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modalD.classList.contains('hidden')) {
                closeDetailModal();
            }
        });
        
        modalD.addEventListener('click', (e) => {
            if (e.target === modalD) closeDetailModal();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
