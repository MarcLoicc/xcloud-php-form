<?php
require_once 'auth.php';
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .tt-up  { display:inline-block; background:rgba(16,185,129,.12); color:#10b981; border-radius:5px; padding:1px 7px; font-weight:600; font-size:0.75rem; }
        .tt-down{ display:inline-block; background:rgba(239,68,68,.12);  color:#ef4444; border-radius:5px; padding:1px 7px; font-weight:600; font-size:0.75rem; }
        .tbl-sticky{ position:sticky; left:0; background:#09090b; z-index:10; border-right:1px solid #27272a; min-width:180px; text-align:left!important; }
        tbody tr:hover .tbl-sticky{ background:#111113; }
        tbody tr:hover td{ background:rgba(39,39,42,.3); }
        .tbl-container{ overflow-x:auto; scrollbar-width:thin; scrollbar-color:#3f3f46 transparent; }
        th{ white-space:nowrap; }
        td{ white-space:nowrap; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12 mb-20" id="main-content">

        <!-- Page Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Estadísticas</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Rendimiento de tráfico web por producto. Comparativa histórica 2025 vs 2026.</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openManageModal()" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-800 border border-zinc-700 rounded-md text-[14px] font-semibold text-zinc-200 hover:bg-zinc-700 transition-colors shadow-sm">
                    <i data-lucide="settings-2" class="w-4 h-4" aria-hidden="true"></i>
                    Gestionar
                </button>
                <button onclick="openAddModal()" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-800 border border-zinc-700 rounded-md text-[14px] font-semibold text-zinc-200 hover:bg-zinc-700 transition-colors shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
                    Añadir Producto
                </button>
                <button onclick="syncAll()" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-100 rounded-md text-[14px] font-bold text-zinc-950 hover:bg-zinc-300 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 shadow-sm">
                    <i data-lucide="refresh-cw" id="btn-icon" class="w-4 h-4" aria-hidden="true"></i>
                    Sincronizar
                </button>
            </div>
        </header>

        <!-- Top Products Table -->
        <section aria-labelledby="products-heading" class="mb-10">
            <h2 id="products-heading" class="text-[13px] font-semibold text-zinc-400 uppercase tracking-widest mb-4">Cuadro Integral de Productos</h2>
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                <div class="tbl-container">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-950/60 border-b border-zinc-800">
                                <th class="tbl-sticky px-5 py-4 text-[11px] font-semibold text-zinc-400 uppercase tracking-wider">Producto / Servicio</th>
                                <th colspan="3" id="hdr-wyoy" class="px-5 py-4 text-[11px] font-semibold text-indigo-400 uppercase tracking-wider text-center border-l border-zinc-800">📅 Semana YoY</th>
                                <th colspan="3" id="hdr-wwow" class="px-5 py-4 text-[11px] font-semibold text-purple-400 uppercase tracking-wider text-center border-l border-zinc-800">📊 Semana WoW</th>
                                <th colspan="3" id="hdr-mmtd" class="px-5 py-4 text-[11px] font-semibold text-cyan-400 uppercase tracking-wider text-center border-l border-zinc-800">🌙 Mes MTD</th>
                                <th colspan="3" class="px-5 py-4 text-[11px] font-semibold text-amber-400 uppercase tracking-wider text-center border-l border-zinc-800">🏆 Año YTD</th>
                            </tr>
                            <tr class="bg-zinc-900/80 border-b border-zinc-800 text-[10px] text-zinc-500 uppercase tracking-wider">
                                <th class="tbl-sticky px-5 py-2"></th>
                                <th id="sub-wyoy-p" class="px-4 py-2 font-medium text-center border-l border-zinc-800">2025</th>
                                <th id="sub-wyoy-c" class="px-4 py-2 font-medium text-center">2026</th>
                                <th class="px-4 py-2 font-medium text-center">%</th>
                                <th id="sub-wwow-p" class="px-4 py-2 font-medium text-center border-l border-zinc-800">Prev</th>
                                <th id="sub-wwow-c" class="px-4 py-2 font-medium text-center">Act</th>
                                <th class="px-4 py-2 font-medium text-center">%</th>
                                <th id="sub-mmtd-p" class="px-4 py-2 font-medium text-center border-l border-zinc-800">2025</th>
                                <th id="sub-mmtd-c" class="px-4 py-2 font-medium text-center">2026</th>
                                <th class="px-4 py-2 font-medium text-center">%</th>
                                <th class="px-4 py-2 font-medium text-center border-l border-zinc-800">2025</th>
                                <th class="px-4 py-2 font-medium text-center">2026</th>
                                <th class="px-4 py-2 font-medium text-center">%</th>
                            </tr>
                        </thead>
                        <tbody id="stats-body" class="divide-y divide-zinc-800/70">
                            <!-- JS renders -->
                        </tbody>
                        <tfoot id="table-footer" class="border-t-2 border-zinc-700 bg-zinc-950/60">
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <!-- Monthly Trend Table -->
        <section aria-labelledby="trend-heading">
            <h2 id="trend-heading" class="text-[13px] font-semibold text-zinc-400 uppercase tracking-widest mb-4">Tendencia Mensual (2025 vs 2026)</h2>
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                <div class="tbl-container">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-950/60 border-b border-zinc-800">
                                <th class="tbl-sticky px-5 py-4 text-[11px] font-semibold text-zinc-400 uppercase tracking-wider !min-w-[130px]">Mes</th>
                                <th colspan="3" class="px-5 py-4 text-[11px] font-semibold text-zinc-300 uppercase tracking-wider text-center border-l border-zinc-800">📈 Total Visitas</th>
                                <th colspan="3" class="px-5 py-4 text-[11px] font-semibold text-indigo-400 uppercase tracking-wider text-center border-l border-zinc-800">💻 Web</th>
                                <th colspan="3" class="px-5 py-4 text-[11px] font-semibold text-purple-400 uppercase tracking-wider text-center border-l border-zinc-800">📱 Móvil</th>
                                <th colspan="3" class="px-5 py-4 text-[11px] font-semibold text-amber-400 uppercase tracking-wider text-center border-l border-zinc-800">⏱️ Retención</th>
                            </tr>
                            <tr class="bg-zinc-900/80 border-b border-zinc-800 text-[10px] text-zinc-500 uppercase tracking-wider">
                                <th class="tbl-sticky px-5 py-2 !min-w-[130px]"></th>
                                <th class="px-4 py-2 text-center border-l border-zinc-800">'25</th>
                                <th class="px-4 py-2 text-center">'26</th>
                                <th class="px-4 py-2 text-center">Var%</th>
                                <th class="px-4 py-2 text-center border-l border-zinc-800">'25</th>
                                <th class="px-4 py-2 text-center">'26</th>
                                <th class="px-4 py-2 text-center">Var%</th>
                                <th class="px-4 py-2 text-center border-l border-zinc-800">'25</th>
                                <th class="px-4 py-2 text-center">'26</th>
                                <th class="px-4 py-2 text-center">Var%</th>
                                <th class="px-4 py-2 text-center border-l border-zinc-800">'25</th>
                                <th class="px-4 py-2 text-center">'26</th>
                                <th class="px-4 py-2 text-center">Var%</th>
                            </tr>
                        </thead>
                        <tbody id="trend-body" class="divide-y divide-zinc-800/70">
                            <!-- JS renders -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <script>
        lucide.createIcons();
        const api = 'api_ga_stats.php';

        function tdC(v, cls='text-zinc-400 text-[13px]') { return `<td class="px-4 py-3.5 text-center ${cls}">${v}</td>`; }
        function tdB(v) { return `<td class="px-4 py-3.5 text-center text-[13px] font-bold text-zinc-100">${v}</td>`; }
        function tdSticky(v) { return `<td class="tbl-sticky px-5 py-3.5 text-[13px] font-medium text-zinc-200">${v}</td>`; }
        function tdPerc(p, raw) { return `<td class="px-4 py-3.5 text-center"><span class="${raw>=0?'tt-up':'tt-down'}">${p}</span></td>`; }

        async function syncAll() {
            const btn = document.getElementById('btn-icon').closest('button');
            btn.disabled = true;
            // Swap icon to spinner manually (avoid Lucide re-render losing spin state)
            const iconEl = document.getElementById('btn-icon');
            iconEl.setAttribute('data-lucide', 'loader-2');
            lucide.createIcons();
            // After createIcons, grab the fresh SVG and add spin
            document.getElementById('btn-icon').classList.add('animate-spin');
            try {
                await renderTrend(true);
                const res = await fetch(api + '?report=init&refresh=true');
                const json = await res.json();
                if(json.status === 'success') {
                    renderTopTable(json.data);
                    await Promise.all(['w_yoy','w_wow','m_yoy','y_yoy'].map(r => fetchAndRender(r, true)));
                }
            } finally {
                // Restore icon: remove spin first, then swap back
                document.getElementById('btn-icon').classList.remove('animate-spin');
                document.getElementById('btn-icon').setAttribute('data-lucide', 'refresh-cw');
                lucide.createIcons();
                btn.disabled = false;
            }
        }

        async function init() {
            const res = await fetch(api + '?report=init');
            const json = await res.json();
            if(json.status === 'success') {
                renderTopTable(json.data);
                ['w_yoy','w_wow','m_yoy','y_yoy'].forEach(r => fetchAndRender(r));
                renderTrend();
            }
        }

        function renderTopTable(data) {
            const body = document.getElementById('stats-body');
            body.innerHTML = '';
            for(const path in data) {
                const prod = data[path];
                const id = path.replace(/[^a-z0-9]/gi, '_');
                body.innerHTML += `<tr class="hover:bg-zinc-800/30 transition-colors group">
                    ${tdSticky(prod.name)}
                    ${tdC(((prod.fixed||{}).w_yoy||0).toLocaleString(),'text-zinc-500 text-[13px] border-l border-zinc-800')}
                    <td class="px-4 py-3.5 text-center text-[13px] font-bold text-zinc-100" id="curr-w_yoy-${id}">-</td>
                    <td class="px-4 py-3.5 text-center" id="perc-w_yoy-${id}">-</td>
                    <td class="px-4 py-3.5 text-center text-[13px] text-zinc-500 border-l border-zinc-800" id="w_wow-${id}-p">-</td>
                    <td class="px-4 py-3.5 text-center text-[13px] font-bold text-zinc-100" id="w_wow-${id}-c">-</td>
                    <td class="px-4 py-3.5 text-center" id="w_wow-${id}-v">-</td>
                    ${tdC(((prod.fixed||{}).m_yoy||0).toLocaleString(),'text-zinc-500 text-[13px] border-l border-zinc-800')}
                    <td class="px-4 py-3.5 text-center text-[13px] font-bold text-zinc-100" id="curr-m_yoy-${id}">-</td>
                    <td class="px-4 py-3.5 text-center" id="perc-m_yoy-${id}">-</td>
                    ${tdC(((prod.fixed||{}).y_yoy||0).toLocaleString(),'text-zinc-500 text-[13px] border-l border-zinc-800')}
                    <td class="px-4 py-3.5 text-center text-[13px] font-bold text-zinc-100" id="curr-y_yoy-${id}">-</td>
                    <td class="px-4 py-3.5 text-center" id="perc-y_yoy-${id}">-</td>
                </tr>`;
            }
            document.getElementById('table-footer').innerHTML = `
                <tr>
                    <td class="tbl-sticky px-5 py-3.5 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Resumen Global</td>
                    <td colspan="3" id="total-w_yoy" class="px-4 py-3.5 text-center text-zinc-500 border-l border-zinc-800">...</td>
                    <td colspan="3" id="total-w_wow" class="px-4 py-3.5 text-center text-zinc-500 border-l border-zinc-800">...</td>
                    <td colspan="3" id="total-m_yoy" class="px-4 py-3.5 text-center text-zinc-500 border-l border-zinc-800">...</td>
                    <td colspan="3" id="total-y_yoy" class="px-4 py-3.5 text-center text-zinc-500 border-l border-zinc-800">...</td>
                </tr>`;
        }

        async function fetchAndRender(rep, force=false) {
            const res = await fetch(api + '?report=' + rep + (force ? '&refresh=true' : ''));
            const json = await res.json();
            if(json.status !== 'success') return;
            let sC=0, sP=0;
            for(const path in json.data) {
                const d = json.data[path];
                const id = path.replace(/[^a-z0-9]/gi, '_');
                sC += d.curr; sP += d.prev;
                if(rep === 'w_wow') {
                    const p = document.getElementById(`w_wow-${id}-p`); if(p) p.innerText = d.prev.toLocaleString();
                    const c = document.getElementById(`w_wow-${id}-c`); if(c) c.innerText = d.curr.toLocaleString();
                    const v = document.getElementById(`w_wow-${id}-v`);
                    if(v) { v.innerHTML = `<span class="${d.raw_perc>=0?'tt-up':'tt-down'}">${d.perc}</span>`; }
                } else {
                    const cellC = document.getElementById(`curr-${rep}-${id}`);
                    const cellP = document.getElementById(`perc-${rep}-${id}`);
                    if(cellC) cellC.innerText = d.curr.toLocaleString();
                    if(cellP) cellP.innerHTML = `<span class="${d.raw_perc>=0?'tt-up':'tt-down'}">${d.perc}</span>`;
                }
            }
            const tp = (sP>0) ? Math.round((sC-sP)/sP*10000)/100 : 0;
            const sign = tp>0 ? '+' : '';
            const footCell = document.getElementById(`total-${rep}`);
            if(footCell) footCell.outerHTML = `
                <td class="px-4 py-3.5 text-center text-[12px] text-zinc-500 border-l border-zinc-800">${sP.toLocaleString()}</td>
                <td class="px-4 py-3.5 text-center text-[13px] font-bold text-zinc-100">${sC.toLocaleString()}</td>
                <td class="px-4 py-3.5 text-center"><span class="${tp>=0?'tt-up':'tt-down'}">${sign}${tp}%</span></td>
            `;
        }

        async function renderTrend(force=false) {
            const res = await fetch(api + '?report=monthly_trend' + (force ? '&refresh=true' : ''));
            const json = await res.json();
            if(json.status !== 'success') return;
            const body = document.getElementById('trend-body');
            body.innerHTML = '';
            json.data.forEach(d => {
                body.innerHTML += `<tr class="hover:bg-zinc-800/30 transition-colors group">
                    ${tdSticky(d.month_name)}
                    ${tdC(d.prev.toLocaleString(),'text-zinc-500 text-[13px] border-l border-zinc-800')}
                    ${tdB(d.curr.toLocaleString())}
                    ${tdPerc(d.perc, d.raw_perc)}
                    ${tdC(d.prev_web.toLocaleString(),'text-zinc-500 text-[13px] border-l border-zinc-800')}
                    <td class="px-4 py-3.5 text-center text-[13px] font-semibold text-indigo-200">${d.curr_web.toLocaleString()}</td>
                    ${tdPerc(d.perc_web, d.raw_perc_web)}
                    ${tdC(d.prev_mob.toLocaleString(),'text-zinc-500 text-[13px] border-l border-zinc-800')}
                    <td class="px-4 py-3.5 text-center text-[13px] font-semibold text-purple-200">${d.curr_mob.toLocaleString()}</td>
                    ${tdPerc(d.perc_mob, d.raw_perc_mob)}
                    ${tdC(d.prev_ret+'s','text-zinc-500 text-[13px] border-l border-zinc-800')}
                    <td class="px-4 py-3.5 text-center text-[13px] font-semibold text-amber-200">${d.curr_ret}s</td>
                    ${tdPerc(d.perc_ret, d.raw_perc_ret)}
                </tr>`;
            });
        }

        // Set dynamic week/month labels in sub-header
        (function() {
            const now = new Date();
            const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
            const mon = months[now.getMonth()];
            const day = now.getDate();

            // ISO week helpers
            function getISOWeek(date) {
                const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
                d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
                const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
                return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            }
            function weekRange(date) {
                const d = new Date(date);
                const day = d.getDay() || 7;
                d.setDate(d.getDate() - day + 1); // Monday
                const start = new Date(d);
                d.setDate(d.getDate() + 6); // Sunday
                const end = new Date(d);
                const m = months[end.getMonth()];
                if (start.getMonth() === end.getMonth()) {
                    return `${start.getDate()}-${end.getDate()} ${m}`;
                }
                return `${start.getDate()} ${months[start.getMonth()]}-${end.getDate()} ${m}`;
            }

            const currWeek = getISOWeek(now);
            const prevWeekDate = new Date(now);
            prevWeekDate.setDate(now.getDate() - 7);

            const currRange = weekRange(now);
            const prevRange = weekRange(prevWeekDate);

            // Update main group headers: remove week number, keep clean
            const hdrWyoy = document.getElementById('hdr-wyoy');
            const hdrWwow = document.getElementById('hdr-wwow');
            const hdrMmtd = document.getElementById('hdr-mmtd');
            if (hdrWyoy) hdrWyoy.innerHTML = '\uD83D\uDCC5 Semana YoY';
            if (hdrWwow) hdrWwow.innerHTML = '\uD83D\uDCCA Semana WoW';
            if (hdrMmtd) hdrMmtd.innerHTML = '\uD83C\uDF19 Mes MTD';

            // Update sub-header cells with date ranges
            const sy25 = document.getElementById('sub-wyoy-p'); if(sy25) sy25.textContent = `Sem ${currWeek} '25`;
            const sy26 = document.getElementById('sub-wyoy-c'); if(sy26) sy26.textContent = `Sem ${currWeek} '26 (${currRange})`;
            const sw_p = document.getElementById('sub-wwow-p'); if(sw_p) sw_p.textContent = `${prevRange} '26`;
            const sw_c = document.getElementById('sub-wwow-c'); if(sw_c) sw_c.textContent = `${currRange} '26`;
            const sm25 = document.getElementById('sub-mmtd-p'); if(sm25) sm25.textContent = `1-${day} ${mon} '25`;
            const sm26 = document.getElementById('sub-mmtd-c'); if(sm26) sm26.textContent = `1-${day} ${mon} '26`;
        })();

        init();
    </script>
    <!-- Modal Añadir Producto -->
    <div id="add-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeAddModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl shadow-2xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-zinc-100">Añadir Producto al Panel</h2>
                    <button onclick="closeAddModal()" class="text-zinc-500 hover:text-zinc-200 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form id="add-form" onsubmit="submitProduct(event)" class="space-y-5">
                    <div>
                        <label class="block text-[12px] font-semibold text-zinc-400 uppercase tracking-wider mb-2">Nombre del Producto *</label>
                        <input id="f-name" type="text" placeholder="Ej: Peluquerías Madrid" required
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-[14px] text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-zinc-400 uppercase tracking-wider mb-2">URL (page_path en GA4) *</label>
                        <input id="f-path" type="text" placeholder="Ej: /diseno-web-para-peluquerias/" required
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-[14px] text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                        <p class="text-[11px] text-zinc-600 mt-1.5">Debe empezar y terminar con / — igual que aparece en Google Analytics</p>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-zinc-800/50 rounded-lg border border-zinc-700">
                        <input id="f-hist" type="checkbox" class="w-4 h-4 rounded accent-indigo-500">
                        <div>
                            <label for="f-hist" class="text-[13px] font-medium text-zinc-200 cursor-pointer">¿Tiene histórico en 2025?</label>
                            <p class="text-[11px] text-zinc-500 mt-0.5">Márcalo si la página ya existía en 2025 (la columna '25 mostrará 0 igualmente si no hay datos en BD).</p>
                        </div>
                    </div>

                    <div id="form-error" class="hidden text-[13px] text-red-400 bg-red-400/10 border border-red-400/30 rounded-lg px-4 py-3"></div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" id="form-btn" class="flex-1 py-3 bg-zinc-100 text-zinc-950 font-bold text-[14px] rounded-lg hover:bg-zinc-300 transition-colors">
                            Añadir al Panel
                        </button>
                        <button type="button" onclick="closeAddModal()" class="px-5 py-3 text-[14px] text-zinc-400 hover:text-zinc-200 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('add-modal').classList.remove('hidden');
            document.getElementById('f-name').focus();
            lucide.createIcons();
        }
        function closeAddModal() {
            document.getElementById('add-modal').classList.add('hidden');
            document.getElementById('add-form').reset();
            document.getElementById('form-error').classList.add('hidden');
        }
        async function submitProduct(e) {
            e.preventDefault();
            const btn = document.getElementById('form-btn');
            const errEl = document.getElementById('form-error');
            errEl.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Guardando...';

            const payload = {
                name: document.getElementById('f-name').value.trim(),
                page_path: document.getElementById('f-path').value.trim(),
                has_2025_history: document.getElementById('f-hist').checked ? 1 : 0
            };

            try {
                const res = await fetch('api_products.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (json.status === 'success') {
                    closeAddModal();
                    // Limpiar caché y recargar datos
                    await syncAll();
                } else {
                    errEl.textContent = json.message || 'Error desconocido.';
                    errEl.classList.remove('hidden');
                }
            } catch(err) {
                errEl.textContent = 'Error de red: ' + err.message;
                errEl.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Añadir al Panel';
            }
        }
    </script>
    <!-- Modal Gestionar Productos -->
    <div id="manage-modal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeManageModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl max-h-[85vh] flex flex-col">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-8 py-6 border-b border-zinc-800">
                    <div>
                        <h2 class="text-xl font-bold text-zinc-100">Gestionar Productos</h2>
                        <p class="text-[12px] text-zinc-500 mt-0.5">Los datos históricos se conservan aunque desactives o elimines un producto.</p>
                    </div>
                    <button onclick="closeManageModal()" class="text-zinc-500 hover:text-zinc-200 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="manage-list" class="overflow-y-auto flex-1 px-8 py-4 space-y-2">
                    <p class="text-zinc-500 text-[13px]">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Gestionar Modal ──
        async function openManageModal() {
            document.getElementById('manage-modal').classList.remove('hidden');
            lucide.createIcons();
            await loadProductList();
        }
        function closeManageModal() {
            document.getElementById('manage-modal').classList.add('hidden');
        }
        async function loadProductList() {
            const list = document.getElementById('manage-list');
            list.innerHTML = '<p class="text-zinc-500 text-[13px]">Cargando...</p>';
            const res = await fetch('api_products.php');
            const json = await res.json();
            if (json.status !== 'success' || !json.data.length) {
                list.innerHTML = '<p class="text-zinc-500 text-[13px]">No hay productos.</p>'; return;
            }
            list.innerHTML = json.data.map(p => `
                <div id="prod-row-${p.id}" class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg border ${
                    p.active == 1 ? 'bg-zinc-800/50 border-zinc-700' : 'bg-zinc-900 border-zinc-800 opacity-60'
                }">
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-zinc-200 truncate">${p.name}</p>
                        <p class="text-[11px] text-zinc-500 truncate">${p.page_path}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium ${
                            p.active == 1 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-zinc-700 text-zinc-500'
                        }">${p.active == 1 ? 'Activo' : 'Inactivo'}</span>
                        <button onclick="toggleProduct(${p.id}, ${p.active == 1 ? 0 : 1})"
                            title="${p.active == 1 ? 'Desactivar' : 'Activar'}"
                            class="p-1.5 rounded-md text-zinc-400 hover:text-yellow-400 hover:bg-yellow-400/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${
                                p.active == 1
                                ? '<path d="M18.36 6.64A9 9 0 0 1 20.77 15"/><path d="M6.16 6.16a9 9 0 1 0 12.68 12.68"/><path d="M12 2v4"/><path d="m2 2 20 20"/>'
                                : '<path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.77.04"/>'
                            }</svg>
                        </button>
                        <button onclick="deleteProduct(${p.id}, '${p.name.replace(/'/g, "\\'")}')"
                            title="Eliminar de ga4_products (datos históricos se conservan)"
                            class="p-1.5 rounded-md text-zinc-400 hover:text-red-400 hover:bg-red-400/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </div>
                </div>
            `).join('');
        }
        async function toggleProduct(id, newActive) {
            await fetch('api_products.php', {
                method: 'PATCH',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id, active: newActive})
            });
            await loadProductList();
            // Recarga el panel sin sincronizar con GA4
            await init();
        }
        async function deleteProduct(id, name) {
            if (!confirm(`¿Eliminar "${name}" del panel?\n\nLos datos históricos en la base de datos se conservarán.`)) return;
            await fetch('api_products.php', {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            });
            await loadProductList();
            await syncAll();
        }
    </script>
</body>
</html>