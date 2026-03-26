<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BI Dashboard - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 4px; }
        
        /* Dark Theme Excel Style */
        .excel-table { border-collapse: separate; border-spacing: 0; min-width: 480px; font-size: 12px; }
        .excel-table th, .excel-table td { border-bottom: 1px solid #27272a; border-right: 1px solid #27272a; padding: 12px 14px; }
        .excel-table th { border-top: 1px solid #27272a; }
        .excel-table th:first-child, .excel-table td:first-child { border-left: 1px solid #27272a; }
        
        .header-top { background: #18181b; color: #fff; text-align: left; font-weight: 900; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; }
        .header-sub { background: #4f46e5; color: #fff; font-weight: bold; text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
        
        .cell-prod { text-align: left; color: #a5b4fc; font-weight: 600; text-decoration: none; white-space: nowrap; transition: color 0.2s; }
        .cell-prod:hover { color: #c7d2fe; }
        .cell-val { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; text-align: center; color: #e4e4e7; }
        
        .perc-up { background: rgba(16, 185, 129, 0.1); color: #34d399; font-weight: 900; text-align: center; }
        .perc-down { background: rgba(244, 63, 94, 0.1); color: #fb7185; font-weight: 900; text-align: center; }
        
        .row-total td { background: #18181b; font-weight: 900; color: #fff; border-top: 2px solid #3f3f46; border-bottom: none; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased font-sans flex items-start">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 flex-1 min-h-screen p-8 lg:p-12 mb-20 overflow-hidden flex flex-col" id="main-content">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8 shrink-0">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center border border-indigo-500/20">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight italic uppercase">Analytics Command v2</h1>
                </div>
                <p class="text-[14px] text-zinc-400 font-medium">Panel Maestro C-Level (YoY / WoW / Acumulados)</p>
            </div>
            
            <button onclick="window.location.reload()" class="bg-zinc-900 hover:bg-zinc-800 text-white p-2.5 rounded-lg transition-all shadow-xl border border-zinc-800 flex items-center gap-2 group">
                <i data-lucide="refresh-cw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"></i> 
                <span class="text-xs font-bold uppercase tracking-widest">Sincronizar GA4</span>
            </button>
        </header>

        <!-- Loading State -->
        <div id="loader" class="flex flex-col items-center justify-center p-20 text-zinc-500 bg-zinc-900/30 rounded-2xl border border-zinc-800/50">
            <i data-lucide="loader-2" class="w-10 h-10 animate-spin mb-4 text-indigo-500"></i>
            <p class="text-sm font-medium uppercase tracking-widest animate-pulse">Sintetizando datos históricos en GA4 API...</p>
        </div>

        <!-- Master Tables Container -->
        <div class="overflow-x-auto custom-scrollbar flex-1 pb-6" style="display: none;" id="master-container">
            <div class="flex flex-nowrap gap-8 min-w-max pb-4" id="tables-wrapper">
                <!-- Las tablas se inyectan dinámicamente aquí -->
            </div>
        </div>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        // Calculo de años dinámico para los headers
        const date = new Date();
        const year = date.getFullYear();
        const lastYear = year - 1;

        async function loadAnalytics() {
            try {
                const response = await fetch('api_ga_stats.php');
                const result = await response.json();
                
                if (result.status === 'success') {
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('master-container').style.display = 'block';
                    renderAll(result.data);
                } else {
                    document.getElementById('loader').innerHTML = `<div class="text-rose-500 font-bold p-4 bg-rose-500/10 rounded-lg border border-rose-500/20">API ERROR: ${result.message}</div>`;
                }
            } catch (e) {
                document.getElementById('loader').innerHTML = `<div class="text-rose-500 font-bold p-4 bg-rose-500/10 rounded-lg border border-rose-500/20">NETWORK ERROR: ${e.message}</div>`;
            }
        }

        function createTableHtml(title, p1Label, p2Label, dataKey, data) {
            let bodyHtml = '';
            let tCurr = 0, tPrev = 0;

            // Orden alfabético por producto
            data.sort((a, b) => a.product.localeCompare(b.product));

            data.forEach(item => {
                const d = item[dataKey];
                const pClass = d.perc >= 0 ? 'perc-up' : 'perc-down';
                tCurr += d.curr; tPrev += d.prev;
                
                bodyHtml += `
                    <tr class="hover:bg-zinc-900/50 transition-colors">
                        <td class="cell-prod"><div class="flex items-center gap-2"><div class="w-1 h-1 rounded-full bg-indigo-500"></div>${item.product}</div></td>
                        <td class="cell-val text-zinc-500">${d.prev.toLocaleString()}</td>
                        <td class="cell-val font-bold text-white">${d.curr.toLocaleString()}</td>
                        <td class="${pClass}">${d.perc >= 0 ? '+' : ''}${d.perc}%</td>
                    </tr>
                `;
            });

            // Calculate total percentage safely
            const totalPerc = tPrev > 0 ? Math.round(((tCurr - tPrev) / tPrev) * 100 * 10) / 10 : 0;
            const tPClass = totalPerc >= 0 ? 'perc-up' : 'perc-down';

            // Return individual table component
            return `
                <div class="bg-zinc-950 rounded-2xl border border-zinc-800 overflow-hidden shadow-2xl flex-shrink-0">
                    <table class="excel-table w-full">
                        <thead>
                            <tr>
                                <th class="header-top border-none" colspan="4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                                            ${title}
                                        </div>
                                        <i data-lucide="bar-chart" class="w-4 h-4 text-zinc-600"></i>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class="header-sub" style="text-align: left; padding-left: 20px;">Producto Base</th>
                                <th class="header-sub w-28">${p1Label}</th>
                                <th class="header-sub w-28">${p2Label}</th>
                                <th class="header-sub w-24">% Var</th>
                            </tr>
                        </thead>
                        <tbody class="bg-zinc-950">
                            ${bodyHtml}
                        </tbody>
                        <tfoot class="row-total">
                            <tr>
                                <td style="text-align:left; padding-left: 20px;">RESUMEN ACUMULADO</td>
                                <td class="cell-val">${tPrev.toLocaleString()}</td>
                                <td class="cell-val text-white">${tCurr.toLocaleString()}</td>
                                <td class="${tPClass}">${totalPerc >= 0 ? '+' : ''}${totalPerc}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
        }

        function renderAll(data) {
            const wrapper = document.getElementById('tables-wrapper');
            
            // Build 4 instances matching the requested format
            const h1 = createTableHtml('Semana YoY', 'Seman Ant Año', 'Semana Actual', 'semana_yoy', data);
            const h2 = createTableHtml('Semana WoW', 'Semana Ant', 'Semana Actual', 'semana_wow', data);
            const h3 = createTableHtml('Acumulado Mes', 'MTD ' + lastYear, 'MTD ' + year, 'mes_yoy', data);
            const h4 = createTableHtml('Acumulado Anual', 'YTD ' + lastYear, 'YTD ' + year, 'anual_yoy', data);
            
            wrapper.innerHTML = h1 + h2 + h3 + h4;
            lucide.createIcons();
        }

        // Initialize Data Fetch
        loadAnalytics();
    </script>
</body>
</html>
