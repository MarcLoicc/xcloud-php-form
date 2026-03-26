<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitas YoY - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
        .table-header { @apply bg-zinc-900 border-b border-zinc-800 text-[11px] font-bold text-zinc-500 uppercase tracking-widest py-4 px-6; }
        .table-cell { @apply py-6 px-6 text-[14px] border-b border-zinc-900/50 text-zinc-300 font-medium; }
        .trend-up { @apply bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded text-[11px] font-bold border border-emerald-500/20 flex items-center gap-1; }
        .trend-down { @apply bg-rose-500/10 text-rose-400 px-2 py-0.5 rounded text-[11px] font-bold border border-rose-500/20 flex items-center gap-1; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased font-sans flex items-start">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 flex-1 min-h-screen p-8 lg:p-12 mb-20" id="main-content">
        <!-- Minimal Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-10 border-b border-zinc-900 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center border border-indigo-500/20">
                        <i data-lucide="line-chart" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight italic uppercase">Métricas YoY</h1>
                </div>
                <p class="text-[14px] text-zinc-500 font-medium">Comparativa de sesiones (Últimos 30 días vs Año Anterior)</p>
            </div>
            
            <button onclick="window.location.reload()" class="bg-zinc-100 hover:bg-zinc-300 text-zinc-950 p-2.5 rounded-lg transition-all shadow-xl">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
            </button>
        </header>

        <!-- Simplified Table -->
        <section>
            <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl overflow-hidden shadow-2xl relative">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="table-header text-left pl-10 sticky left-0 bg-zinc-900 z-10 w-[300px]">Producto / Página</th>
                                <th class="table-header text-right pr-10">Visitas YoY (Últ. 30 días)</th>
                            </tr>
                        </thead>
                        <tbody id="ga-table-body">
                            <tr><td colspan="2" class="py-24 text-center text-zinc-500 font-medium animate-pulse italic">Consultando datos históricos con GA4...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        async function loadAnalytics() {
            try {
                const response = await fetch('api_ga_stats.php');
                const result = await response.json();
                
                if (result.status === 'success') {
                    renderTable(result.data);
                } else {
                    console.error('GA Error:', result.message);
                }
            } catch (error) {
                console.error('Fetch Error:', error);
            }
        }

        function renderTable(data) {
            const container = document.getElementById('ga-table-body');
            if (!data || data.length === 0) {
                container.innerHTML = '<tr><td colspan="2" class="py-20 text-center text-zinc-500 italic">No hay datos disponibles para el periodo seleccionado.</td></tr>';
                return;
            }
            container.innerHTML = '';
            
            data.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-zinc-900/80 transition-all group cursor-default';
                
                const trendClass = item.change >= 0 ? 'trend-up' : 'trend-down';
                const trendIcon = item.change >= 0 ? 'trending-up' : 'trending-down';
                
                tr.innerHTML = `
                    <td class="table-cell font-bold text-zinc-100 pl-10 sticky left-0 bg-zinc-950 group-hover:bg-zinc-900 z-10 transition-colors">
                        <div class="flex items-center gap-4">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            ${item.product}
                        </div>
                    </td>
                    <td class="table-cell text-right pr-10">
                        <div class="flex flex-col items-end">
                            <div class="flex items-center gap-3">
                                <span class="text-xl font-black text-white font-mono">${item.visits.toLocaleString()}</span>
                                <div class="${trendClass}">
                                    <i data-lucide="${trendIcon}" class="w-3 h-3"></i>
                                    ${item.change >= 0 ? '+' : ''}${item.change}%
                                </div>
                            </div>
                            <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest mt-1">vs año anterior: ${item.prev_year.toLocaleString()}</span>
                        </div>
                    </td>
                `;
                container.appendChild(tr);
            });
            lucide.createIcons();
        }

        loadAnalytics();
    </script>
</body>
</html>
