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
        .custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; border-radius: 4px; border-top: 1px solid #27272a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; border: 2px solid #09090b; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525b; }
        
        /* Master Table Dark Style */
        .excel-table { border-collapse: separate; border-spacing: 0; font-size: 12px; }
        .excel-table th, .excel-table td { border-bottom: 1px solid #27272a; border-right: 1px dotted #27272a; padding: 12px 16px; }
        .excel-table th { border-top: 1px solid #27272a; border-right: 1px solid #3f3f46; }
        
        .header-top { font-weight: 900; font-size: 13px; letter-spacing: 0.1em; }
        .header-sub { background: #4f46e5; color: #fff; font-weight: 900; text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #312e81 !important; }
        
        .cell-prod { color: #a5b4fc; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.02em; }
        .cell-val { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; text-align: center; font-size: 13px; }
        
        .perc-up { background: rgba(16, 185, 129, 0.15); color: #34d399; font-weight: 900; text-align: center; font-size: 13px; }
        .perc-down { background: rgba(244, 63, 94, 0.1); color: #fb7185; font-weight: 900; text-align: center; font-size: 13px; }
        
        .row-total td { background: #18181b; font-weight: 900; color: #fff; border-top: 2px solid #4f46e5 !important; border-bottom: none; font-size: 14px; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased font-sans flex items-start">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 flex-1 min-h-screen pt-8 px-4 lg:px-8 pb-20 flex flex-col max-w-[100vw]" id="main-content">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-zinc-900 mb-6 shrink-0">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center border border-indigo-500/30 shadow-[0_0_15px_rgba(99,102,241,0.2)]">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight italic uppercase">Analytics Command v3</h1>
                </div>
                <p class="text-[14px] text-zinc-400 font-medium">Panel Maestro C-Level Consolidado (YoY / WoW / Acumulados)</p>
            </div>
            
            <button onclick="loadAnalytics(true)" class="bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 px-5 rounded-xl transition-all shadow-[0_0_15px_rgba(79,70,229,0.3)] hover:shadow-[0_0_25px_rgba(79,70,229,0.5)] border border-indigo-400/50 flex items-center gap-3 group border-b-2">
                <i id="btn-icon" data-lucide="refresh-cw" class="w-4 h-4 transition-transform duration-500"></i> 
                <span class="text-xs font-bold uppercase tracking-widest">Sincronizar API GA4</span>
            </button>
        </header>

        <!-- Loading State -->
        <div id="loader" class="flex flex-col items-center justify-center p-24 text-zinc-500 bg-zinc-900/40 rounded-3xl border border-zinc-800/80 shadow-2xl mt-10">
            <div class="relative w-16 h-16 mb-6">
                <div class="absolute inset-0 border-4 border-indigo-500/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-indigo-500 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p id="loader-text" class="text-sm font-bold uppercase tracking-[0.2em] text-indigo-400 animate-pulse">Consultando caché de Big Data...</p>
        </div>

        <!-- Master Table Container -->
        <div class="overflow-x-auto overflow-y-hidden custom-scrollbar flex-1 pb-6 w-full" style="display: none;" id="master-container">
            <div id="tables-wrapper" class="min-w-max pb-4 h-full">
                <!-- La tabla maestra se inyecta aquí -->
            </div>
        </div>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        const date = new Date();
        const year = date.getFullYear();
        const lastYear = year - 1;

        async function loadAnalytics(isRefresh = false) {
            try {
                if (isRefresh) {
                    document.getElementById('master-container').style.display = 'none';
                    document.getElementById('loader').style.display = 'flex';
                    const icon = document.getElementById('btn-icon');
                    if(icon) icon.classList.add('animate-spin');
                    document.getElementById('loader-text').innerText = "Solicitando Big Data a Google (Hasta 30segs)...";
                }

                const endpoint = isRefresh ? 'api_ga_stats.php?refresh=true' : 'api_ga_stats.php';
                const response = await fetch(endpoint);
                const result = await response.json();
                
                if (result.status === 'success') {
                    if (isRefresh) {
                        const icon = document.getElementById('btn-icon');
                        if(icon) icon.classList.remove('animate-spin');
                    }
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('master-container').style.display = 'block';
                    renderMasterTable(result.data);
                } else {
                    document.getElementById('loader').innerHTML = `<div class="text-rose-500 font-bold p-6 bg-rose-500/10 rounded-xl border border-rose-500/30 flex items-center gap-3"><i data-lucide="alert-triangle"></i> API ERROR: \n${result.message}</div>`;
                    lucide.createIcons();
                }
            } catch (e) {
                document.getElementById('loader').innerHTML = `<div class="text-rose-500 font-bold p-6 bg-rose-500/10 rounded-xl border border-rose-500/30 flex items-center gap-3"><i data-lucide="wifi-off"></i> NETWORK ERROR: Revise su conexión o los logs. \n${e.message}</div>`;
                lucide.createIcons();
            }
        }

        function getPercClass(perc) {
            return perc >= 0 ? 'perc-up' : 'perc-down';
        }

        function getPercStr(perc) {
            return (perc >= 0 ? '+' : '') + perc + '%';
        }

        function createCells(d) {
            const pClass = getPercClass(d.perc);
            return `
                <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${d.prev.toLocaleString()}</td>
                <td class="cell-val font-bold text-white bg-zinc-800/20">${d.curr.toLocaleString()}</td>
                <td class="${pClass} border-r border-zinc-800/60 font-black shadow-[inset_1px_0_10px_rgba(0,0,0,0.1)]">${getPercStr(d.perc)}</td>
            `;
        }

        function renderMasterTable(data) {
            const wrapper = document.getElementById('tables-wrapper');
            let bodyHtml = '';
            
            // Totals Tracker
            let t = {
                w_yoy: { curr: 0, prev: 0 },
                w_wow: { curr: 0, prev: 0 },
                m_yoy: { curr: 0, prev: 0 },
                y_yoy: { curr: 0, prev: 0 }
            };

            // Alfabetico por producto
            data.sort((a, b) => a.product.localeCompare(b.product));

            data.forEach(item => {
                t.w_yoy.curr += item.semana_yoy.curr; t.w_yoy.prev += item.semana_yoy.prev;
                t.w_wow.curr += item.semana_wow.curr; t.w_wow.prev += item.semana_wow.prev;
                t.m_yoy.curr += item.mes_yoy.curr;    t.m_yoy.prev += item.mes_yoy.prev;
                t.y_yoy.curr += item.anual_yoy.curr;  t.y_yoy.prev += item.anual_yoy.prev;

                bodyHtml += `
                    <tr class="hover:bg-zinc-800/40 transition-colors group">
                        <td class="cell-prod sticky left-0 bg-zinc-950 group-hover:bg-zinc-900 z-10 shadow-[2px_0_15px_rgba(0,0,0,0.6)] border-r border-indigo-500/30">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500/50 group-hover:scale-150 group-hover:bg-indigo-400 transition-all duration-300"></div>
                                ${item.product}
                            </div>
                        </td>
                        ${createCells(item.semana_yoy)}
                        ${createCells(item.semana_wow)}
                        ${createCells(item.mes_yoy)}
                        ${createCells(item.anual_yoy)}
                    </tr>
                `;
            });

            const calcPerc = (c, p) => p > 0 ? Math.round(((c - p) / p) * 100 * 10) / 10 : 0;
            const tCells = (key) => {
                const perc = calcPerc(t[key].curr, t[key].prev);
                return `
                    <td class="cell-val border-l border-zinc-800/50">${t[key].prev.toLocaleString()}</td>
                    <td class="cell-val text-white">${t[key].curr.toLocaleString()}</td>
                    <td class="${getPercClass(perc)} border-r border-zinc-800/80 tracking-widest">${getPercStr(perc)}</td>
                `;
            };

            const tfootHtml = `
                <tr>
                    <td class="sticky left-0 bg-[#18181b] z-10 text-left px-5 tracking-widest text-[#a5b4fc] border-r border-indigo-500/50">
                        <div class="flex items-center justify-between">
                            RESUMEN GLOBAL
                            <i data-lucide="sigma" class="w-4 h-4 text-indigo-400"></i>
                        </div>
                    </td>
                    ${tCells('w_yoy')}
                    ${tCells('w_wow')}
                    ${tCells('m_yoy')}
                    ${tCells('y_yoy')}
                </tr>
            `;

            const tableHtml = `
                <div class="bg-zinc-950 rounded-2xl border-2 border-zinc-800/80 shadow-[0_10px_40px_rgba(0,0,0,0.5)] overflow-hidden inline-block h-max">
                    <table class="excel-table w-full whitespace-nowrap m-0">
                        <thead>
                            <tr>
                                <th class="border-none bg-zinc-950 sticky left-0 z-20 border-r border-indigo-500/30 shadow-[2px_0_15px_rgba(0,0,0,0.6)]"></th>
                                <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📅 SEMANA YoY</th>
                                <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">🔄 SEMANA WoW</th>
                                <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📅 ACUMULADO MES (MTD)</th>
                                <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📈 ACUMULADO ANUAL (YTD)</th>
                            </tr>
                            <tr>
                                <th class="header-sub text-left px-5 sticky left-0 z-20 shadow-[2px_0_15px_rgba(0,0,0,0.5)] tracking-widest border-r border-white/20">PRODUCTO / CAMPAÑA</th>
                                <th class="header-sub w-24 border-l border-white/10">Seman Ant</th><th class="header-sub w-24">Sem Act</th><th class="header-sub w-20 bg-indigo-600">% Var</th>
                                <th class="header-sub w-24 border-l border-white/10">Seman Ant</th><th class="header-sub w-24">Sem Act</th><th class="header-sub w-20 bg-indigo-600">% Var</th>
                                <th class="header-sub w-24 border-l border-white/10">MTD ${lastYear}</th><th class="header-sub w-24">MTD ${year}</th><th class="header-sub w-20 bg-indigo-600">% Var</th>
                                <th class="header-sub w-24 border-l border-white/10">YTD ${lastYear}</th><th class="header-sub w-24">YTD ${year}</th><th class="header-sub w-20 bg-indigo-600">% Var</th>
                            </tr>
                        </thead>
                        <tbody class="bg-zinc-950/50">
                            ${bodyHtml}
                        </tbody>
                        <tfoot class="row-total">
                            ${tfootHtml}
                        </tfoot>
                    </table>
                </div>
            `;

            wrapper.innerHTML = tableHtml;
            lucide.createIcons();
        }
    </script>
</body>
</html>
