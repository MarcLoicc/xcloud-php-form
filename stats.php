<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analítico GA4 - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
        .table-header { @apply bg-zinc-900 border-b border-zinc-800 text-[11px] font-bold text-zinc-500 uppercase tracking-widest text-center py-4 px-2; }
        .table-cell { @apply py-4 px-4 text-[13px] border-b border-zinc-900/50 text-zinc-300 font-medium; }
        .trend-up { @apply bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-500/20; }
        .trend-down { @apply bg-rose-500/10 text-rose-400 px-2 py-0.5 rounded text-[10px] font-bold border border-rose-500/20; }
        .bg-glass { background: rgba(9, 9, 11, 0.7); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12 mb-20" id="main-content">
        <!-- Analytics Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-10 border-b border-zinc-900 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center border border-indigo-500/20">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight italic">ANALYTICS COMMAND</h1>
                </div>
                <p class="text-[14px] text-zinc-500 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Data: Google Analytics 4 + CRM Lead Generator
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-1.5 flex gap-1">
                    <button class="px-4 py-2 bg-zinc-100 text-zinc-950 text-[12px] font-black rounded-md shadow-lg transition-transform active:scale-95">SEMANA</button>
                    <button class="px-4 py-2 text-zinc-400 hover:text-white text-[12px] font-black rounded-md transition-colors hover:bg-zinc-800">MES</button>
                    <button class="px-4 py-2 text-zinc-400 hover:text-white text-[12px] font-black rounded-md transition-colors hover:bg-zinc-800">ANUAL</button>
                </div>
                <button onclick="window.location.reload()" class="bg-zinc-100 hover:bg-zinc-300 text-zinc-950 p-2.5 rounded-lg transition-all shadow-xl hover:rotate-180 duration-500">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </button>
            </div>
        </header>

        <!-- Product Performance Table -->
        <section class="mb-12">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-[12px] font-black text-indigo-400 uppercase tracking-[0.2em] italic flex items-center gap-2">
                    <span class="w-8 h-[2px] bg-indigo-500/30"></span> 
                    Rendimiento por Línea de Negocio
                </h3>
                <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest bg-zinc-900 px-3 py-1 rounded-full border border-zinc-800">Cifras en Tiempo Real</span>
            </div>

            <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl overflow-hidden shadow-2xl relative">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="table-header text-left pl-8 sticky left-0 bg-zinc-900 z-10 w-[200px]">Producto</th>
                                <th class="table-header">Tarificación (P1)</th>
                                <th class="table-header">Ratio Tarificación (P2)</th>
                                <th class="table-header">Ratio Cualific. (P3)</th>
                                <th class="table-header">Inicio Contratación</th>
                                <th class="table-header">Contrataciones (P5)</th>
                                <th class="table-header">Éxito Global (P5/P1)</th>
                            </tr>
                        </thead>
                        <tbody id="ga-table-body">
                            <!-- Data will be injected here -->
                            <tr><td colspan="7" class="py-20 text-center text-zinc-500 font-medium">Sincronizando con GA4 Cloud...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Charts Grid Integration -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
             <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-8 relative group overflow-hidden">
                <div class="bg-indigo-500/5 absolute inset-0 blur-3xl rounded-full translate-x-1/2 -translate-y-1/2"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-bold font-serif italic text-zinc-100">Funnel de Conversión</h3>
                            <p class="text-xs text-zinc-500 font-medium">Comparativa de cierres globales</p>
                        </div>
                        <i data-lucide="trending-up" class="w-5 h-5 text-emerald-500 opacity-50"></i>
                    </div>
                    <div class="h-[300px]">
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-8 relative group overflow-hidden">
                <div class="bg-cyan-500/5 absolute inset-0 blur-3xl rounded-full -translate-x-1/2 translate-y-1/2"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-bold font-serif italic text-zinc-100">Calidad de Leads</h3>
                            <p class="text-xs text-zinc-500 font-medium">Distribución cualitativa por canal</p>
                        </div>
                        <i data-lucide="pie-chart" class="w-5 h-5 text-indigo-400 opacity-50"></i>
                    </div>
                    <div class="h-[300px]">
                        <canvas id="qualityChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        let convChart, qualChart;

        async function loadAnalytics() {
            try {
                const response = await fetch('api_ga_stats.php');
                const result = await response.json();
                
                if (result.status === 'success') {
                    renderTable(result.data);
                } else {
                    console.error('GA Data status:', result.status, result.message);
                }
            } catch (error) {
                console.error('Error loading GA data (Network/Path error):', error);
                alert('No se pudo conectar con el servidor de estadísticas (api_ga_stats.php)');
            }
        }

        function renderTable(data) {
            const container = document.getElementById('ga-table-body');
            if (data.length === 0) {
                container.innerHTML = '<tr><td colspan="7" class="py-20 text-center text-zinc-500 font-medium italic">No se encontraron visitas registradas para estas URLs en GA4.</td></tr>';
                return;
            }
            container.innerHTML = '';
            
            data.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-zinc-900/80 transition-colors group cursor-default';
                
                const trendIcon = item.tarificacion.change >= 0 ? 'trending-up' : 'trending-down';
                const trendClass = item.tarificacion.change >= 0 ? 'trend-up' : 'trend-down';
                
                // Helper to color change percentages
                const colorValue = (val) => val >= 0 ? 'text-emerald-400' : 'text-rose-400';
                
                tr.innerHTML = `
                    <td class="table-cell font-bold text-zinc-100 pl-8 sticky left-0 bg-zinc-950 group-hover:bg-zinc-900 z-10 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></span>
                            \${item.product}
                        </div>
                    </td>
                    <td class="table-cell text-center font-mono">
                        <div class="flex flex-col items-center">
                            <span class="text-[15px] font-black italic tracking-tight">\${item.tarificacion.current.toLocaleString()}</span>
                            <span class="\${trendClass}">
                                \${item.tarificacion.change >= 0 ? '+' : ''}\${item.tarificacion.change}%
                            </span>
                        </div>
                    </td>
                    <td class="table-cell text-center group-hover:scale-110 transition-transform">
                        <div class="flex flex-col items-center">
                            <span class="text-[14px] font-black text-white py-1 px-3 bg-zinc-800/80 rounded border border-zinc-700/50">\${item.ratio_tarificacion.current}%</span>
                            <span class="text-[10px] text-zinc-500 mt-1">Prev: \${item.ratio_tarificacion.prev}%</span>
                        </div>
                    </td>
                    <td class="table-cell text-center">
                        <div class="inline-flex items-center px-3 py-1 bg-indigo-500/10 text-indigo-300 rounded-full border border-indigo-500/20 text-[12px] font-bold">
                            \${item.ratio_cualificado.current}%
                        </div>
                    </td>
                    <td class="table-cell text-center font-mono">
                        <div class="flex flex-col items-center">
                             <span class="text-[14px] font-bold">\${item.inicio_contratacion.current}</span>
                             <span class="text-[10px] \${colorValue(item.inicio_contratacion.change)} font-bold">\${item.inicio_contratacion.change}%</span>
                        </div>
                    </td>
                    <td class="table-cell text-center">
                        <div class="flex flex-col items-center">
                              <span class="text-[14px] font-black text-emerald-400 bg-emerald-500/5 px-4 py-1.5 rounded-lg border border-emerald-500/10 shadow-[inset_0_0_12px_rgba(16,185,129,0.05)]">\${item.contrataciones.current}</span>
                              <span class="text-[10px] \${colorValue(item.contrataciones.change)} font-bold mt-1">\${item.contrataciones.change}%</span>
                        </div>
                    </td>
                    <td class="table-cell text-center">
                        <div class="relative h-10 w-24 mx-auto flex items-center justify-center">
                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#27272a" stroke-width="8" />
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#6366f1" stroke-width="8" stroke-dasharray="282.7" stroke-dashoffset="\${282.7 - (282.7 * item.ratio_exito_global / 100)}" />
                            </svg>
                            <span class="text-[11px] font-black italic">\${item.ratio_exito_global}%</span>
                        </div>
                    </td>
                `;
                container.appendChild(tr);
            });
            lucide.createIcons();
        }

        async function initCharts(range = '7') {
            const res = await fetch(`api_stats.php?range=${range}`);
            const data = await res.json();
            
            const p = data.metrics.pago;
            const o = data.metrics.organico;
            const totalLeads = p.total + o.total;
            const totalUnqualified = p.unqualified + o.unqualified;

            // Render Gráfica 1: Conversión (Won vs Lost)
            if(convChart) convChart.destroy();
            convChart = new Chart(document.getElementById('conversionChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Márketing Pago', 'Tráfico Orgánico'],
                    datasets: [
                        { label: 'Ganados', data: [p.won, o.won], backgroundColor: '#10b981', borderRadius: 8, barThickness: 40 },
                        { label: 'Perdidos', data: [p.lost, o.lost], backgroundColor: '#f43f5e', borderRadius: 8, barThickness: 40 }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'bottom', labels: { color: '#71717a', font: { weight: 'bold', size: 11 }, padding: 25, usePointStyle: true } } 
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: '#71717a' } },
                        y: { stacked: true, grid: { color: '#27272a' }, border: { dash: [4, 4] }, ticks: { color: '#a1a1aa', font: { weight: 'bold' } } }
                    }
                }
            });

            // Render Gráfica 2: Calidad (Cualificados vs No Cualificados)
            if(qualChart) qualChart.destroy();
            qualChart = new Chart(document.getElementById('qualityChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Cualificados', 'No Cualificados'],
                    datasets: [{
                        data: [(totalLeads - totalUnqualified), totalUnqualified],
                        backgroundColor: ['#6366f1', '#18181b'],
                        borderColor: '#27272a',
                        borderWidth: 2,
                        hoverOffset: 12
                    }]
                },
                options: {
                    cutout: '82%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'bottom', labels: { color: '#71717a', usePointStyle: true, padding: 30, font: { weight: 'bold' } } } 
                    }
                }
            });
        }

        // Carga Inicial
        loadAnalytics();
        initCharts('7');
    </script>
</body>
</html>
