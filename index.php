<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// --- CÁLCULOS SEMANALES (Lunes a Domingo) ---
$weekQuery = "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";

// Total Leads de la semana
$totalWeek = $conn->query("SELECT COUNT(*) as count FROM leads WHERE $weekQuery")->fetch_assoc()['count'];

// Leads Orgánicos de la semana
$organicWeek = $conn->query("SELECT COUNT(*) as count FROM leads WHERE $weekQuery AND source = 'organico'")->fetch_assoc()['count'];

// Leads de Pago de la semana
$paidWeek = $conn->query("SELECT COUNT(*) as count FROM leads WHERE $weekQuery AND source = 'pago'")->fetch_assoc()['count'];

// Facturación/Propuestas potenciales de la semana
$revenueWeek = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE $weekQuery")->fetch_assoc()['total'] ?? 0;

// Leads recientes (últimos 6 de siempre para referencia)
$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Dashboard Semanal - CRM Blue Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-8 min-h-screen bg-bg">
        <header class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tighter">Dashboard <span class="text-zinc-700 italic font-medium ml-2 uppercase text-sm tracking-widest">Semanal</span></h1>
                <p class="text-zinc-500 text-sm mt-1">Monitoreo de rendimiento: Lunes a Domingo (Semana Actual).</p>
            </div>
            <div class="px-4 py-2 bg-zinc-900 border border-zinc-800 rounded-xl text-[10px] font-black text-zinc-500 uppercase tracking-widest shadow-sm">
                Año <?php echo date('Y'); ?> &middot; Semana <?php echo date('W'); ?>
            </div>
        </header>

        <!-- Métricas Segmentadas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <!-- Total de la Semana -->
            <div class="bg-card border border-border p-8 rounded-3xl shadow-sm relative overflow-hidden group hover:border-blue-500/30 transition-all">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 rounded-full -mr-8 -mt-8"></div>
                <div class="relative z-10">
                    <div class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] mb-4">Volumen Semanal</div>
                    <div class="flex items-end gap-3">
                        <span class="text-5xl font-black text-white tabular-nums leading-none"><?php echo $totalWeek; ?></span>
                        <span class="text-xs font-bold text-blue-500 pb-1 uppercase">Leads</span>
                    </div>
                </div>
            </div>

            <!-- Orgánico -->
            <div class="bg-card border border-border p-8 rounded-3xl shadow-sm relative overflow-hidden transition-all hover:bg-zinc-900/40">
                <div class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                   <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div> Orgánico
                </div>
                <div class="text-4xl font-black text-white tabular-nums mb-1"><?php echo $organicWeek; ?></div>
                <div class="text-[10px] font-bold text-zinc-600 uppercase">Tráfico Gratuito</div>
            </div>

            <!-- Pago -->
            <div class="bg-card border border-border p-8 rounded-3xl shadow-sm relative overflow-hidden transition-all hover:bg-zinc-900/40">
                <div class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                   <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div> Pago (Ads)
                </div>
                <div class="text-4xl font-black text-white tabular-nums mb-1"><?php echo $paidWeek; ?></div>
                <div class="text-[10px] font-bold text-zinc-600 uppercase">Inversión Activa</div>
            </div>

            <!-- Valor Proyectado -->
            <div class="bg-card border-2 border-blue-600/20 p-8 rounded-3xl shadow-xl shadow-blue-600/5 relative overflow-hidden bg-zinc-900/50">
                <div class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-4">Valor Estimado</div>
                <div class="text-4xl font-black text-white tabular-nums mb-1"><?php echo number_format($revenueWeek, 0, ',', '.'); ?>€</div>
                <div class="text-[10px] font-bold text-zinc-500 uppercase">Propuestas enviadas</div>
            </div>
        </div>

        <!-- Tabla de Actividad Reciente -->
        <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-sm">
            <div class="px-8 py-6 border-b border-border flex items-center justify-between bg-zinc-900/30">
                <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.2em]">Actividad Reciente</h2>
                <a href="leads.php" class="text-[10px] font-black text-blue-500 hover:text-blue-400 uppercase tracking-widest transition-colors flex items-center gap-2">Ver todos <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-950/50 text-[10px] font-black uppercase tracking-widest text-zinc-700 border-b border-border">
                            <th class="px-8 py-4">Lead / Empresa</th>
                            <th class="px-8 py-4">Fuente</th>
                            <th class="px-8 py-4 text-right">Fecha Entrada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <?php while($row = $recentLeads->fetch_assoc()): ?>
                        <tr class="hover:bg-zinc-900/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white mb-0.5"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="text-[10px] text-zinc-600 uppercase font-bold tracking-tighter"><?php echo $row['company'] ?: 'Particular'; ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border <?php echo $row['source'] == 'pago' ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'; ?>">
                                    <?php echo $row['source']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="text-xs font-bold text-zinc-400 tabular-nums"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
