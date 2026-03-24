<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analíticas Maestras - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
        .filter-btn-active { background-color: #f4f4f5 !important; color: #09090b !important; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12" id="main-content">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Panel Avanzado de Analítica</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Reportes en tiempo real y filtrado dinámico de rendimiento.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex bg-zinc-900 border border-zinc-800 rounded-lg p-1 overflow-hidden">
                    <button onclick="updateRange('all', this)" class="range-btn filter-btn-active px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800">TODO</button>
                    <button onclick="updateRange('7', this)" class="range-btn px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800 text-zinc-400">7D</button>
                    <button onclick="updateRange('14', this)" class="range-btn px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800 text-zinc-400">14D</button>
                    <button onclick="updateRange('30', this)" class="range-btn px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800 text-zinc-400">30D</button>
                </div>
                
                <div class="flex items-center gap-2 bg-zinc-900 border border-zinc-800 rounded-lg px-3 py-1">
                    <input type="date" id="customStart" class="bg-transparent border-0 text-[12px] text-zinc-300 outline-none p-1">
                    <span class="text-zinc-600">→</span>
                    <input type="date" id="customEnd" class="bg-transparent border-0 text-[12px] text-zinc-300 outline-none p-1">
                    <button onclick="applyCustom()" class="p-1 text-indigo-400 hover:text-indigo-300 transition-colors">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </button>
                </div>

                <button onclick="toggleModal()" class="px-5 py-2.5 bg-zinc-100 rounded-md text-[14px] font-bold text-zinc-950 hover:bg-zinc-300 transition-all flex items-center gap-2 shadow-lg">
                    <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Lead
                </button>
            </div>
        </header>

        <!-- KPI Grid - Analytical Report -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            <!-- PAGO Section -->
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner border-l-4 border-l-indigo-500 overflow-hidden relative">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Tráfico de Pago (ADS)</span>
                    <i data-lucide="target" class="w-4 h-4 text-indigo-500"></i>
                </div>
                <div class="flex flex-col gap-6 relative z-10">
                    <div>
                        <span id="stat-pago-total" class="text-4xl font-black text-zinc-100 italic">0</span>
                        <p class="text-[11px] text-zinc-500 font-bold uppercase mt-1">Leads Totales</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-zinc-800">
                        <div>
                            <span id="stat-pago-won" class="text-xl font-bold text-emerald-500 block">0</span>
                            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">Cerrados</span>
                        </div>
                        <div>
                            <span id="stat-pago-lost" class="text-xl font-bold text-zinc-600 block">0</span>
                            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">Perdidos</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ORGANICO Section -->
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner border-l-4 border-l-cyan-500 overflow-hidden relative">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-cyan-500/10 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Tráfico Orgánico</span>
                    <i data-lucide="globe" class="w-4 h-4 text-cyan-500"></i>
                </div>
                <div class="flex flex-col gap-6 relative z-10">
                    <div>
                        <span id="stat-organico-total" class="text-4xl font-black text-zinc-100 italic">0</span>
                        <p class="text-[11px] text-zinc-500 font-bold uppercase mt-1">Leads Totales</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-zinc-800">
                        <div>
                            <span id="stat-organico-won" class="text-xl font-bold text-emerald-500 block">0</span>
                            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">Cerrados</span>
                        </div>
                        <div>
                            <span id="stat-organico-lost" class="text-xl font-bold text-zinc-600 block">0</span>
                            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">Perdidos</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Section -->
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner border-l-4 border-l-yellow-500 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">Facturación Proyectada</span>
                        <i data-lucide="line-chart" class="w-4 h-4 text-yellow-500"></i>
                    </div>
                    <div class="flex flex-col">
                        <span id="stat-revenue" class="text-4xl font-black text-zinc-100 font-mono">€0</span>
                        <p class="text-[11px] text-zinc-500 mt-2">Métrica basada en leads ganados y propuestas enviadas en el periodo.</p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-zinc-800">
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] text-zinc-500 font-bold">TOTAL LEADS:</span>
                        <span id="stat-total" class="text-[14px] font-mono text-zinc-300">0</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts Row: Detailed Comparison -->
        <section class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-10">
            <!-- Main Separated Trend Chart -->
            <div class="lg:col-span-3 bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-inner relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 blur-[100px] -z-0"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h3 class="text-[16px] font-bold text-zinc-100">Comparativa de Captación</h3>
                        <p class="text-[12px] text-zinc-500">Desglose diario entre tráfico de Pago vs Orgánico</p>
                    </div>
                </div>
                <div class="h-[400px] w-full relative z-10">
                    <canvas id="leadsTrendChart"></canvas>
                </div>
            </div>

            <!-- Side Widgets -->
            <div class="lg:col-span-1 flex flex-col gap-6">
                <!-- Source Percentages -->
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-inner flex flex-col items-center">
                    <h3 class="text-[14px] font-bold text-zinc-100 mb-6 uppercase tracking-wider text-center">Cuota de Origen</h3>
                    <div class="h-[180px] w-full mb-4">
                        <canvas id="sourceDonutChart"></canvas>
                    </div>
                    <div class="w-full space-y-3 mt-4">
                        <div class="flex items-center justify-between p-2 bg-indigo-500/10 border border-indigo-500/20 rounded-lg">
                            <span class="text-[12px] font-bold text-indigo-400">ADS DE PAGO (FB/META)</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-cyan-500/10 border border-cyan-500/20 rounded-lg">
                            <span class="text-[12px] font-bold text-cyan-400">TRÁFICO ORGÁNICO</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-inner flex-1 flex flex-col justify-center text-center">
                    <i data-lucide="info" class="w-6 h-6 text-zinc-700 mx-auto mb-2"></i>
                    <p class="text-[12px] text-zinc-500">Los datos se actualizan automáticamente al detectar nuevas inserciones en la base de datos.</p>
                </div>
            </div>
        </section>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        let trendChart, donutChart;
        let currentRange = '7';

        async function fetchStats(range = '7', start = null, end = null) {
            let url = `api_stats.php?range=${range}`;
            if (start && end) url += `&start=${start}&end=${end}`;
            
            const res = await fetch(url);
            const data = await res.json();
            
            // Actualizar Tarjetas Desglosadas
            document.getElementById('stat-total').innerText = data.metrics.totalLeads;
            document.getElementById('stat-revenue').innerText = '€' + data.metrics.revenue;
            
            // Pago
            document.getElementById('stat-pago-total').innerText = data.metrics.pago.total;
            document.getElementById('stat-pago-won').innerText = data.metrics.pago.won;
            document.getElementById('stat-pago-lost').innerText = data.metrics.pago.lost;
            
            // Orgánico
            document.getElementById('stat-organico-total').innerText = data.metrics.organico.total;
            document.getElementById('stat-organico-won').innerText = data.metrics.organico.won;
            document.getElementById('stat-organico-lost').innerText = data.metrics.organico.lost;

            // Actualizar Gráfica Tendencia
            if (trendChart) trendChart.destroy();
            trendChart = new Chart(document.getElementById('leadsTrendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.chart.labels,
                    datasets: [
                        {
                            label: 'ADS (Pago)',
                            data: data.chart.pago,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            borderWidth: 3
                        },
                        {
                            label: 'Orgánico',
                            data: data.chart.organico,
                            borderColor: '#06b6d4',
                            backgroundColor: 'rgba(6, 182, 212, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'top', 
                            labels: { color: '#a1a1aa', font: { weight: 'bold', size: 10 } } 
                        } 
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#71717a' } },
                        y: { grid: { color: 'rgba(39, 39, 42, 0.5)' }, ticks: { stepSize: 1, color: '#71717a' } }
                    }
                }
            });

            // Actualizar Donut
            if (donutChart) donutChart.destroy();
            donutChart = new Chart(document.getElementById('sourceDonutChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: data.donut.data,
                        backgroundColor: ['#6366f1', '#06b6d4'],
                        borderWidth: 0,
                        hoverOffset: 12
                    }]
                },
                options: {
                    cutout: '80%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }

        function updateRange(range, btn) {
            document.querySelectorAll('.range-btn').forEach(b => {
                b.classList.remove('filter-btn-active');
                b.classList.add('text-zinc-400');
            });
            btn.classList.add('filter-btn-active');
            btn.classList.remove('text-zinc-400');
            currentRange = range;
            fetchStats(range);
        }

        function applyCustom() {
            const start = document.getElementById('customStart').value;
            const end = document.getElementById('customEnd').value;
            if(!start || !end) return alert('Selecciona ambas fechas');
            
            document.querySelectorAll('.range-btn').forEach(b => {
                b.classList.remove('filter-btn-active');
                b.classList.add('text-zinc-400');
            });
            
            fetchStats(null, start, end);
        }

        // Carga inicial
        fetchStats('all');

        // Polling cada 30 segundos para datos "Live"
        setInterval(() => {
            const start = document.getElementById('customStart').value;
            const end = document.getElementById('customEnd').value;
            if(!start || !end) fetchStats(currentRange);
        }, 30000);
    </script>
</body>
</html>
<?php $conn->close(); ?>
