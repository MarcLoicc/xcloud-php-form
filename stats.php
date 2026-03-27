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
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap');
        
        :root {
            font-family: 'Montserrat', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; border-radius: 4px; border-top: 1px solid #27272a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; border: 2px solid #09090b; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525b; }
        
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
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased flex items-start">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 flex-1 min-h-screen pt-8 px-4 lg:px-8 pb-20 flex flex-col max-w-[100vw]" id="main-content">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-zinc-900 mb-6 shrink-0">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center border border-indigo-500/30">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight italic uppercase">Analytics Command v4</h1>
                </div>
                <p class="text-[14px] text-zinc-400 font-medium">Motor Paralelizado de Big Data en Tiempo Real</p>
            </div>
            
            <button onclick="loadAnalytics(true)" class="bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 px-5 rounded-xl border border-indigo-400/50 flex items-center gap-3 group border-b-2">
                <i id="btn-icon" data-lucide="refresh-cw" class="w-4 h-4 transition-transform duration-500"></i> 
                <span class="text-xs font-bold uppercase tracking-widest">Sincronizar API GA4</span>
            </button>
        </header>

        <div id="init-loader" class="flex flex-col items-center justify-center p-24 text-zinc-500 bg-zinc-900/40 rounded-3xl border border-zinc-800/80 mt-10">
            <i data-lucide="loader-2" class="w-12 h-12 animate-spin mb-4 text-indigo-500"></i>
            <p id="loader-text" class="text-sm font-bold uppercase tracking-widest text-indigo-400 animate-pulse">Iniciando Motor Paralelo...</p>
        </div>

        <div class="overflow-x-auto overflow-y-hidden custom-scrollbar flex-1 pb-6 w-full" style="display: none;" id="master-container">
            <div class="bg-zinc-950 rounded-2xl border-2 border-zinc-800/80 shadow-[0_10px_40px_rgba(0,0,0,0.5)] overflow-hidden inline-block h-max min-w-max">
                <table class="excel-table w-full whitespace-nowrap m-0" id="mega-table">
                    <thead>
                        <tr>
                            <th class="border-none bg-zinc-950 sticky left-0 z-20 border-r border-indigo-500/30"></th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📅 SEMANA YoY</th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">🔄 SEMANA WoW</th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📅 MES ACUM. (MTD)</th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📈 ANUAL ACUM. (YTD)</th>
                        </tr>
                        <tr>
                            <th class="header-sub" style="text-align: left; padding-left: 20px;">Producto Base</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-yoy-prev">Seman Ant</th><th class="header-sub w-24" id="head-yoy-curr">Sem Act</th><th class="header-sub w-20 bg-indigo-600">Var. %</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-wow-prev">Seman Ant</th><th class="header-sub w-24" id="head-wow-curr">Sem Act</th><th class="header-sub w-20 bg-indigo-600">Var. %</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-mtd-prev">MoM Año Ant</th><th class="header-sub w-24" id="head-mtd-curr">MoM Act</th><th class="header-sub w-20 bg-indigo-600">Var. %</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-ytd-prev">YoY Año Ant</th><th class="header-sub w-24" id="head-ytd-curr">YoY Act</th><th class="header-sub w-20 bg-indigo-600">Var. %</th>
                        </tr>
                    </thead>
                    <tbody class="bg-zinc-950/50" id="body-target">
                    </tbody>
                    <tfoot class="row-total">
                        <tr>
                            <td class="sticky left-0 bg-[#18181b] z-10 text-left px-5 tracking-widest text-[#a5b4fc] border-r border-indigo-500/50">RESUMEN GLOBAL</td>
                            <td colspan="3" class="cell-val text-center" id="total-w_yoy"><i data-lucide="loader" class="w-4 h-4 animate-spin mx-auto text-zinc-500"></i></td>
                            <td colspan="3" class="cell-val text-center" id="total-w_wow"><i data-lucide="loader" class="w-4 h-4 animate-spin mx-auto text-zinc-500"></i></td>
                            <td colspan="3" class="cell-val text-center" id="total-m_yoy"><i data-lucide="loader" class="w-4 h-4 animate-spin mx-auto text-zinc-500"></i></td>
                            <td colspan="3" class="cell-val text-center" id="total-y_yoy"><i data-lucide="loader" class="w-4 h-4 animate-spin mx-auto text-zinc-500"></i></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Nueva Tabla: Tendencia Mensual (12 Meses) -->
        <div class="mt-12 mb-20 w-full" style="display: none;" id="trend-container">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center border border-indigo-500/30">
                    <i data-lucide="calendar" class="w-4 h-4 text-indigo-400"></i>
                </div>
                <h2 class="text-xl font-bold text-white uppercase tracking-tight italic">Tendencia Mensual (Full Year)</h2>
            </div>
            
            <div class="bg-zinc-950 rounded-2xl border-2 border-zinc-800/80 shadow-[0_10px_40px_rgba(0,0,0,0.5)] overflow-hidden max-w-full">
                <table class="excel-table w-full whitespace-nowrap m-0">
                    <thead>
                        <tr>
                            <th class="header-sub text-left pl-6" rowspan="2">Mes</th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📈 TOTAL VISITAS</th>
                            <th colspan="2" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">💻 WEB (DESKTOP)</th>
                            <th colspan="2" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📱 MÓVIL / TAB</th>
                            <th colspan="2" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">⏳ RETENCIÓN (SEG)</th>
                        </tr>
                        <tr>
                            <th class="header-sub w-24 border-l border-white/10">2025</th>
                            <th class="header-sub w-24">2026</th>
                            <th class="header-sub w-20 bg-indigo-600">%</th>
                            <th class="header-sub w-24 border-l border-white/10">2025</th>
                            <th class="header-sub w-24">2026</th>
                            <th class="header-sub w-24 border-l border-white/10">2025</th>
                            <th class="header-sub w-24">2026</th>
                            <th class="header-sub w-24 border-l border-white/10">2025</th>
                            <th class="header-sub w-24">2026</th>


                        </tr>
                    </thead>
                    <tbody class="bg-zinc-950/50" id="trend-body">
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        const date = new Date();
        const year = date.getFullYear();
        const lastYear = year - 1;

        // --- Generador Dinámico de Cabeceras de Semana ---
        function getISOWeekInfo(d) {
            const date = new Date(d.valueOf());
            const dayn = (date.getDay() + 6) % 7;
            date.setDate(date.getDate() - dayn + 3);
            const firstThursday = date.valueOf();
            date.setMonth(0, 1);
            if (date.getDay() !== 4) date.setMonth(0, 1 + ((4 - date.getDay()) + 7) % 7);
            return 1 + Math.ceil((firstThursday - date) / 604800000);
        }

        function formatHeader(startOffset, endOffset, forcedYear = null) {
            const endD = new Date(); endD.setDate(endD.getDate() - endOffset);
            const startD = new Date(); startD.setDate(startD.getDate() - startOffset);
            
            const wNum = getISOWeekInfo(endD);
            const sDay = ("0" + startD.getDate()).slice(-2);
            const eDay = ("0" + endD.getDate()).slice(-2);
            const months = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
            const mo = months[endD.getMonth()];
            const finalY = forcedYear || endD.getFullYear();
            
            return `Sem ${wNum}<br><span class="text-[10px] text-zinc-400 font-medium">(${sDay}-${eDay} ${mo}) ${finalY}</span>`;
        }

        document.getElementById('head-yoy-curr').innerHTML = formatHeader(7, 0, year);
        document.getElementById('head-yoy-prev').innerHTML = formatHeader(7, 0, lastYear);
        document.getElementById('head-wow-curr').innerHTML = formatHeader(7, 0, year);
        document.getElementById('head-wow-prev').innerHTML = formatHeader(14, 7, year);

        const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        const currentMonthName = monthNames[date.getMonth()].toUpperCase();
        
        document.getElementById('head-mtd-prev').innerHTML = `MTD ${currentMonthName}<br><span class="text-[10px] text-zinc-400 font-medium">(Acum.) ${lastYear}</span>`;
        document.getElementById('head-mtd-curr').innerHTML = `MTD ${currentMonthName}<br><span class="text-[10px] text-zinc-400 font-medium">(Acum.) ${year}</span>`;
        document.getElementById('head-ytd-prev').innerHTML = `YTD ANUAL<br><span class="text-[10px] text-zinc-400 font-medium">(Acum.) ${lastYear}</span>`;
        document.getElementById('head-ytd-curr').innerHTML = `YTD ANUAL<br><span class="text-[10px] text-zinc-400 font-medium">(Acum.) ${year}</span>`;
        
        lucide.createIcons();
        let globalProducts = {};

        async function loadAnalytics(isRefresh = false) {
            try {
                if (isRefresh) {
                    const icon = document.getElementById('btn-icon');
                    if(icon) icon.classList.add('animate-spin');
                }

                const initRes = await fetch('api_ga_stats.php?report=init');
                const initJSON = await initRes.json();
                if (initJSON.status !== 'success') throw new Error(initJSON.message);

                globalProducts = initJSON.data;
                const pathKeys = Object.keys(globalProducts);

                let bodyHtml = '';
                let totalFixed = { w_yoy:0, m_yoy:0, y_yoy:0 };

                pathKeys.forEach(p => {
                    const id = btoa(p).replace(/=/g,'');
                    const prod = globalProducts[p];
                    const f = prod.fixed || { w_yoy:0, m_yoy:0, y_yoy:0 };
                    
                    totalFixed.w_yoy += (f.w_yoy || 0);
                    totalFixed.m_yoy += (f.m_yoy || 0);
                    totalFixed.y_yoy += (f.y_yoy || 0);

                    bodyHtml += `
                        <tr class="hover:bg-zinc-800/40 transition-colors group">
                            <td class="cell-prod sticky left-0 bg-zinc-950 group-hover:bg-zinc-900 z-10 shadow-[2px_0_15px_rgba(0,0,0,0.6)] border-r border-indigo-500/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500/50 group-hover:bg-indigo-400"></div>
                                    ${prod.name}
                                </div>
                            </td>
                            <!-- Semana YoY: Prev es Fijo 2025 -->
                            <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${(f.w_yoy || 0).toLocaleString()}</td>
                            <td class="cell-val font-bold text-white bg-zinc-800/20" id="curr-w_yoy-${id}"><i data-lucide="loader-2" class="w-3 h-3 animate-spin mx-auto opacity-20"></i></td>
                            <td class="cell-val border-r border-zinc-800/60 font-black" id="perc-w_yoy-${id}">-</td>

                            <!-- Semana WoW: Ambos son API 2026 -->
                            <td colspan="3" class="cell-val border-r border-zinc-800/60 text-zinc-600" id="w_wow-${id}">cargando...</td>

                            <!-- Mes MoM: Prev es Fijo 2025 -->
                            <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${(f.m_yoy || 0).toLocaleString()}</td>
                            <td class="cell-val font-bold text-white bg-zinc-800/20" id="curr-m_yoy-${id}"><i data-lucide="loader-2" class="w-3 h-3 animate-spin mx-auto opacity-20"></i></td>
                            <td class="cell-val border-r border-zinc-800/60 font-black" id="perc-m_yoy-${id}">-</td>

                            <!-- Anual YoY: Prev es Fijo 2025 -->
                            <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${(f.y_yoy || 0).toLocaleString()}</td>
                            <td class="cell-val font-bold text-white bg-zinc-800/20" id="curr-y_yoy-${id}"><i data-lucide="loader-2" class="w-3 h-3 animate-spin mx-auto opacity-20"></i></td>
                            <td class="cell-val border-r border-zinc-800/80 font-black" id="perc-y_yoy-${id}">-</td>
                        </tr>
                    `;
                });

                document.getElementById('body-target').innerHTML = bodyHtml;
                
                // Footer con datos fijos ya sumados
                document.getElementById('total-w_yoy').innerHTML = `
                    <div class="flex items-center justify-between px-2">
                        <span class="text-zinc-400">${totalFixed.w_yoy.toLocaleString()}</span>
                        <span class="text-white font-black" id="footer-curr-w_yoy">...</span>
                        <span class="tracking-widest bg-zinc-900/50 px-2 rounded" id="footer-perc-w_yoy">-</span>
                    </div>
                `;
                document.getElementById('total-m_yoy').innerHTML = `
                    <div class="flex items-center justify-between px-2">
                        <span class="text-zinc-400">${totalFixed.m_yoy.toLocaleString()}</span>
                        <span class="text-white font-black" id="footer-curr-m_yoy">...</span>
                        <span class="tracking-widest bg-zinc-900/50 px-2 rounded" id="footer-perc-m_yoy">-</span>
                    </div>
                `;
                document.getElementById('total-y_yoy').innerHTML = `
                    <div class="flex items-center justify-between px-2">
                        <span class="text-zinc-400">${totalFixed.y_yoy.toLocaleString()}</span>
                        <span class="text-white font-black" id="footer-curr-y_yoy">...</span>
                        <span class="tracking-widest bg-zinc-900/50 px-2 rounded" id="footer-perc-y_yoy">-</span>
                    </div>
                `;

                document.getElementById('init-loader').style.display = 'none';
                document.getElementById('master-container').style.display = 'block';
                lucide.createIcons();

                const queries = ['w_yoy', 'w_wow', 'm_yoy', 'y_yoy'];
                queries.forEach(q => fetchAndRender(q, isRefresh));
                fetchMonthlyTrend(isRefresh);

            } catch(e) {
                document.getElementById('init-loader').innerHTML = `<div class="text-rose-500 font-bold p-6 bg-rose-500/10 rounded-xl border border-rose-500/30 flex items-center gap-3"><i data-lucide="wifi-off"></i> ERROR INIT: ${e.message}</div>`;
                lucide.createIcons();
            }
        }

        async function fetchAndRender(reportType, isRefresh) {
            try {
                const url = `api_ga_stats.php?report=${reportType}${isRefresh ? '&refresh=true' : ''}`;
                const response = await fetch(url);
                const dataJSON = await response.json();

                if (dataJSON.status === 'success') {
                    const data = dataJSON.data;
                    let tCurr=0, tPrev=0;

                    for (const path in data) {
                        const d = data[path];
                        const id = btoa(path).replace(/=/g,'');
                        
                        if (reportType === 'w_wow') {
                            const td = document.getElementById(`w_wow-${id}`);
                            if (td) {
                                const pClass = d.raw_perc >= 0 ? 'perc-up' : 'perc-down';
                                td.outerHTML = `
                                    <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${d.prev.toLocaleString()}</td>
                                    <td class="cell-val font-bold text-white bg-zinc-800/20 shadow-[inset_1px_0_10px_rgba(0,0,0,0.1)]">${d.curr.toLocaleString()}</td>
                                    <td class="${pClass} border-r border-zinc-800/60 font-black">${d.perc}</td>
                                `;
                            }
                        } else {
                            const currTd = document.getElementById(`curr-${reportType}-${id}`);
                            const percTd = document.getElementById(`perc-${reportType}-${id}`);
                            if (currTd) currTd.innerText = d.curr.toLocaleString();
                            if (percTd) {
                                percTd.innerText = d.perc;
                                percTd.className = (d.raw_perc >= 0 ? 'perc-up' : 'perc-down') + ' border-r border-zinc-800/60 font-black';
                            }
                        }
                        tCurr += d.curr; tPrev += d.prev;
                    }

                    // Total Row (Var % Relativa Global)
                    let tPercNum = 0;
                    let tpStr = 'N/A';
                    if (tPrev > 0) {
                        tPercNum = Math.round(((tCurr - tPrev)/tPrev)*100*100)/100;
                        tpStr = (tPercNum > 0 ? '+' : '') + tPercNum + '%';
                    } else if (tCurr > 0) {
                        tPercNum = 999;
                        tpStr = '+∞';
                    }
                    const tpClass = tPercNum >= 0 ? 'perc-up' : 'perc-down';
                    
                    if (reportType === 'w_wow') {
                        document.getElementById(`total-w_wow`).outerHTML = `
                            <td class="cell-val text-zinc-400 border-l border-zinc-800/50">${tPrev.toLocaleString()}</td>
                            <td class="cell-val text-white font-black">${tCurr.toLocaleString()}</td>
                            <td class="${tpClass} border-r border-zinc-800/80 tracking-widest bg-zinc-900/50">${tpStr}</td>
                        `;
                    } else {
                        const fCurr = document.getElementById(`footer-curr-${reportType}`);
                        const fPerc = document.getElementById(`footer-perc-${reportType}`);
                        if (fCurr) fCurr.innerText = tCurr.toLocaleString();
                        if (fPerc) {
                            fPerc.innerText = tpStr;
                            fPerc.className = tpClass + ' border-r border-zinc-800/80 tracking-widest bg-zinc-900/50';
                        }
                    }

                } else {
                    console.error("Error en reporte:", reportType, dataJSON.message);
                }
            } catch (e) {
                console.error("Fallo de red en:", reportType, e);
            } finally {
                const icon = document.getElementById('btn-icon');
                if(icon && reportType === 'y_yoy') icon.classList.remove('animate-spin');
            }
        }

        async function fetchMonthlyTrend(isRefresh) {
            try {
                const res = await fetch(`api_ga_stats.php?report=monthly_trend${isRefresh ? '&refresh=true' : ''}`);
                const json = await res.json();
                if (json.status === 'success') {
                    let html = '';
                    json.data.forEach(m => {
                        const pClass = m.raw_perc >= 0 ? 'perc-up' : 'perc-down';
                        html += `
                            <tr class="hover:bg-zinc-800/20 transition-colors">
                                <td class="cell-prod pl-6 h-12">${m.month_name}</td>
                                <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${m.prev.toLocaleString()}</td>
                                <td class="cell-val font-bold text-white bg-zinc-800/10">${m.curr.toLocaleString()}</td>



                                <td class="${pClass} font-black text-center">${m.perc}</td>
                                <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${m.prev_web.toLocaleString()}</td>
                                <td class="cell-val text-zinc-300 font-bold">${m.curr_web.toLocaleString()}</td>
                                <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${m.prev_mob.toLocaleString()}</td>
                                <td class="cell-val text-zinc-300 font-bold">${m.curr_mob.toLocaleString()}</td>
                                <td class="cell-val text-zinc-500 border-l border-zinc-800/40">${Math.round(m.prev_ret)}s</td>
                                <td class="cell-val text-indigo-300 font-black">${Math.round(m.curr_ret)}s</td>
                            </tr>
                        `;
                    });
                    document.getElementById('trend-body').innerHTML = html;
                    document.getElementById('trend-container').style.display = 'block';
                    lucide.createIcons();
                }
            } catch (e) {
                console.error("Error Trend:", e);
            }
        }

        loadAnalytics();
    </script>
</body>
</html>
   
 