<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';
$totalLeadsResult = $conn->query("SELECT COUNT(*) as total FROM leads");
$totalLeads = $totalLeadsResult->fetch_assoc()['total'];

$sourcesResult = $conn->query("SELECT source, COUNT(*) as count FROM leads GROUP BY source");
$sources = [];
while ($row = $sourcesResult->fetch_assoc()) { $sources[$row['source']] = $row['count']; }

$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 6");

$revenueResult = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE status IN ('ganado', 'propuesta_enviada')");
$revenue = $revenueResult->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Master Analytics - CRM Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-14 space-y-12">
        <!-- Serious Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-12 border-b border-slate-200 group">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-pulse shadow-md shadow-indigo-100"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200 shadow-sm opacity-80">Monitor Engine Active</span>
                </div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3 italic">Panel <span class="text-indigo-600 not-italic font-black uppercase">Administrativo</span></h1>
                <p class="text-slate-400 text-sm font-medium max-w-lg mt-3">Visualización técnica de prospectos comerciales y rendimiento operacional.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end mr-6 border-r pr-6 border-slate-200">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Status de Conexión</span>
                    <span class="text-[11px] font-black text-emerald-600 uppercase flex items-center gap-2 tracking-tighter shadow-sm bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 italic">xCloud Link OK</span>
                </div>
                <button onclick="location.reload()" class="p-3 bg-white border border-slate-200 rounded-lg text-slate-400 hover:text-slate-900 hover:border-slate-800 transition-all shadow-sm active:scale-95 group">
                    <i data-lucide="refresh-cw" class="w-4 h-4 group-hover:rotate-180 transition-all"></i>
                </button>
            </div>
        </header>

        <!-- Stats Section (Serious Grid) -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="card-premium relative p-8 rounded-xl group overflow-hidden">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-slate-900 rounded-lg flex items-center justify-center text-white shadow-xl shadow-slate-200 group-hover:scale-105 transition-all">
                        <i data-lucide="users" class="w-6 h-6 stroke-[2.5]"></i>
                    </div>
                </div>
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Prospectos Totales</h3>
                <div class="text-3xl font-black text-slate-900 tracking-tight tabular-nums italic"><?php echo $totalLeads; ?> <span class="text-slate-200 not-italic font-bold text-xs uppercase ml-2 tracking-widest">Leads</span></div>
            </div>

            <div class="card-premium relative p-8 rounded-xl group overflow-hidden border-l-4 border-l-indigo-600 shadow-lg">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-xl shadow-indigo-100 group-hover:scale-105 transition-all">
                        <i data-lucide="trending-up" class="w-6 h-6 stroke-[2.5]"></i>
                    </div>
                </div>
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Volumen Proyectado</h3>
                <div class="text-3xl font-black text-slate-900 tracking-tight tabular-nums italic"><?php echo number_format($revenue, 2, ',', '.'); ?>€</div>
            </div>

            <div class="card-premium relative p-8 rounded-xl group overflow-hidden border-l-4 border-l-amber-500">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center text-white shadow-xl shadow-amber-100 group-hover:scale-105 transition-all">
                        <i data-lucide="zap" class="w-6 h-6 stroke-[2.5] fill-white/20"></i>
                    </div>
                </div>
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Captura Paid Ads</h3>
                <div class="text-3xl font-black text-slate-900 tracking-tight tabular-nums italic"><?php echo $sources['pago'] ?? 0; ?> <span class="text-slate-200 not-italic font-bold text-xs uppercase ml-2 tracking-widest">Leads</span></div>
            </div>

            <div class="card-premium relative p-8 rounded-xl group overflow-hidden border-l-4 border-l-emerald-500">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-emerald-500 rounded-lg flex items-center justify-center text-white shadow-xl shadow-emerald-100 group-hover:scale-105 transition-all">
                        <i data-lucide="leaf" class="w-6 h-6 stroke-[2.5] fill-white/20"></i>
                    </div>
                </div>
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-0.5">Captura Orgánica</h3>
                <div class="text-3xl font-black text-slate-900 tracking-tight tabular-nums italic"><?php echo $sources['organico'] ?? 0; ?> <span class="text-slate-200 not-italic font-bold text-xs uppercase ml-2 tracking-widest">Leads</span></div>
            </div>
        </section>

        <!-- Serious Data Section -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-10 min-h-[500px]">
            <!-- Activity Table Card -->
            <div class="lg:col-span-8 card-premium p-10 rounded-xl relative overflow-hidden group shadow-xl">
                <div class="flex items-center justify-between mb-10 pb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight uppercase italic">Actividad <span class="text-indigo-600 not-italic">Operacional Reciente</span></h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 opacity-60">Log de últimos ingresos detectados</p>
                    </div>
                    <a href="leads.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-900 hover:text-white text-[10px] font-bold text-slate-600 rounded-lg uppercase tracking-widest transition-all shadow-sm active:scale-95">Ir al Historial</a>
                </div>

                <div class="space-y-4">
                    <?php while($lead = $recentLeads->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-5 bg-slate-50/50 border border-slate-100 hover:bg-white hover:border-slate-300 rounded-xl transition-all hover:shadow-lg hover:shadow-slate-100 group/row cursor-pointer" onclick="location.href='leads.php'">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-white border border-slate-100 rounded-lg flex items-center justify-center text-slate-700 font-bold text-sm shadow-sm group-hover/row:bg-slate-900 group-hover/row:text-white transition-all overflow-hidden uppercase">
                                <?php echo substr($lead['name'], 0, 1); ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900 group-hover/row:text-indigo-600 transition-all tracking-tight leading-none text-base italic uppercase truncate max-w-[200px]"><?php echo htmlspecialchars($lead['name']); ?></span>
                                <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest block"><?php echo htmlspecialchars($lead['company'] ?: 'Particular'); ?></span>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-8">
                            <div class="hidden md:block">
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest block mb-1">Status Estamento</span>
                                <span class="px-3 py-1 bg-white border border-slate-200 rounded-md text-[9px] font-bold uppercase text-slate-600 shadow-sm"><?php echo strtoupper($lead['status'] ?? 'NUEVO'); ?></span>
                            </div>
                            <div class="flex flex-col items-end min-w-[100px]">
                                <span class="text-lg font-black text-slate-900 italic tabular-nums group-hover/row:text-indigo-600 transition-all"><?php echo number_format($lead['proposal_price'] ?? 0, 0, ',', '.'); ?>€</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase opacity-60 tabular-nums"><?php echo date('H:i', strtotime($lead['created_at'])); ?> h</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 group-hover/row:text-indigo-600 transition-all group-hover/row:translate-x-1"></i>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Conversion Insights Card -->
            <div class="lg:col-span-4 bg-white border border-slate-200 p-10 rounded-xl shadow-xl relative overflow-hidden flex flex-col justify-between group group-hover:border-indigo-100 transition-all">
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-14 pb-6 border-b border-slate-50">
                        <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center text-white ring-4 ring-slate-100 shadow-xl shadow-slate-100">
                            <i data-lucide="zap" class="w-5 h-5 fill-white/10 stroke-[2.5]"></i>
                        </div>
                        <h3 class="text-lg font-bold tracking-tight uppercase italic text-slate-900">Eficacia <span class="text-indigo-600">Comercial</span></h3>
                    </div>

                    <div class="space-y-12">
                        <div class="space-y-5">
                            <div class="flex justify-between items-end">
                                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">Tasa de Conversión</span>
                                <span class="text-2xl font-black italic tabular-nums text-slate-900">24.8%</span>
                            </div>
                            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50 p-0.5 shadow-inner">
                                <div class="w-[24.8%] h-full bg-slate-900 rounded-full transition-all duration-1000 shadow-lg"></div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="flex justify-between items-end">
                                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">Churn Rate Estimado</span>
                                <span class="text-2xl font-black italic tabular-nums text-slate-400">04.2%</span>
                            </div>
                            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50 p-0.5 shadow-inner">
                                <div class="w-[12.2%] h-full bg-slate-300 rounded-full transition-all duration-1000"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-16 pt-8 border-t border-slate-50">
                    <button class="w-full py-4 bg-slate-900 text-white font-bold rounded-lg text-[11px] uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl active:scale-95 group flex items-center justify-center gap-3">
                        Expandir Métrica <i data-lucide="activity" class="w-4 h-4 group-hover:scale-125 transition-all text-indigo-400"></i>
                    </button>
                    <p class="text-center text-slate-300 text-[8px] font-bold uppercase tracking-widest mt-6 italic opacity-80">Algoritmo de predicción Master AI activo</p>
                </div>
            </div>
        </section>
    </main>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
<?php $conn->close(); ?>
