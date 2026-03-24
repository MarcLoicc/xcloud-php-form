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
    <title>Master Analytics (Dark) | CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-slate-950 min-h-screen text-slate-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-14 space-y-12">
        <!-- Dashboard Header Dark -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-10 pb-12 border-b border-slate-800 group">
            <div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse shadow-md shadow-indigo-600"></div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] bg-slate-900 px-3 py-1 rounded-full border border-slate-800">Operational Monitor Active</span>
                </div>
                <h1 class="text-3xl font-black text-white tracking-widest flex items-center gap-4 italic uppercase">ANALYTICS <span class="text-indigo-500 not-italic font-black">CONSOLE</span></h1>
                <p class="text-slate-500 text-[14px] font-medium max-w-lg mt-4 leading-relaxed">Control centralizado de prospectos comerciales y flujo de adquisición en tiempo real.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end mr-6 border-r pr-6 border-slate-800">
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-2">Sync Status</span>
                    <span class="text-[12px] font-black text-emerald-400 uppercase flex items-center gap-2 tracking-tighter shadow-xl bg-emerald-950 px-4 py-1.5 rounded-full border border-emerald-900 italic">Cloud Connected</span>
                </div>
                <button onclick="location.reload()" class="p-4 bg-slate-900 border border-slate-800 rounded-xl text-slate-500 hover:text-white hover:border-slate-600 transition-all shadow-xl active:scale-95 group">
                    <i data-lucide="refresh-cw" class="w-5 h-5 group-hover:rotate-180 transition-all"></i>
                </button>
            </div>
        </header>

        <!-- Stats Section Dark -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="card-premium relative p-8 rounded-xl group overflow-hidden border border-slate-800">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-slate-950 shadow-2xl group-hover:scale-105 transition-all">
                        <i data-lucide="users" class="w-6 h-6 stroke-[2.5]"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2 ml-1">TOTAL LEADS</h3>
                <div class="text-4xl font-black text-white tracking-tight tabular-nums italic"><?php echo $totalLeads; ?> <span class="text-slate-800 not-italic font-black text-xs uppercase ml-3 tracking-widest">Prospectos</span></div>
            </div>

            <div class="card-premium relative p-8 rounded-xl group overflow-hidden border-l-4 border-l-indigo-600 shadow-2xl">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-2xl shadow-indigo-900 group-hover:scale-105 transition-all">
                        <i data-lucide="trending-up" class="w-6 h-6 stroke-[2.5]"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2 ml-1">REVENUE MASTER</h3>
                <div class="text-4xl font-black text-white tracking-tight tabular-nums italic"><?php echo number_format($revenue, 2, ',', '.'); ?>€</div>
            </div>

            <div class="card-premium relative p-8 rounded-xl group overflow-hidden border-l-4 border-l-amber-600">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-12 h-12 bg-amber-600 rounded-lg flex items-center justify-center text-white shadow-2xl group-hover:scale-105 transition-all">
                        <i data-lucide="zap" class="w-6 h-6 stroke-[2.5] fill-white/20"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2 ml-1">PAID ADS CAPTURE</h3>
                <div class="text-4xl font-black text-white tracking-tight tabular-nums italic"><?php echo $sources['pago'] ?? 0; ?> <span class="text-slate-800 not-italic font-black text-xs uppercase ml-3 tracking-widest">LEADS</span></div>
            </div>

            <div class="card-premium relative p-8 rounded-xl group overflow-hidden border-l-4 border-l-emerald-600">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-12 h-12 bg-emerald-600 rounded-lg flex items-center justify-center text-white shadow-2xl group-hover:scale-105 transition-all">
                        <i data-lucide="leaf" class="w-6 h-6 stroke-[2.5] fill-white/20"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2 ml-1">ORGANIC CAPTURE</h3>
                <div class="text-4xl font-black text-white tracking-tight tabular-nums italic"><?php echo $sources['organico'] ?? 0; ?> <span class="text-slate-800 not-italic font-black text-xs uppercase ml-3 tracking-widest">LEADS</span></div>
            </div>
        </section>

        <!-- Serious Data Section Dark -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-10 min-h-[500px]">
            <!-- Table Card Dark -->
            <div class="lg:col-span-8 card-premium p-10 rounded-xl relative overflow-hidden group shadow-2xl">
                <div class="flex items-center justify-between mb-12 pb-8 border-b border-slate-800">
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-widest uppercase italic leading-none">ACTIVOS <span class="text-indigo-500 not-italic">RECIENTES</span></h3>
                        <p class="text-[11px] text-slate-500 font-black uppercase tracking-[0.4em] mt-3 opacity-60">Log de auditoría de los últimos prospectos en el clúster.</p>
                    </div>
                    <a href="leads.php" class="px-6 py-3.5 bg-slate-800 hover:bg-white hover:text-slate-950 text-[11px] font-black text-slate-400 rounded-lg uppercase tracking-widest transition-all shadow-xl active:scale-95 border border-slate-700">Explorar Todo</a>
                </div>

                <div class="space-y-6">
                    <?php while($lead = $recentLeads->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-6 bg-slate-950/50 border border-slate-800/80 hover:bg-slate-800 hover:border-indigo-600 transition-all cursor-pointer group/row rounded-xl shadow-lg" onclick="location.href='leads.php'">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-slate-900 border border-slate-800 rounded-lg flex items-center justify-center text-white font-black text-xl shadow-inner group-hover/row:bg-white group-hover/row:text-slate-950 transition-all uppercase italic">
                                <?php echo substr($lead['name'], 0, 1); ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-black text-white group-hover/row:text-indigo-400 transition-all tracking-tight leading-none text-xl italic uppercase truncate max-w-[300px]"><?php echo htmlspecialchars($lead['name']); ?></span>
                                <span class="text-[11px] font-black text-slate-600 mt-2 uppercase tracking-widest block opacity-80"><?php echo htmlspecialchars($lead['company'] ?: 'ENTIDAD PARTICULAR'); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-10">
                            <div class="hidden md:flex flex-col items-end">
                                <span class="text-[9px] font-black text-slate-700 uppercase tracking-widest mb-2 block">STATUS</span>
                                <span class="px-4 py-1.5 bg-slate-900 border border-slate-800 rounded-md text-[10px] font-black uppercase text-slate-400 group-hover/row:border-slate-600 shadow-xl italic"><?php echo strtoupper($lead['status'] ?? 'NUEVO'); ?></span>
                            </div>
                            <div class="flex flex-col items-end min-w-[120px]">
                                <span class="text-2xl font-black text-white italic tabular-nums group-hover/row:text-indigo-400 transition-all"><?php echo number_format($lead['proposal_price'] ?? 0, 0, ',', '.'); ?>€</span>
                                <span class="text-[11px] font-black text-slate-700 uppercase opacity-60 tabular-nums mt-1"><?php echo date('H:i', strtotime($lead['created_at'])); ?> HRS</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-6 h-6 text-slate-800 group-hover/row:text-indigo-400 transition-all group-hover/row:translate-x-2"></i>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Insights Card Dark -->
            <div class="lg:col-span-4 bg-slate-900 border border-slate-800 p-10 rounded-xl shadow-2xl relative overflow-hidden flex flex-col justify-between group hover:border-indigo-600 transition-all">
                <div class="relative z-10">
                    <div class="flex items-center gap-5 mb-20 pb-10 border-b border-slate-800">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-950 shadow-2xl transition-all group-hover:scale-105">
                            <i data-lucide="zap" class="w-6 h-6 fill-slate-950 stroke-[2.5]"></i>
                        </div>
                        <h3 class="text-xl font-black tracking-widest uppercase italic text-white leading-none">RATIO <span class="text-indigo-500">COMERCIAL</span></h3>
                    </div>

                    <div class="space-y-16">
                        <div class="space-y-6">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-600 italic">Conversión Real</span>
                                <span class="text-4xl font-black italic tabular-nums text-white group-hover:text-indigo-400 transition-all">24.8%</span>
                            </div>
                            <div class="w-full h-3 bg-slate-950 rounded-full overflow-hidden border border-slate-800 p-0.5 shadow-inner">
                                <div class="w-[24.8%] h-full bg-indigo-600 rounded-full transition-all duration-1000 shadow-[0_0_15px_#4f46e5]"></div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-600 italic">Índice Abandono</span>
                                <span class="text-4xl font-black italic tabular-nums text-slate-700 group-hover:text-slate-400 transition-all">04.2%</span>
                            </div>
                            <div class="w-full h-3 bg-slate-950 rounded-full overflow-hidden border border-slate-800 p-0.5 shadow-inner">
                                <div class="w-[12.2%] h-full bg-slate-800 rounded-full transition-all duration-1000"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-20 pt-10 border-t border-slate-800">
                    <button class="w-full py-5 bg-white text-slate-950 font-black rounded-lg text-[13px] uppercase tracking-[0.3em] hover:bg-slate-200 transition-all shadow-2xl active:scale-95 group flex items-center justify-center gap-4">
                        DETALLE TÉCNICO <i data-lucide="activity" class="w-5 h-5 group-hover:scale-125 transition-all text-indigo-600"></i>
                    </button>
                    <p class="text-center text-slate-700 text-[9px] font-black uppercase tracking-[0.5em] mt-8 italic opacity-60">MASTER AI PREDICTION ENGINE ACTIVATED</p>
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
