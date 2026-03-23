<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// --- ESTADÍSTICAS REALES TOTALES ---

// Totales de PAGO
$paidTotal = $conn->query("SELECT COUNT(*) as count FROM leads WHERE source = 'pago'")->fetch_assoc()['count'];
$paidValue = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE source = 'pago'")->fetch_assoc()['total'] ?? 0;
$paidLeads = $conn->query("SELECT * FROM leads WHERE source = 'pago' ORDER BY created_at DESC LIMIT 4");

// Totales de ORGÁNICO
$organicTotal = $conn->query("SELECT COUNT(*) as count FROM leads WHERE source = 'organico'")->fetch_assoc()['count'];
$organicValue = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE source = 'organico'")->fetch_assoc()['total'] ?? 0;
$organicLeads = $conn->query("SELECT * FROM leads WHERE source = 'organico' ORDER BY created_at DESC LIMIT 4");

// Datos para la Gráfica Diaria (Últimos 7 días)
$dailyData = [];
for($i=6; $i>=0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $q = $conn->query("SELECT COUNT(*) as count FROM leads WHERE DATE(created_at) = '$date'")->fetch_assoc()['count'];
    $dailyData[] = ['date' => date('d/m', strtotime($date)), 'count' => $q];
}
$labels = json_encode(array_column($dailyData, 'date'));
$counts = json_encode(array_column($dailyData, 'count'));
?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>SaaS Dashboard Pro</title>
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
                <h1 class="text-3xl font-extrabold text-white tracking-tighter">Panel de <span class="text-primary italic font-medium ml-2 uppercase text-sm tracking-widest">Rendimiento Real</span></h1>
                <p class="text-zinc-500 text-sm mt-1">Análisis detallado segmentado por fuente de tráfico.</p>
            </div>
            <div class="px-4 py-2 bg-card border border-border rounded-xl text-[10px] font-black text-zinc-500 uppercase tracking-widest shadow-sm">
                Año <?php echo date('Y'); ?> &middot; Monitor Activo
            </div>
        </header>

        <!-- SECCIÓN 1: PAGO / ADS (SUPERIOR) -->
        <section class="mb-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center border border-amber-500/20">
                    <i data-lucide="zap" class="w-5 h-5 text-amber-500"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white leading-none">Canal de Pago</h2>
                    <p class="text-[10px] text-amber-500/60 uppercase font-black tracking-widest mt-1">Tráfico Pagado (Ads/Meta/Google)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-card border border-border p-8 rounded-3xl md:col-span-1">
                    <div class="text-[9px] font-black text-zinc-600 uppercase tracking-widest mb-4">Volumen Pago</div>
                    <div class="text-6xl font-black text-white tabular-nums leading-none"><?php echo $paidTotal; ?></div>
                    <div class="mt-4 text-[10px] font-bold text-zinc-500 uppercase">Leads Registrados</div>
                    <div class="mt-8 pt-6 border-t border-border">
                        <div class="text-[9px] font-black text-zinc-600 uppercase mb-2">Valor de Cartera (Ads)</div>
                        <div class="text-2xl font-bold text-white"><?php echo number_format($paidValue, 0, ',', '.'); ?>€</div>
                    </div>
                </div>
                
                <div class="md:col-span-2 bg-zinc-900/40 border border-border rounded-3xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-zinc-950/50 text-[9px] font-black uppercase tracking-widest text-zinc-600 border-b border-border">
                                <th class="px-6 py-4">Lead / Empresa</th>
                                <th class="px-6 py-4 text-right">Propuesta (€)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php while($row = $paidLeads->fetch_assoc()): ?>
                            <tr class="group transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-white"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <span class="text-[10px] text-zinc-600 uppercase font-bold tracking-tighter"><?php echo $row['company'] ?: 'Particular'; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right font-bold text-amber-500"><?php echo number_format($row['proposal_price'] ?? 0, 0, ',', '.'); ?>€</td>
                            </tr>
                            <?php endwhile; if($paidTotal == 0) echo '<tr><td colspan="2" class="px-6 py-10 text-center text-xs text-zinc-700 italic">No hay leads de pago registrados</td></tr>'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 2: ORGÁNICO (INFERIOR) -->
        <section class="mb-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20">
                    <i data-lucide="leaf" class="w-5 h-5 text-emerald-500"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white leading-none">Canal Orgánico</h2>
                    <p class="text-[10px] text-emerald-500/60 uppercase font-black tracking-widest mt-1">Tráfico Natural (SEO/Referencia)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-card border border-border p-8 rounded-3xl md:col-span-1">
                    <div class="text-[9px] font-black text-zinc-600 uppercase tracking-widest mb-4">Volumen Orgánico</div>
                    <div class="text-6xl font-black text-white tabular-nums leading-none"><?php echo $organicTotal; ?></div>
                    <div class="mt-4 text-[10px] font-bold text-zinc-500 uppercase">Leads Registrados</div>
                    <div class="mt-8 pt-6 border-t border-border">
                        <div class="text-[9px] font-black text-zinc-600 uppercase mb-2">Valor de Cartera (Org)</div>
                        <div class="text-2xl font-bold text-white"><?php echo number_format($organicValue, 0, ',', '.'); ?>€</div>
                    </div>
                </div>
                
                <div class="md:col-span-2 bg-zinc-900/40 border border-border rounded-3xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-zinc-950/50 text-[9px] font-black uppercase tracking-widest text-zinc-600 border-b border-border">
                                <th class="px-6 py-4">Lead / Empresa</th>
                                <th class="px-6 py-4 text-right">Propuesta (€)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php while($row = $organicLeads->fetch_assoc()): ?>
                            <tr class="group transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-white"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <span class="text-[10px] text-zinc-600 uppercase font-bold tracking-tighter"><?php echo $row['company'] ?: 'Particular'; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right font-bold text-emerald-500"><?php echo number_format($row['proposal_price'] ?? 0, 0, ',', '.'); ?>€</td>
                            </tr>
                            <?php endwhile; if($organicTotal == 0) echo '<tr><td colspan="2" class="px-6 py-10 text-center text-xs text-zinc-700 italic">No hay leads orgánicos registrados</td></tr>'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 3: GRÁFICA SEMANAL -->
        <div class="bg-card border border-border p-10 rounded-[2.5rem] shadow-sm">
            <h2 class="text-xs font-black text-zinc-500 uppercase tracking-widest mb-8 text-center leading-none">Monitor Semanal de Entradas</h2>
            <div class="h-64">
                <canvas id="leadsChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        const ctx = document.getElementById('leadsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    label: 'Leads',
                    data: <?php echo $counts; ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 5,
                    tension: 0.5,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#09090b',
                    pointBorderWidth: 4,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#52525b', font: { size: 10, weight: 'bold' } } },
                    y: { beginAtZero: true, grid: { color: '#27272a' }, ticks: { color: '#52525b', font: { size: 10 }, stepSize: 1 } }
                }
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
