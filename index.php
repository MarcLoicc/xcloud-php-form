<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// Totales históricos para las cajas superiores
$paidTotal = $conn->query("SELECT COUNT(*) as count FROM leads WHERE source = 'pago'")->fetch_assoc()['count'];
$paidValue = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE source = 'pago'")->fetch_assoc()['total'] ?? 0;

$organicTotal = $conn->query("SELECT COUNT(*) as count FROM leads WHERE source = 'organico'")->fetch_assoc()['count'];
$organicValue = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE source = 'organico'")->fetch_assoc()['total'] ?? 0;

// DATOS GRÁFICAS (7 Días)
$dates = []; $paidData = []; $orgData = [];
for($i=6; $i>=0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('d/m', strtotime($d));
    $paidData[] = $conn->query("SELECT COUNT(*) as c FROM leads WHERE DATE(created_at)='$d' AND source='pago'")->fetch_assoc()['c'];
    $orgData[] = $conn->query("SELECT COUNT(*) as c FROM leads WHERE DATE(created_at)='$d' AND source='organico'")->fetch_assoc()['c'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Dashboard - CRM Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-8 min-h-screen">
        <header class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-200">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Dashboard <span class="text-indigo-600">Analítico</span></h1>
                <p class="text-slate-500 text-sm mt-1">Control de rendimiento y adquisición en tiempo real.</p>
            </div>
            <button onclick="toggleModal()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Registrar Lead
            </button>
        </header>

        <!-- SECCIÓN 1: PAGO (ADS) -->
        <section class="mb-12">
            <div class="flex items-center gap-3 mb-6 bg-amber-50 px-4 py-2 rounded-full w-fit border border-amber-100">
                <i data-lucide="zap" class="w-4 h-4 text-amber-600"></i>
                <h2 class="text-[10px] font-black text-amber-900 uppercase tracking-widest">Campaña de Pago</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white border border-slate-200 p-8 rounded-[2rem] shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Total Ads (Semanas)</div>
                        <div class="text-6xl font-black text-slate-900"><?php echo $paidTotal; ?></div>
                        <div class="text-2xl font-bold text-amber-600 mt-4"><?php echo number_format($paidValue, 0, ',', '.'); ?>€</div>
                    </div>
                </div>
                <div class="md:col-span-2 bg-white border border-slate-200 rounded-[2rem] p-8 h-80 shadow-sm">
                    <canvas id="paidChart"></canvas>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 2: ORGÁNICO -->
        <section class="mb-12">
            <div class="flex items-center gap-3 mb-6 bg-emerald-50 px-4 py-2 rounded-full w-fit border border-emerald-100">
                <i data-lucide="leaf" class="w-4 h-4 text-emerald-600"></i>
                <h2 class="text-[10px] font-black text-emerald-900 uppercase tracking-widest">Crecimiento Orgánico</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white border border-slate-200 p-8 rounded-[2rem] shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Total Org (Natural)</div>
                        <div class="text-6xl font-black text-slate-900"><?php echo $organicTotal; ?></div>
                        <div class="text-2xl font-bold text-emerald-600 mt-4"><?php echo number_format($organicValue, 0, ',', '.'); ?>€</div>
                    </div>
                </div>
                <div class="md:col-span-2 bg-white border border-slate-200 rounded-[2rem] p-8 h-80 shadow-sm">
                    <canvas id="organicChart"></canvas>
                </div>
            </div>
        </section>
    </main>

    <script>
        lucide.createIcons();
        const cfg = { type: 'line', options: { responsive: true, maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: { 
                x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10, weight: '600' } } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 10 }, stepSize: 1 } } 
            }
        }};

        // Gráfico Pago
        new Chart(document.getElementById('paidChart').getContext('2d'), { ...cfg, data: { labels: <?php echo json_encode($dates); ?>,
            datasets: [{ data: <?php echo json_encode($paidData); ?>, borderColor: '#f59e0b', borderWidth: 3, tension: 0.4, fill: true, backgroundColor: 'rgba(245, 158, 11, 0.05)', pointRadius: 4, pointBackgroundColor: '#fff', pointBorderWidth: 2 }]
        }});
        
        // Gráfico Orgánico
        new Chart(document.getElementById('organicChart').getContext('2d'), { ...cfg, data: { labels: <?php echo json_encode($dates); ?>,
            datasets: [{ data: <?php echo json_encode($orgData); ?>, borderColor: '#10b981', borderWidth: 3, tension: 0.4, fill: true, backgroundColor: 'rgba(16, 185, 129, 0.05)', pointRadius: 4, pointBackgroundColor: '#fff', pointBorderWidth: 2 }]
        }});
    </script>
</body>
</html>
<?php $conn->close(); ?>
