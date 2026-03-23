<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// Estadísticas Pro
$totalLeads = $conn->query("SELECT COUNT(*) as count FROM leads")->fetch_assoc()['count'];
$todayLeads = $conn->query("SELECT COUNT(*) as count FROM leads WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$conversionRate = ($totalLeads > 0) ? round(($todayLeads / $totalLeads) * 100, 1) : 0;
$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>CRM Dashboard - Blue Pro</title>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-8 min-h-screen bg-bg">
        <header class="mb-10">
            <h1 class="text-3xl font-semibold text-white tracking-tight">Dashboard</h1>
            <p class="text-zinc-500 text-sm mt-1">Resumen de actividad y métricas clave.</p>
        </header>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="p-6 bg-card border border-border rounded-xl shadow-sm">
                <div class="flex items-center gap-3 mb-4 text-zinc-500">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Total Leads</span>
                </div>
                <div class="text-4xl font-bold text-white tracking-tight leading-none"><?php echo $totalLeads; ?></div>
            </div>

            <div class="p-6 bg-card border border-border rounded-xl shadow-sm">
                <div class="flex items-center gap-3 mb-4 text-zinc-500">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Hoy</span>
                </div>
                <div class="text-4xl font-bold text-white tracking-tight leading-none"><?php echo $todayLeads; ?></div>
            </div>

            <div class="p-6 bg-card border border-border rounded-xl shadow-sm">
                <div class="flex items-center gap-3 mb-4 text-zinc-500">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Tasa Diaria</span>
                </div>
                <div class="text-4xl font-bold text-white tracking-tight leading-none"><?php echo $conversionRate; ?>%</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="bg-card border border-border rounded-xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-border bg-zinc-900/20 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-primary"></i>
                        Actividad Reciente
                    </h2>
                    <a href="leads.php" class="text-xs font-medium text-primary hover:text-blue-400 transition-colors">Ver todos</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-zinc-500 text-[11px] font-semibold uppercase tracking-wider border-b border-border bg-zinc-900/30">
                                <th class="px-6 py-4">Lead</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-right">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php while($row = $recentLeads->fetch_assoc()): ?>
                            <tr class="hover:bg-zinc-900/40 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-white group-hover:text-primary transition-colors"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <span class="text-xs text-zinc-600 mt-0.5"><?php echo htmlspecialchars($row['email'] ?: 'Sin contacto'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 bg-green-500/10 text-green-500 text-[10px] font-bold rounded-md">NUEVO</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs text-zinc-400 font-medium"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
