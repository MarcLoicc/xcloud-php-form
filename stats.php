<?php
require_once 'auth.php';
require_once 'db.php';
$ga4_id_query = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'ga4_property_id'");
$property_id = ($ga4_id_query && $ga4_id_query->num_rows > 0) ? $ga4_id_query->fetch_assoc()['setting_value'] : null;
?>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard | Marc Loic CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #09090b; color: #f4f4f5; }
        .glass-card { background: rgba(24, 24, 27, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(63, 63, 70, 0.4); border-radius: 1.25rem; }
        .table-container { overflow-x: auto; scrollbar-width: thin; scrollbar-color: #3f3f46 transparent; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #a1a1aa; padding: 1rem 1.25rem; border-bottom: 1px solid rgba(63, 63, 70, 0.4); text-align: center; }
        td { padding: 1rem 1.25rem; border-bottom: 1px solid rgba(63, 63, 70, 0.2); font-size: 0.875rem; text-align: center; transition: all 0.2s; }
        tr:last-child td { border-bottom: none; }
        .sticky-col { position: sticky; left: 0; background: #121214; z-index: 20; border-right: 1px solid rgba(63, 63, 70, 0.4); text-align: left !important; min-width: 240px; }
        tr:hover .sticky-col { background: #18181b; }
        .perc-up { color: #10b981; font-weight: 500; font-size: 0.8rem; }
        .perc-down { color: #ef4444; font-weight: 500; font-size: 0.8rem; }
        .tt-up { background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 6px; padding: 2px 6px; font-weight: 600; }
        .tt-down { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 6px; padding: 2px 6px; font-weight: 600; }
        .loader-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .btn-refresh { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); transition: transform 0.2s; }
        .btn-refresh:hover { transform: translateY(-2px); opacity: 0.9; }
        tr:hover td { background: rgba(39, 39, 42, 0.4); }
        .section-header { border-left: 4px solid #6366f1; padding-left: 1rem; }
        .metric-label { font-size: 0.7rem; color: #71717a; text-transform: uppercase; margin-bottom: 2px; }
    </style>
</head>
<body class="p-6 lg:p-10">
    <div class="max-w-[1700px] mx-auto space-y-10">
        
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-2">
            <div>
                <h1 class="text-4xl font-bold flex items-center gap-3">
                    Hello Marc Loic <span class="text-3xl">👋</span>
                </h1>
                <p class="text-zinc-400 mt-2 font-light">Análisis estratégico de rendimiento y comparativa histórica de productos.</p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="syncAll()" class="btn-refresh flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white shadow-lg shadow-indigo-500/20">
                    <i data-lucide="refresh-cw" id="btn-icon" class="w-5 h-5"></i>
                    Sincronizar Datos
                </button>
            </div>
        </header>

        <!-- Stats Section -->
        <section class="space-y-6">
            <div class="flex items-center gap-3 section-header">
                <i data-lucide="layers" class="w-6 h-6 text-indigo-400"></i>
                <h2 class="text-xl font-semibold tracking-tight text-white/90">Cuadro Integral de Rendimiento</h2>
            </div>
            
            <div class="glass-card table-container">
                <table id="main-stats-table">
                    <thead>
                        <tr>
                            <th class="sticky-col">Producto / Servicio</th>
                            <th colspan="3" class="bg-indigo-500/5 text-indigo-300">📅 Semana YoY</th>
                            <th colspan="3" class="bg-purple-500/5 text-purple-300">📊 Semana WoW</th>
                            <th colspan="3" class="bg-cyan-500/5 text-cyan-300">🌙 Mes MTD</th>
                            <th colspan="3" class="bg-amber-500/5 text-amber-300">🏆 Año YTD</th>
                        </tr>
                        <tr class="text-[0.65rem] text-zinc-500 bg-white/5 uppercase tracking-tighter border-b border-zinc-800">
                            <th class="sticky-col"></th>
                            <th>2025</th><th>2026</th><th>%</th>
                            <th>Prev</th><th>Act</th><th>%</th>
                            <th>2025</th><th>2026</th><th>%</th>
                            <th>2025</th><th>2026</th><th>%</th>
                        </tr>
                    </thead>
                    <tbody id="stats-body" class="divide-y divide-zinc-800">
                        <!-- JS renders -->
                    </tbody>
                    <tfoot id="table-footer" class="bg-zinc-900/60 font-bold border-t border-indigo-500/30">
                        <!-- JS placeholders -->
                    </tfoot>
                </table>
            </div>
        </section>

        <!-- Trend Section -->
        <section class="space-y-6 pt-4">
            <div class="flex items-center gap-3 section-header border-cyan-400">
                <i data-lucide="line-chart" class="w-6 h-6 text-cyan-400"></i>
                <h2 class="text-xl font-semibold tracking-tight text-white/90 text-cyan-50">Análisis Estacional (Tendencia 2025 vs 2026)</h2>
            </div>
            
            <div class="glass-card table-container">
                <table id="trend-table">
                    <thead>
                        <tr>
                            <th class="sticky-col !min-w-[150px]">Mes Histórico</th>
                            <th colspan="3" class="bg-white/5 border-x border-zinc-700/50">📈 Total Visitas</th>
                            <th colspan="3" class="bg-white/5 border-x border-zinc-700/50 text-indigo-300">💻 Tráfico Web</th>
                            <th colspan="3" class="bg-white/5 border-x border-zinc-700/50 text-purple-300">📱 Tráfico Móvil</th>
                            <th colspan="3" class="bg-white/5 border-x border-zinc-700/50 text-amber-300">⏱️ Retención</th>
                        </tr>
                        <tr class="text-[0.65rem] text-zinc-500 bg-white/5 uppercase border-b border-zinc-800">
                            <th class="sticky-col !min-w-[150px]"></th>
                            <th>'25</th><th>'26</th><th>Var%</th>
                            <th>'25</th><th>'26</th><th>Var%</th>
                            <th>'25</th><th>'26</th><th>Var%</th>
                            <th>'25</th><th>'26</th><th>Var%</th>
                        </tr>
                    </thead>
                    <tbody id="trend-body" class="divide-y divide-zinc-800">
                        <!-- JS renders -->
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    <script>
        lucide.createIcons();
        const api = 'api_ga_stats.php';

        async function syncAll() {
            const btn = document.getElementById('btn-icon');
            btn.classList.add('animate-spin');
            
            await renderTrend(true);
            const initRes = await fetch(api + '?report=init&refresh=true');
            const initData = await initRes.json();
            if(initData.status === 'success') {
                renderTopTable(initData.data);
                const reports = ['w_yoy', 'w_wow', 'm_yoy', 'y_yoy'];
                await Promise.all(reports.map(r => fetchAndRender(r, true)));
            }
            btn.classList.remove('animate-spin');
        }

        async function init() {
            const res = await fetch(api + '?report=init');
            const json = await res.json();
            if(json.status === 'success') {
                renderTopTable(json.data);
                const reports = ['w_yoy', 'w_wow', 'm_yoy', 'y_yoy'];
                reports.forEach(r => fetchAndRender(r));
                renderTrend();
            }
        }

        function renderTopTable(data) {
            const body = document.getElementById('stats-body');
            body.innerHTML = '';
            for(const path in data) {
                const prod = data[path];
                const id = path.replace(/[^a-z0-9]/gi, '_');
                body.innerHTML += `
                    <tr class="group">
                        <td class="sticky-col font-medium text-white/80">${prod.name}</td>
                        <td class="text-zinc-500">${(prod.prev_w_yoy || 0).toLocaleString()}</td>
                        <td class="font-bold text-white bg-indigo-500/10" id="curr-w_yoy-${id}">-</td>
                        <td class="text-xs" id="perc-w_yoy-${id}">-</td>
                        <td id="w_wow-${id}-p" class="text-zinc-500">-</td>
                        <td id="w_wow-${id}-c" class="font-bold text-white bg-purple-500/10">-</td>
                        <td id="w_wow-${id}-v" class="text-xs">-</td>
                        <td class="text-zinc-500">${(prod.prev_m_yoy || 0).toLocaleString()}</td>
                        <td class="font-bold text-white bg-cyan-500/10" id="curr-m_yoy-${id}">-</td>
                        <td class="text-xs" id="perc-m_yoy-${id}">-</td>
                        <td class="text-zinc-500">${(prod.prev_y_yoy || 0).toLocaleString()}</td>
                        <td class="font-bold text-white bg-amber-500/10" id="curr-y_yoy-${id}">-</td>
                        <td class="text-xs" id="perc-y_yoy-${id}">-</td>
                    </tr>
                `;
            }
            document.getElementById('table-footer').innerHTML = `
                <tr>
                    <td class="sticky-col text-indigo-400 uppercase text-[0.7rem] pt-5">Resumen Global</td>
                    <td colspan="3" id="total-w_yoy" class="bg-indigo-500/5 pt-5">...</td>
                    <td colspan="3" id="total-w_wow" class="bg-purple-500/5 pt-5">...</td>
                    <td colspan="3" id="total-m_yoy" class="bg-cyan-500/5 pt-5">...</td>
                    <td colspan="3" id="total-y_yoy" class="bg-amber-500/5 pt-5">...</td>
                </tr>
            `;
        }

        async function fetchAndRender(rep, force = false) {
            const res = await fetch(api + '?report=' + rep + (force ? '&refresh=true' : ''));
            const json = await res.json();
            if(json.status === 'success') {
                let sC=0, sP=0;
                for(const path in json.data) {
                    const d = json.data[path];
                    const id = path.replace(/[^a-z0-9]/gi, '_');
                    sC += d.curr; sP += d.prev;
                    if(rep === 'w_wow') {
                        document.getElementById(`w_wow-${id}-p`).innerText = d.prev.toLocaleString();
                        document.getElementById(`w_wow-${id}-c`).innerText = d.curr.toLocaleString();
                        const pe = document.getElementById(`w_wow-${id}-v`);
                        pe.innerText = d.perc; pe.className = d.raw_perc >= 0 ? 'tt-up' : 'tt-down';
                    } else {
                        const cellC = document.getElementById(`curr-${rep}-${id}`);
                        const cellP = document.getElementById(`perc-${rep}-${id}`);
                        if(cellC) cellC.innerText = d.curr.toLocaleString();
                        if(cellP) { cellP.innerText = d.perc; cellP.className = d.raw_perc >= 0 ? 'tt-up' : 'tt-down'; }
                    }
                }
                const tp = (sP > 0) ? Math.round((sC - sP) / sP * 10000) / 100 : 0;
                const sign = tp > 0 ? '+' : '';
                document.getElementById(`total-${rep}`).outerHTML = `
                    <td class="text-zinc-500 font-light text-[0.65rem] pt-5">${sP.toLocaleString()}</td>
                    <td class="text-white pt-5">${sC.toLocaleString()}</td>
                    <td class="pt-5"><span class="${tp>=0?'tt-up':'tt-down'} text-[0.7rem]">${sign}${tp}%</span></td>
                `;
            }
        }

        async function renderTrend(force = false) {
            const res = await fetch(api + '?report=monthly_trend' + (force ? '&refresh=true' : ''));
            const json = await res.json();
            if(json.status === 'success') {
                const body = document.getElementById('trend-body');
                body.innerHTML = '';
                json.data.forEach(d => {
                    body.innerHTML += `
                        <tr class="group">
                            <td class="sticky-col font-medium text-white/70">${d.month_name}</td>
                            <td class="text-zinc-500">${d.prev.toLocaleString()}</td><td class="font-semibold">${d.curr.toLocaleString()}</td>
                            <td class="pt-2"><span class="${d.raw_perc>=0?'tt-up':'tt-down'} text-xs font-bold">${d.perc}</span></td>
                            <td class="text-zinc-500">${d.prev_web.toLocaleString()}</td><td class="font-semibold text-indigo-100">${d.curr_web.toLocaleString()}</td>
                            <td class="pt-2"><span class="${d.raw_perc_web>=0?'tt-up':'tt-down'} text-xs">${d.perc_web}</span></td>
                            <td class="text-zinc-500">${d.prev_mob.toLocaleString()}</td><td class="font-semibold text-purple-100">${d.curr_mob.toLocaleString()}</td>
                            <td class="pt-2"><span class="${d.raw_perc_mob>=0?'tt-up':'tt-down'} text-xs">${d.perc_mob}</span></td>
                            <td class="text-zinc-500">${d.prev_ret}s</td><td class="font-semibold text-amber-100">${d.curr_ret}s</td>
                            <td class="pt-2"><span class="${d.raw_perc_ret>=0?'tt-up':'tt-down'} text-xs">${d.perc_ret}</span></td>
                        </tr>
                    `;
                });
            }
        }
        init();
    </script>
</body>
</html>