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

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen flex flex-col">
        <header class="mb-10 animate-in fade-in slide-in-from-top-4 duration-700">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div>
                    <h1 class="text-6xl font-black text-white tracking-tighter uppercase italic">Lista de Leads</h1>
                    <p class="mt-2 text-zinc-500 text-lg tracking-tight">Gestión avanzada de prospectos y propuestas económicas de xCloud.</p>
                </div>
                <button onclick="toggleModal()" class="px-8 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl flex items-center gap-3 transition-all transform hover:-translate-y-1 shadow-2xl shadow-blue-600/20 active:scale-95 uppercase tracking-widest text-xs">
                    <i data-lucide="shield-plus" class="w-5 h-5"></i> Registrar Nuevo Lead
                </button>
            </div>
        </header>

        <!-- Filtro Buscador -->
        <div class="mb-8 bg-zinc-950 border border-zinc-900 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="relative w-full md:w-[450px] group">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-zinc-500 group-focus-within:text-blue-500 transition-colors">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </div>
                <input type="text" id="searchLeadInput" placeholder="Buscar por nombre, etiquetas, fuente..." 
                       class="w-full pl-16 pr-6 py-4 bg-zinc-900 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-700 transition-all font-bold text-sm outline-none">
            </div>
            <div class="px-5 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl text-[10px] font-black tracking-[0.3em] text-zinc-500 uppercase">
                <span id="visibleLeadsCount" class="text-blue-500"><?php echo $result->num_rows; ?></span> Leads localizados
            </div>
        </div>

        <div class="bg-zinc-950 border border-zinc-900 rounded-[3rem] overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-900/40 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600 border-b border-zinc-900">
                            <th class="px-10 py-8">Lead / Empresa</th>
                            <th class="px-6 py-8 text-center">Fuente</th>
                            <th class="px-6 py-8 text-center">Etiquetas</th>
                            <th class="px-6 py-8 text-right">Fecha Registro</th>
                            <th class="px-10 py-8 text-right">Propuesta (€)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="lead-row hover:bg-blue-600/5 transition-all group">
                            <!-- Nombre y Empresa -->
                            <td class="px-10 py-6">
                                <div class="flex flex-col">
                                    <span class="text-white font-black text-xl leading-none group-hover:text-blue-400 transition-colors uppercase italic tracking-tighter"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="text-zinc-600 text-xs font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                                        <i data-lucide="building-2" class="w-3 h-3"></i>
                                        <?php echo $row['company'] ? htmlspecialchars($row['company']) : 'Cliente Particular'; ?>
                                        <?php if($row['website']): ?>
                                            <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank" class="text-blue-500/30 hover:text-blue-500 transition-colors ml-1">&rarr; Web</a>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Fuente -->
                            <td class="px-6 py-6 text-center">
                                <?php if($row['source'] == 'pago'): ?>
                                    <span class="px-4 py-2 bg-amber-500/5 text-amber-500 text-[10px] font-black rounded-xl border border-amber-500/10 uppercase tracking-[0.2em]">PAGO</span>
                                <?php else: ?>
                                    <span class="px-4 py-2 bg-blue-500/5 text-blue-400 text-[10px] font-black rounded-xl border border-blue-500/10 uppercase tracking-[0.2em]">ORGÁNICO</span>
                                <?php endif; ?>
                            </td>

                            <!-- Etiquetas -->
                            <td class="px-6 py-6 text-center">
                                <div class="flex flex-wrap justify-center gap-2 min-w-[140px]">
                                    <?php 
                                    $tags = !empty($row['tags']) ? explode(', ', $row['tags']) : [];
                                    foreach($tags as $tag): 
                                        $tagName = ($tag == 'metaads') ? 'Metaads' : ucfirst($tag);
                                        $tagColor = ($tag == 'metaads') ? 'blue' : 'zinc';
                                    ?>
                                        <span class="px-3 py-1.5 bg-<?php echo $tagColor; ?>-500/10 text-<?php echo $tagColor; ?>-400 text-[9px] font-black rounded-xl border border-<?php echo $tagColor; ?>-500/20 uppercase tracking-widest"><?php echo $tagName; ?></span>
                                    <?php endforeach; if(empty($tags)) echo '<span class="text-zinc-800 text-[10px] font-black tracking-widest uppercase opacity-30">---</span>'; ?>
                                 </div>
                            </td>

                            <!-- Fecha -->
                            <td class="px-6 py-6 text-right">
                                <span class="text-zinc-200 font-bold block text-sm tracking-widest"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></span>
                                <span class="text-[10px] font-black text-zinc-700 tracking-[0.2em] mt-1 block uppercase"><?php echo date('H:i', strtotime($row['created_at'])); ?>H</span>
                            </td>

                            <!-- Precio Propuesta -->
                            <td class="px-10 py-6 text-right">
                                <div class="px-5 py-3 bg-zinc-900/50 border border-zinc-900 rounded-2xl inline-block group-hover:border-blue-500/20 transition-all">
                                    <span class="text-white font-black text-xl italic tracking-tighter">
                                        <?php echo number_format($row['proposal_price'], 2, ',', '.'); ?>€
                                    </span>
                                </div>
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
