<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BI Dashboard YoY/WoW - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
        
        .table-header-group { @apply bg-zinc-900/80 border-b border-zinc-800 text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] py-3 px-4 text-center border-x border-zinc-800/30; }
        .table-header-sub { @apply bg-zinc-900 border-b border-zinc-800 text-[9px] font-bold text-zinc-500 uppercase tracking-widest py-3 px-2 text-center border-x border-zinc-800/10; }
        
        .cell-val { @apply py-4 px-2 text-[12px] text-zinc-300 font-mono text-center border-x border-zinc-900/20 group-hover:bg-zinc-900/30 transition-colors; }
        .cell-perc { @apply py-4 px-2 text-[11px] font-black text-center border-x border-zinc-900/20 group-hover:bg-zinc-900/40 transition-colors; }
        
        .bg-up { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .bg-down { background: rgba(244, 63, 94, 0.15); color: #f43f5e; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased font-sans flex items-start">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 flex-1 min-h-screen p-6 lg:p-8 mb-20" id="main-content">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-zinc-900 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center border border-indigo-500/20">
                        <i data-lucide="layout-grid" class="w-5 h-5 text-indigo-100"></i>
                    </div>
                    <h1 class="text-2xl font-black text-white tracking-tight italic uppercase">Analytics command v2</h1>
                </div>
                <p class="text-[12px] text-zinc-500 font-medium">Panel Maestro de Inteligencia de Negocio (YoY / WoW / Acumulados)</p>
            </div>
            
            <button onclick="window.location.reload()" class="bg-zinc-100 hover:bg-zinc-300 text-zinc-950 p-2 rounded-lg transition-all shadow-xl">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
            </button>
        </header>

        <!-- Master Table -->
        <section>
            <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl overflow-hidden shadow-2xl relative">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse table-fixed min-w-[1200px]">
                        <thead>
                            <!-- High Level Header -->
                            <tr>
                                <th class="bg-zinc-950 w-[200px] border-b border-zinc-800"></th>
                                <th colspan="3" class="table-header-group">Semana YoY</th>
                                <th colspan="3" class="table-header-group">Semana WoW</th>
                                <th colspan="3" class="table-header-group">Mes Actual YoY</th>
                                <th colspan="3" class="table-header-group">Acumulado Anual YoY</th>
                            </tr>
                            <!-- Sub Headers -->
                            <tr>
                                <th class="table-header-sub text-left pl-8 sticky left-0 bg-zinc-900 z-10">Producto</th>
                                <!-- Semana YoY -->
                                <th class="table-header-sub italic">Actual</th>
                                <th class="table-header-sub italic">Ant. Año</th>
                                <th class="table-header-sub">%</th>
                                <!-- Semana WoW -->
                                <th class="table-header-sub italic">Actual</th>
                                <th class="table-header-sub italic">Ant. Sem</th>
                                <th class="table-header-sub">%</th>
                                <!-- Mes YoY -->
                                <th class="table-header-sub italic">MTD</th>
                                <th class="table-header-sub italic">MTD Ant. Año</th>
                                <th class="table-header-sub">%</th>
                                <!-- Anual YoY -->
                                <th class="table-header-sub italic">YTD</th>
                                <th class="table-header-sub italic">YTD Ant. Año</th>
                                <th class="table-header-sub">%</th>
                            </tr>
                        </thead>
                        <tbody id="ga-table-body">
                            <tr><td colspan="13" class="py-24 text-center text-zinc-600 font-mono text-xs uppercase tracking-widest animate-pulse italic">Iniciando motor de BI de Google Analytics...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script>
        lucide.createIcons();

        async function loadAnalytics() {
            try {
                const response = await fetch('api_ga_stats.php');
                const result = await response.json();
                if (result.status === 'success') renderTable(result.data);
            } catch (error) { console.error('Error:', error); }
        }

        function getPercClass(val) {
            return val >= 0 ? 'bg-up' : 'bg-down';
        }

        function renderTable(data) {
            const container = document.getElementById('ga-table-body');
            container.innerHTML = '';
            
            data.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-zinc-900/20 transition-all group cursor-default';
                
                tr.innerHTML = `
                    <td class="py-4 px-6 text-[12px] font-bold text-zinc-200 sticky left-0 bg-zinc-950 group-hover:bg-zinc-900 z-10 border-b border-zinc-900 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            ${item.product}
                        </div>
                    </td>
                    <!-- Semana YoY -->
                    <td class="cell-val">${item.semana_yoy.curr}</td>
                    <td class="cell-val text-zinc-500">${item.semana_yoy.prev}</td>
                    <td class="cell-perc ${getPercClass(item.semana_yoy.perc)}">${item.semana_yoy.perc}%</td>
                    
                    <!-- Semana WoW -->
                    <td class="cell-val">${item.semana_wow.curr}</td>
                    <td class="cell-val text-zinc-500">${item.semana_wow.prev}</td>
                    <td class="cell-perc ${getPercClass(item.semana_wow.perc)}">${item.semana_wow.perc}%</td>

                    <!-- Mes YoY -->
                    <td class="cell-val">${item.mes_yoy.curr}</td>
                    <td class="cell-val text-zinc-500">${item.mes_yoy.prev}</td>
                    <td class="cell-perc ${getPercClass(item.mes_yoy.perc)}">${item.mes_yoy.perc}%</td>

                    <!-- Anual YoY -->
                    <td class="cell-val">${item.anual_yoy.curr}</td>
                    <td class="cell-val text-zinc-500">${item.anual_yoy.prev}</td>
                    <td class="cell-perc ${getPercClass(item.anual_yoy.perc)}">${item.anual_yoy.perc}%</td>
                `;
                container.appendChild(tr);
            });
            lucide.createIcons();
        }

        loadAnalytics();
    </script>
</body>
</html>
