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

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen">
        <div class="mb-10 animate-in fade-in slide-in-from-bottom-5 duration-700">
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-500 mb-2">Panel de Control</h1>
            <p class="text-gray-400 text-lg">Resumen de actividad en tiempo real de tu CRM.</p>
        </div>

        <!-- Estadísticas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-blue-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-6 h-6 text-blue-500"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Total Leads</h3>
                <span class="text-3xl font-bold text-white block mt-1"><?php echo $totalLeads; ?></span>
            </div>

            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-cyan-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-cyan-500/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="trending-up" class="w-6 h-6 text-cyan-500"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Hoy</h3>
                <span class="text-3xl font-bold text-white block mt-1"><?php echo $todayLeads; ?></span>
            </div>

            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-blue-600/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-600/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="line-chart" class="w-6 h-6 text-blue-400"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Crecimiento</h3>
                <span class="text-3xl font-bold text-white block mt-1"><?php echo $conversionRate; ?>%</span>
                <p class="text-gray-500 text-xs mt-2 italic">Diario vs Historico</p>
            </div>

            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-blue-400/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-400/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="plus" class="w-6 h-6 text-blue-400"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Acción Rápida</h3>
                <button onclick="toggleModal()" class="w-full mt-3 text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 px-4 py-2.5 rounded-xl transition-all shadow-lg shadow-blue-600/20 active:scale-95">Registrar Lead +</button>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="grid grid-cols-1 gap-6">
            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-blue-400"></i>
                        Recién Llegados
                    </h2>
                    <a href="leads.php" class="text-sm font-medium text-blue-400 hover:text-blue-300">Ir a lista completa &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-dark-border">
                                <th class="pb-4">Nombre</th>
                                <th class="pb-4">Email</th>
                                <th class="pb-4">Calidad</th>
                                <th class="pb-4 text-right">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-border">
                            <?php while($row = $recentLeads->fetch_assoc()): ?>
                            <tr class="hover:bg-blue-500/5 transition-colors group">
                                <td class="py-4 font-semibold text-gray-200"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="py-4">
                                    <span class="px-3 py-1 bg-blue-900/10 text-blue-300 text-xs rounded-lg border border-blue-500/10">
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </span>
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 bg-green-500/10 text-green-400 text-[10px] font-black rounded-lg uppercase">Nuevo</span>
                                </td>
                                <td class="py-4 text-right text-sm text-gray-500 font-medium"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></td>
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
