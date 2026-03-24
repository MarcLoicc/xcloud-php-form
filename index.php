<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// Métricas de Cabecera
$totalLeads = $conn->query("SELECT COUNT(*) as total FROM leads")->fetch_assoc()['total'];
$revenue = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE status IN ('ganado', 'propuesta_enviada')")->fetch_assoc()['total'] ?? 0;
$statusCounts = $conn->query("SELECT status, COUNT(*) as count FROM leads GROUP BY status");
$sources = $conn->query("SELECT source, COUNT(*) as count FROM leads GROUP BY source");

// Datos para Gráfica de Tendencia (Últimos 7 días)
$historyData = $conn->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM leads 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
    GROUP BY DATE(created_at) 
    ORDER BY date ASC
");
$dates = []; $counts = [];
while($h = $historyData->fetch_assoc()) {
    $dates[] = date('d M', strtotime($h['date']));
    $counts[] = (int)$h['count'];
}

// Datos para Gráfica de Origen (Donut)
$sourceData = [];
$sourcesResult = $conn->query("SELECT source, COUNT(*) as count FROM leads GROUP BY source");
while($s = $sourcesResult->fetch_assoc()) { $sourceData[$s['source']] = $s['count']; }

$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analíticas - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12" id="main-content">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-10">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Panel Avanzado de Analítica</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Control total sobre el rendimiento de captación y ventas.</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="toggleModal()" class="px-5 py-2.5 bg-zinc-100 rounded-md text-[14px] font-bold text-zinc-950 hover:bg-zinc-300 transition-all flex items-center gap-2 shadow-lg hover:scale-105 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                    <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Lead
                </button>
            </div>
        </header>

        <!-- KPI Grid -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner group hover:border-zinc-700 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-zinc-500 uppercase tracking-widest">Leads Totales</span>
                    <i data-lucide="users" class="w-4 h-4 text-indigo-500"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-100"><?php echo number_format($totalLeads); ?></span>
                    <span class="text-[12px] text-emerald-500 font-bold">+12%</span>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner group hover:border-zinc-700 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-zinc-500 uppercase tracking-widest">Ingresos Proyectados</span>
                    <i data-lucide="bar-chart-3" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-100">€<?php echo number_format($revenue, 0, '.', ','); ?></span>
                    <span class="text-[12px] text-emerald-500 font-bold">Live</span>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner group hover:border-zinc-700 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-zinc-500 uppercase tracking-widest">Conversión Ads</span>
                    <i data-lucide="target" class="w-4 h-4 text-red-500"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-100"><?php echo $sourceData['pago'] ?? 0; ?></span>
                    <span class="text-[12px] text-zinc-500 font-medium">Leads</span>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl shadow-inner group hover:border-zinc-700 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-zinc-500 uppercase tracking-widest">Traf. Orgánico</span>
                    <i data-lucide="globe" class="w-4 h-4 text-cyan-500"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-100"><?php echo $sourceData['organico'] ?? 0; ?></span>
                    <span class="text-[12px] text-zinc-500 font-medium">Leads</span>
                </div>
            </div>
        </section>

        <!-- Charts Row -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Main Chart Area -->
            <div class="lg:col-span-2 bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-inner">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[16px] font-bold text-zinc-100">Tendencia Semanal de Captación</h3>
                    <select class="bg-zinc-950 border border-zinc-800 text-[12px] text-zinc-400 rounded-md px-2 py-1 outline-none">
                        <option>Últimos 7 días</option>
                    </select>
                </div>
                <div class="h-[300px] w-full">
                    <canvas id="leadsTrendChart"></canvas>
                </div>
            </div>

            <!-- Distribution Chart -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 shadow-inner">
                <h3 class="text-[16px] font-bold text-zinc-100 mb-8">Distribución por Origen</h3>
                <div class="relative h-[240px] w-full flex items-center justify-center">
                    <canvas id="sourceDonutChart"></canvas>
                </div>
                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between text-[13px]">
                        <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-indigo-500"></div> Ads de Pago</span>
                        <span class="font-bold text-zinc-100"><?php echo $sourceData['pago'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between text-[13px]">
                        <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-cyan-500"></div> Orgánico</span>
                        <span class="font-bold text-zinc-100"><?php echo $sourceData['organico'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Activity & Recent Table -->
        <section class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-inner">
            <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                <h2 class="text-[16px] font-bold text-zinc-100">Últimos Movimientos</h2>
                <a href="leads.php" class="text-[14px] font-medium text-indigo-400 hover:text-indigo-300">Explorar CRM completo</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[14px]">
                    <thead class="bg-zinc-950/50 text-zinc-500 font-semibold border-b border-zinc-800">
                        <tr>
                            <td class="px-6 py-4 uppercase text-[11px] tracking-widest">Cliente / Empresa</td>
                            <td class="px-6 py-4 uppercase text-[11px] tracking-widest">Valor</td>
                            <td class="px-6 py-4 uppercase text-[11px] tracking-widest">Estado</td>
                            <td class="px-6 py-4 uppercase text-[11px] tracking-widest text-right">Fecha</td>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        <?php while($row = $recentLeads->fetch_assoc()): ?>
                        <tr class="hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-zinc-100 block"><?php echo htmlspecialchars($row['name']); ?></span>
                                <span class="text-[12px] text-zinc-500"><?php echo htmlspecialchars($row['company'] ?: 'Particular'); ?></span>
                            </td>
                            <td class="px-6 py-4 font-mono text-emerald-400">€<?php echo number_format($row['proposal_price'] ?? 0, 0); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-800 border border-zinc-700 text-[11px] text-zinc-300 font-bold uppercase tracking-tighter">
                                    <?php echo str_replace('_', ' ', $row['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-zinc-500 text-[13px]"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        // Configuración de Gráficas
        const ctxTrend = document.getElementById('leadsTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Leads Captados',
                    data: <?php echo json_encode($counts); ?>,
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(99, 102, 241, 0.05)',
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#71717a' } },
                    y: { grid: { color: 'rgba(39, 39, 42, 0.5)' }, ticks: { stepSize: 1, color: '#71717a' } }
                }
            }
        });

        const ctxDonut = document.getElementById('sourceDonutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [<?php echo $sourceData['pago'] ?? 0; ?>, <?php echo $sourceData['organico'] ?? 0; ?>],
                    backgroundColor: ['#6366f1', '#06b6d4'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                cutout: '80%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
