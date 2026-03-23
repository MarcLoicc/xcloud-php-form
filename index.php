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

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen flex flex-col">
        <header class="mb-10 animate-in fade-in slide-in-from-top-4 duration-700">
            <h1 class="text-6xl font-black text-white tracking-tighter uppercase italic">Panel de Control</h1>
            <p class="mt-2 text-zinc-500 text-lg tracking-tight">Resumen estratégico de actividad en tiempo real de tu CRM.</p>
        </header>

        <!-- Estadísticas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="p-8 bg-zinc-950 border border-zinc-900 rounded-3xl hover:border-blue-500/30 transition-all group shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-125 transition-transform">
                    <i data-lucide="users" class="w-20 h-20 text-blue-500"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mb-4">Total Leads</h3>
                    <span class="text-5xl font-black text-white italic tracking-tighter"><?php echo $totalLeads; ?></span>
                </div>
            </div>

            <div class="p-8 bg-zinc-950 border border-zinc-900 rounded-3xl hover:border-blue-500/30 transition-all group shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-125 transition-transform">
                    <i data-lucide="trending-up" class="w-20 h-20 text-blue-500"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mb-4">Registros Hoy</h3>
                    <span class="text-5xl font-black text-white italic tracking-tighter"><?php echo $todayLeads; ?></span>
                </div>
            </div>

            <div class="p-8 bg-zinc-950 border border-zinc-900 rounded-3xl hover:border-blue-500/30 transition-all group shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-125 transition-transform">
                    <i data-lucide="line-chart" class="w-20 h-20 text-blue-500"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mb-4">Conversión</h3>
                    <span class="text-5xl font-black text-white italic tracking-tighter"><?php echo $conversionRate; ?>%</span>
                </div>
            </div>

            <div class="p-8 bg-zinc-950 border border-zinc-900 rounded-3xl hover:border-blue-500/30 transition-all group shadow-2xl flex flex-col justify-center gap-4">
                <button onclick="toggleModal()" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-blue-600/20 active:scale-95 uppercase tracking-widest text-xs">
                    <i data-lucide="plus" class="w-4 h-4 inline-block mr-2"></i> Nuevo Lead
                </button>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="grid grid-cols-1 gap-6">
            <div class="bg-zinc-950 border border-zinc-900 rounded-[3rem] overflow-hidden shadow-2xl">
                <div class="p-8 border-b border-zinc-900 bg-zinc-900/20 flex items-center justify-between">
                    <h2 class="text-xl font-black text-white italic uppercase tracking-tighter flex items-center gap-3">
                        <i data-lucide="clock" class="w-5 h-5 text-blue-500"></i>
                        Recién Llegados
                    </h2>
                    <a href="leads.php" class="text-xs font-black text-blue-500 hover:text-blue-400 uppercase tracking-widest">Ver todos &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-zinc-900">
                                <th class="px-10 py-6">Lead</th>
                                <th class="px-6 py-6 text-center">Estado</th>
                                <th class="px-10 py-6 text-right">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-900">
                            <?php while($row = $recentLeads->fetch_assoc()): ?>
                            <tr class="hover:bg-blue-600/5 transition-all group">
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-white font-black text-lg italic tracking-tighter group-hover:text-blue-400 transition-colors uppercase"><?php echo htmlspecialchars($row['name']); ?></span>
                                        <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest mt-1"><?php echo htmlspecialchars($row['email'] ?: 'Sin email'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-3 py-1.5 bg-green-500/5 text-green-500 text-[9px] font-black rounded-xl border border-green-500/10 uppercase tracking-widest">NUEVO</span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <span class="text-zinc-400 font-bold block text-sm tracking-widest"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></span>
                                    <span class="text-[10px] font-black text-zinc-700 tracking-[0.2em] block uppercase mt-1"><?php echo date('H:i', strtotime($row['created_at'])); ?>H</span>
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
