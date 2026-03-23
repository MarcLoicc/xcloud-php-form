<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// --- ESTADÍSTICAS REALES (Últimos 7 días) ---
$periodQuery = "created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)";

// Total Leads, Orgánicos, Pago y Valor (Últimos 7 días)
$total7 = $conn->query("SELECT COUNT(*) as count FROM leads WHERE $periodQuery")->fetch_assoc()['count'];
$organic7 = $conn->query("SELECT COUNT(*) as count FROM leads WHERE $periodQuery AND source = 'organico'")->fetch_assoc()['count'];
$paid7 = $conn->query("SELECT COUNT(*) as count FROM leads WHERE $periodQuery AND source = 'pago'")->fetch_assoc()['count'];
$revenue7 = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE $periodQuery")->fetch_assoc()['total'] ?? 0;

// Datos para la Gráfica (Últimos 7 días)
$dailyData = [];
for($i=6; $i>=0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $q = $conn->query("SELECT COUNT(*) as count FROM leads WHERE DATE(created_at) = '$date'")->fetch_assoc()['count'];
    $dailyData[] = ['date' => date('d/m', strtotime($date)), 'count' => $q];
}
$labels = json_encode(array_column($dailyData, 'date'));
$counts = json_encode(array_column($dailyData, 'count'));

// Leads recientes
$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Dashboard Analítico - CRM Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-8 min-h-screen bg-bg">
        <header class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tighter">Rendimiento <span class="text-primary italic font-medium ml-2 uppercase text-sm tracking-widest">Real</span></h1>
                <p class="text-zinc-500 text-sm mt-1">Análisis detallado de los últimos 7 días de actividad.</p>
            </div>
            <div class="px-4 py-2 bg-card border border-border rounded-xl text-[10px] font-black text-zinc-500 uppercase tracking-widest shadow-sm">
                Datos actualizados: <?php echo date('H:i'); ?>
            </div>
        </header>

        <!-- Métricas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-card border border-border p-6 rounded-3xl">
                <div class="text-[9px] font-black text-zinc-600 uppercase tracking-widest mb-3">Total Leads (7d)</div>
                <div class="text-4xl font-black text-white tabular-nums mb-1"><?php echo $total7; ?></div>
                <div class="w-full bg-zinc-900 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-primary h-full" style="width: 100%"></div>
                </div>
            </div>
            <div class="bg-card border border-border p-6 rounded-3xl">
                <div class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-3">Orgánico</div>
                <div class="text-4xl font-black text-white tabular-nums mb-1"><?php echo $organic7; ?></div>
                <div class="text-[9px] font-bold text-zinc-600 uppercase"><?php echo $total7 > 0 ? round(($organic7/$total7)*100) : 0; ?>% del total</div>
            </div>
            <div class="bg-card border border-border p-6 rounded-3xl">
                <div class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-3">Canal de Pago</div>
                <div class="text-4xl font-black text-white tabular-nums mb-1"><?php echo $paid7; ?></div>
                <div class="text-[9px] font-bold text-zinc-600 uppercase"><?php echo $total7 > 0 ? round(($paid7/$total7)*100) : 0; ?>% del total</div>
            </div>
            <div class="bg-card border border-border p-6 rounded-3xl">
                <div class="text-[9px] font-black text-primary uppercase tracking-widest mb-3">Valor Estimado</div>
                <div class="text-4xl font-black text-white tabular-nums mb-1"><?php echo number_format($revenue7, 0, ',', '.'); ?>€</div>
                <div class="text-[9px] font-bold text-zinc-600 uppercase">Potencial generado</div>
            </div>
        </div>

        <!-- Gráfica Semanal -->
        <div class="bg-card border border-border p-8 rounded-[2rem] shadow-sm mb-10">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xs font-black text-zinc-500 uppercase tracking-widest ml-1">Evolución de Leads (Últimos 7 días)</h2>
            </div>
            <div class="h-64">
                <canvas id="leadsChart"></canvas>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="bg-card border border-border rounded-[2rem] overflow-hidden shadow-sm">
            <div class="px-8 py-6 border-b border-border flex items-center justify-between bg-zinc-900/30 text-xs font-black text-zinc-500 uppercase tracking-widest">
                Últimas Entradas
                <a href="leads.php" class="text-primary hover:underline transition-all">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <tbody class="divide-y divide-border">
                        <?php while($row = $recentLeads->fetch_assoc()): ?>
                        <tr class="hover:bg-zinc-900/40 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="text-[10px] text-zinc-600 uppercase font-black tracking-tighter"><?php echo $row['company'] ?: 'Particular'; ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase border <?php echo $row['source'] == 'pago' ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'; ?>">
                                    <?php echo $row['source']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right font-mono text-[10px] text-zinc-600 uppercase font-bold">
                                <?php echo date('d M, H:i', strtotime($row['created_at'])); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        // Configuración de la Gráfica
        const ctx = document.getElementById('leadsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    label: 'Leads',
                    data: <?php echo $counts; ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#09090b',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#52525b', font: { size: 10, weight: 'bold' } } },
                    y: { beginAtZero: true, grid: { color: '#27272a', drawBorder: false }, ticks: { color: '#52525b', font: { size: 10 }, stepSize: 1 } }
                }
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
