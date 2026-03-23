<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// Totales históticos para las cajas superiores
$paidTotal = $conn->query("SELECT COUNT(*) as count FROM leads WHERE source = 'pago'")->fetch_assoc()['count'];
$paidValue = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE source = 'pago'")->fetch_assoc()['total'] ?? 0;
$paidLeads = $conn->query("SELECT * FROM leads WHERE source = 'pago' ORDER BY created_at DESC LIMIT 3");

$organicTotal = $conn->query("SELECT COUNT(*) as count FROM leads WHERE source = 'organico'")->fetch_assoc()['count'];
$organicValue = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE source = 'organico'")->fetch_assoc()['total'] ?? 0;
$organicLeads = $conn->query("SELECT * FROM leads WHERE source = 'organico' ORDER BY created_at DESC LIMIT 3");

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
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Pro Dashboard Analítico</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-8 min-h-screen bg-bg">
        <header class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tighter italic">Monitor <span class="text-primary not-italic font-medium">Empresarial</span></h1>
                <p class="text-zinc-600 text-sm mt-1">Control de rendimiento y adquisición en tiempo real.</p>
            </div>
            <button onclick="toggleModal()" class="px-6 py-2.5 bg-primary hover:bg-blue-500 text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2 uppercase tracking-widest">
                <i data-lucide="shield-plus" class="w-4 h-4"></i> Registrar Lead
            </button>
        </header>

        <!-- SECCIÓN 1: PAGO (ADS) -->
        <section class="mb-12">
            <div class="flex items-center gap-3 mb-6 bg-zinc-900/40 p-3 rounded-2xl w-fit border border-amber-500/10">
                <i data-lucide="zap" class="w-5 h-5 text-amber-500"></i>
                <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Campaña de Pago <span class="text-zinc-700 ml-1 font-bold">(Inversión Activa)</span></h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-card border border-border p-8 rounded-[2rem] flex flex-col justify-between">
                    <div>
                        <div class="text-[9px] font-black text-zinc-600 uppercase tracking-widest mb-4">Total Histórico (Ads)</div>
                        <div class="text-6xl font-black text-white"><?php echo $paidTotal; ?></div>
                        <div class="text-2xl font-bold text-amber-500 mt-4"><?php echo number_format($paidValue, 0, ',', '.'); ?>€</div>
                    </div>
                </div>
                <div class="md:col-span-2 bg-zinc-900/30 border border-border rounded-[2.5rem] p-8 h-64">
                    <canvas id="paidChart"></canvas>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 2: ORGÁNICO -->
        <section class="mb-12 pt-8 border-t border-zinc-900">
            <div class="flex items-center gap-3 mb-6 bg-zinc-900/40 p-3 rounded-2xl w-fit border border-emerald-500/10">
                <i data-lucide="leaf" class="w-5 h-5 text-emerald-500"></i>
                <h2 class="text-xs font-black text-white uppercase tracking-[0.2em]">Crecimiento Orgánico <span class="text-zinc-700 ml-1 font-bold">(Tráfico Natural)</span></h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-card border border-border p-8 rounded-[2rem] flex flex-col justify-between">
                    <div>
                        <div class="text-[9px] font-black text-zinc-600 uppercase tracking-widest mb-4">Total Histórico (Org)</div>
                        <div class="text-6xl font-black text-white"><?php echo $organicTotal; ?></div>
                        <div class="text-2xl font-bold text-emerald-500 mt-4"><?php echo number_format($organicValue, 0, ',', '.'); ?>€</div>
                    </div>
                </div>
                <div class="md:col-span-2 bg-zinc-900/30 border border-border rounded-[2.5rem] p-8 h-64">
                    <canvas id="organicChart"></canvas>
                </div>
            </div>
        </section>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();
        const cfg = { type: 'line', options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false }, ticks: { color: '#3f3f46', font: { size: 9, weight: 'bold' } } },
            y: { beginAtZero: true, grid: { color: '#18181b' }, ticks: { color: '#3f3f46', font: { size: 9 }, stepSize: 1 } } }
        }};

        // Gráfico Pago
        new Chart(document.getElementById('paidChart').getContext('2d'), { ...cfg, data: { labels: <?php echo json_encode($dates); ?>,
            datasets: [{ data: <?php echo json_encode($paidData); ?>, borderColor: '#f59e0b', borderWidth: 4, tension: 0.4, fill: true, backgroundColor: 'rgba(245, 158, 11, 0.05)', pointRadius: 0 }]
        }});
        
        // Gráfico Orgánico
        new Chart(document.getElementById('organicChart').getContext('2d'), { ...cfg, data: { labels: <?php echo json_encode($dates); ?>,
            datasets: [{ data: <?php echo json_encode($orgData); ?>, borderColor: '#10b981', borderWidth: 4, tension: 0.4, fill: true, backgroundColor: 'rgba(16, 185, 129, 0.05)', pointRadius: 0 }]
        }});
    </script>
</body>
</html>
<?php $conn->close(); ?>
