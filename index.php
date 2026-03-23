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
    <title>CRM Dashboard - Premium SaaS</title>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen">
        <div class="mb-10 animate-in fade-in slide-in-from-bottom-5 duration-700">
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-500 mb-2">Panel de Control</h1>
            <p class="text-gray-400 text-lg">Resumen de actividad en tiempo real de tu CRM.</p>
        </div>

        <!-- Estadísticas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-indigo-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-500/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-6 h-6 text-indigo-500"></i>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 bg-green-500/10 text-green-500 rounded-lg">TOTAL</span>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Total Leads</h3>
                <span class="text-3xl font-bold text-white block mt-1"><?php echo $totalLeads; ?></span>
            </div>

            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-emerald-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-500/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="calendar" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 bg-indigo-500/10 text-indigo-500 rounded-lg">HOY</span>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Leads Registrados</h3>
                <span class="text-3xl font-bold text-white block mt-1"><?php echo $todayLeads; ?></span>
            </div>

            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-violet-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-violet-500/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="line-chart" class="w-6 h-6 text-violet-500"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Tasa de Registro</h3>
                <span class="text-3xl font-bold text-white block mt-1"><?php echo $conversionRate; ?>%</span>
                <p class="text-gray-500 text-xs mt-2 font-medium italic">Calculado hoy vs total</p>
            </div>

            <div class="p-6 bg-dark-card border border-dark-border rounded-2xl hover:border-amber-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-500/10 rounded-xl group-hover:scale-110 transition-transform">
                        <i data-lucide="zap" class="w-6 h-6 text-amber-500"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-sm font-medium">Acción Rápida</h3>
                <a href="add-lead.php" class="inline-block mt-3 text-sm font-semibold text-amber-500 hover:text-white transition-colors bg-amber-500/10 hover:bg-amber-600 px-4 py-2 rounded-xl">Nuevo Registro +</a>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Recent Table -->
            <div class="xl:col-span-2 p-6 bg-dark-card border border-dark-border rounded-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-indigo-400"></i>
                        Última Actividad
                    </h2>
                    <a href="leads.php" class="text-sm font-medium text-indigo-400 hover:text-indigo-300">Ver todo &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-dark-border">
                                <th class="pb-4">Nombre</th>
                                <th class="pb-4">Email</th>
                                <th class="pb-4 text-center">Estado</th>
                                <th class="pb-4 text-right">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-border">
                            <?php while($row = $recentLeads->fetch_assoc()): ?>
                            <tr class="hover:bg-indigo-500/5 transition-colors group">
                                <td class="py-4 font-semibold text-gray-200"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="py-4">
                                    <span class="px-3 py-1 bg-dark-border/50 text-gray-400 text-sm rounded-lg group-hover:text-indigo-300 transition-colors">
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </span>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="px-2 py-1 bg-indigo-500/10 text-indigo-400 text-xs font-bold rounded-lg uppercase">Nuevo</span>
                                </td>
                                <td class="py-4 text-right text-sm text-gray-500"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Profile / Info Card -->
            <div class="p-6 bg-gradient-to-b from-indigo-900/20 to-dark-card border border-dark-border rounded-2xl flex flex-col justify-center text-center">
                <div class="w-20 h-20 bg-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-6 border border-indigo-500/30">
                    <i data-lucide="shield-check" class="w-10 h-10 text-indigo-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Seguridad Activa</h3>
                <p class="text-gray-400 text-sm mb-6">Tu CRM está protegido con autenticación privada y bloqueo de indexación.</p>
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between text-xs px-4 py-3 bg-dark-card/50 rounded-xl border border-dark-border">
                        <span class="text-gray-500 uppercase font-bold tracking-widest">Base de Datos</span>
                        <span class="text-green-500 flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> Online</span>
                    </div>
                    <div class="flex items-center justify-between text-xs px-4 py-3 bg-dark-card/50 rounded-xl border border-dark-border">
                        <span class="text-gray-500 uppercase font-bold tracking-widest">Visibilidad IA</span>
                        <span class="text-red-500 flex items-center gap-1"><i data-lucide="slash" class="w-3 h-3"></i> Oculta</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
