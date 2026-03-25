<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas Avanzadas - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
        .stat-card-glow { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 0; pointer-events: none; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12 mb-20" id="main-content">
        <!-- Stats Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Estadísticas Estadísticas</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Análisis detallado de rendimiento histórico y ratio de conversión.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex bg-zinc-900 border border-zinc-800 rounded-lg p-1 overflow-hidden">
                    <button onclick="updateRange('7', this)" class="range-btn bg-zinc-100 text-zinc-950 px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors">7D</button>
                    <button onclick="updateRange('30', this)" class="range-btn px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800 text-zinc-400">30D</button>
                    <button onclick="updateRange('90', this)" class="range-btn px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800 text-zinc-400">90D</button>
                    <button onclick="updateRange('all', this)" class="range-btn px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800 text-zinc-400">TOTAL</button>
                </div>
            </div>
        </header>

        <!-- Main Chart Section: Performance Comparison -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8 shadow-inner relative overflow-hidden group">
                <div class="stat-card-glow bg-indigo-500/5 blur-[100px]"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-zinc-100 italic">Rendimiento Comercial (Won vs Lost)</h3>
                        <p class="text-xs text-zinc-500 font-medium">Histórico acumulado de cierres positivos vs negativos.</p>
                    </div>
                    <div class="flex-1 min-h-[300px] w-full mt-auto">
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8 shadow-inner relative overflow-hidden group">
                <div class="stat-card-glow bg-cyan-500/5 blur-[100px]"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-zinc-100 italic">Leads Cualificados vs No Cualificados</h3>
                        <p class="text-xs text-zinc-500 font-medium">Calidad de los registros por canal de entrada.</p>
                    </div>
                    <div class="flex-1 min-h-[300px] w-full mt-auto">
                        <canvas id="qualityChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detailed Metrics Grid -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Win Rate Card -->
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner border-l-4 border-l-emerald-500">
                <h4 class="text-[11px] font-bold text-emerald-500 uppercase tracking-widest mb-4">Win Rate Global</h4>
                <div class="flex items-baseline gap-2">
                    <span id="stat-win-rate" class="text-4xl font-black text-zinc-100 italic">0%</span>
                    <span class="text-xs text-zinc-600 font-bold uppercase">Cerrados</span>
                </div>
            </div>

            <!-- Total Revenue Project -->
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner border-l-4 border-l-indigo-500">
                <h4 class="text-[11px] font-bold text-indigo-500 uppercase tracking-widest mb-4">Valor Promedio Lead</h4>
                <div class="flex items-baseline gap-2">
                    <span id="stat-avg-value" class="text-4xl font-black text-zinc-100 italic">€0</span>
                    <span class="text-xs text-zinc-600 font-bold uppercase">Ticket Avg</span>
                </div>
            </div>

            <!-- Unqualified Rate Card -->
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner border-l-4 border-l-red-500/50">
                <h4 class="text-[11px] font-bold text-red-400 uppercase tracking-widest mb-4">Ratio Descalificación</h4>
                <div class="flex items-baseline gap-2">
                    <span id="stat-unqualified-rate" class="text-4xl font-black text-zinc-100 italic">0%</span>
                    <span class="text-xs text-zinc-600 font-bold uppercase">No Cualif.</span>
                </div>
            </div>

            <!-- Total Growth Section -->
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner border-l-4 border-l-zinc-500">
                <h4 class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-4">Total Analizados</h4>
                <div class="flex items-baseline gap-2">
                    <span id="stat-analyzed" class="text-4xl font-black text-zinc-100 italic">0</span>
                    <span class="text-xs text-zinc-600 font-bold uppercase truncate">Periodo</span>
                </div>
            </div>
        </section>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        let convChart, qualChart;

        async function initStats(range = '7') {
            const res = await fetch(`api_stats?range=${range}`);
            const data = await res.json();
            
            const p = data.metrics.pago;
            const o = data.metrics.organico;
            
            // Calculos Globales
            const totalWon = p.won + o.won;
            const totalLost = p.lost + o.lost;
            const totalUnqualified = p.unqualified + o.unqualified;
            const totalLeads = p.total + o.total;
            const totalRev = parseFloat(data.metrics.revenue.replace(/,/g, ''));
            
            const winRate = totalLeads > 0 ? ((totalWon / totalLeads) * 100).toFixed(1) : 0;
            const avgValue = totalWon > 0 ? (totalRev / totalWon).toFixed(0) : 0;
            const unqRate = totalLeads > 0 ? ((totalUnqualified / totalLeads) * 100).toFixed(1) : 0;
            
            // Actualizar Cards
            document.getElementById('stat-win-rate').innerText = winRate + '%';
            document.getElementById('stat-avg-value').innerText = '€' + avgValue.toLocaleString();
            document.getElementById('stat-unqualified-rate').innerText = unqRate + '%';
            document.getElementById('stat-analyzed').innerText = totalLeads;

            // Render Gráfica 1: Conversión (Won vs Lost)
            if(convChart) convChart.destroy();
            convChart = new Chart(document.getElementById('conversionChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Márketing Pago', 'Tráfico Orgánico'],
                    datasets: [
                        { label: 'Ganados', data: [p.won, o.won], backgroundColor: '#10b981', borderRadius: 6 },
                        { label: 'Perdidos', data: [p.lost, o.lost], backgroundColor: '#ef4444', borderRadius: 6 }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'top', labels: { color: '#71717a', font: { weight: 'bold', size: 10 } } } 
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: '#71717a' } },
                        y: { stacked: true, grid: { color: '#27272a' }, ticks: { color: '#a1a1aa' } }
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
                        backgroundColor: ['#6366f1', '#3f3f46'],
                        hoverOffset: 12, borderWidth: 0
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'bottom', labels: { color: '#71717a', usePointStyle: true, padding: 20 } } 
                    }
                }
            });
        }

        function updateRange(v, btn) {
            document.querySelectorAll('.range-btn').forEach(b => {
                b.className = 'range-btn px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors hover:bg-zinc-800 text-zinc-400';
            });
            btn.className = 'range-btn bg-zinc-100 text-zinc-950 px-3 py-1.5 text-[12px] font-bold rounded-md transition-colors';
            initStats(v);
        }

        // Carga Inicial
        initStats('7');
    </script>
</body>
</html>
