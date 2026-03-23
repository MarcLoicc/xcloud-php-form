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
                    </thead>
                    <tbody class="divide-y divide-border">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="lead-row hover:bg-zinc-900/40 transition-all group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-white group-hover:text-primary transition-colors"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="text-xs text-zinc-500 mt-0.5 flex items-center gap-1.5 uppercase tracking-tighter">
                                        <?php echo $row['company'] ? htmlspecialchars($row['company']) : 'Particular'; ?>
                                        <?php if($row['website']): ?>
                                            <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank" class="text-zinc-700 hover:text-primary transition-colors">&middot; web</a>
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
                                        <span class="px-2 py-0.5 bg-zinc-800 text-zinc-400 text-[9px] font-semibold rounded border border-border"><?php echo $tagName; ?></span>
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
                        <i data-lucide="layers" class="w-16 h-16 mx-auto mb-6 opacity-5"></i>
                        <h3 class="font-black text-xl mb-4 tracking-[0.2em] opacity-10">SISTEMA VACÍO</h3>
                        <button onclick="toggleModal()" class="text-blue-600 font-bold hover:text-blue-500 underline underline-offset-8 transition-all">INICIAR MI EXPLOTACIÓN DE LEADS &rarr;</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        
        const searchInput = document.getElementById('searchLeadInput');
        const countSpan = document.getElementById('visibleLeadsCount');
        const rows = document.querySelectorAll('tbody tr.lead-row');

        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                let visible = 0;
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    if(text.includes(term)) {
                        row.style.display = '';
                        visible++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                if(countSpan) countSpan.textContent = visible;
            });
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
