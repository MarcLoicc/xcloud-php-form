<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';
$totalLeadsResult = $conn->query("SELECT COUNT(*) as total FROM leads");
$totalLeads = $totalLeadsResult->fetch_assoc()['total'];

$sourcesResult = $conn->query("SELECT source, COUNT(*) as count FROM leads GROUP BY source");
$sources = [];
while ($row = $sourcesResult->fetch_assoc()) { $sources[$row['source']] = $row['count']; }

$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");

$revenueResult = $conn->query("SELECT SUM(proposal_price) as total FROM leads WHERE status IN ('ganado', 'propuesta_enviada')");
$revenue = $revenueResult->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Monitor CRM - Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-[#f0f2f5] min-h-screen text-[#1e293b]">
    <!-- Decorative Blurs -->
    <div class="fixed inset-0 z-[-1] pointer-events-none opacity-40">
      <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/20 blur-[120px] rounded-full"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-400/20 blur-[120px] rounded-full"></div>
    </div>

    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-72 min-h-screen p-8 lg:p-14 space-y-12">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-10 border-b border-white/60 group">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-3 h-3 bg-indigo-600 rounded-full animate-pulse shadow-lg shadow-indigo-200"></div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Analytics Engine 5.0</span>
                </div>
                <h1 class="text-5xl font-black text-slate-800 tracking-tighter italic">Monitor <span class="text-indigo-600 not-italic font-medium">Empresarial</span></h1>
                <p class="text-slate-500 text-sm mt-3 font-medium max-w-lg">Bienvenido de nuevo, Marc. Aquí tienes el rendimiento total de tus fuentes de adquisición hoy.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden lg:block">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Status Servidor</span>
                    <span class="text-sm font-bold text-emerald-500 tabular-nums uppercase flex items-center gap-2"><div class="w-2 h-2 bg-emerald-500 rounded-full"></div> ONLINE</span>
                </div>
                <button onclick="location.reload()" class="px-8 py-4 bg-white border border-white/80 rounded-3xl text-xs font-black text-slate-700 hover:bg-slate-50 transition-all shadow-xl shadow-slate-100 flex items-center gap-3 active:scale-95 group">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-indigo-500 group-hover:rotate-180 transition-all"></i>
                    Sincronizar Cloud
                </button>
            </div>
        </header>

        <!-- Stats Section -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="glass-card indigo-glow-hover relative p-10 rounded-[3.5rem] transition-all hover:-translate-y-2 group overflow-hidden">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-16 h-16 bg-slate-900 rounded-[2rem] flex items-center justify-center text-white shadow-2xl shadow-slate-300 ring-4 ring-slate-100">
                        <i data-lucide="users" class="w-8 h-8 stroke-[2.5]"></i>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full text-[10px] font-black uppercase tracking-tight">+12.5%</span>
                </div>
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Leads en Pipeline</h3>
                <div class="text-4xl font-black text-slate-800 tracking-tighter tabular-nums italic"><?php echo $totalLeads; ?></div>
            </div>

            <div class="glass-card indigo-glow-hover relative p-10 rounded-[3.5rem] transition-all hover:-translate-y-2 group overflow-hidden">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-16 h-16 bg-indigo-600 rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-100 ring-4 ring-indigo-50">
                        <i data-lucide="wallet" class="w-8 h-8 stroke-[2.5]"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Capital Proyectado</h3>
                <div class="text-4xl font-black text-slate-800 tracking-tighter tabular-nums italic"><?php echo number_format($revenue, 0, ',', '.'); ?>€</div>
            </div>

            <div class="glass-card indigo-glow-hover relative p-10 rounded-[3.5rem] transition-all hover:-translate-y-2 group overflow-hidden">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-[2.2rem] flex items-center justify-center border-2 border-white shadow-sm ring-4 ring-amber-50">
                        <i data-lucide="zap" class="w-8 h-8 stroke-[2.5] fill-amber-300/30"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Ads Conversión</h3>
                <div class="text-4xl font-black text-slate-800 tracking-tighter tabular-nums italic"><?php echo $sources['pago'] ?? 0; ?></div>
            </div>

            <div class="glass-card indigo-glow-hover relative p-10 rounded-[3.5rem] transition-all hover:-translate-y-2 group overflow-hidden">
                <div class="flex justify-between items-start mb-10">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-[2.2rem] flex items-center justify-center border-2 border-white shadow-sm ring-4 ring-emerald-50">
                        <i data-lucide="leaf" class="w-8 h-8 stroke-[2.5] fill-emerald-300/30"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Origen Orgánico</h3>
                <div class="text-4xl font-black text-slate-800 tracking-tighter tabular-nums italic"><?php echo $sources['organico'] ?? 0; ?></div>
            </div>
        </section>

        <!-- Dynamic Content Section -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-10 min-h-[500px]">
            <!-- Recent Activity Card -->
            <div class="lg:col-span-8 glass-card p-12 rounded-[4rem] group relative overflow-hidden">
                <div class="flex items-center justify-between mb-12">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight italic">Actividad <span class="text-indigo-600 not-italic">en Tiempo Real</span></h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-2">Últimos prospectos detectados</p>
                    </div>
                    <a href="leads.php" class="px-6 py-3 bg-slate-900 border border-slate-800 text-[10px] font-black text-white rounded-2xl uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-slate-200">Ver Historial</a>
                </div>

                <div class="space-y-6">
                    <?php while($lead = $recentLeads->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-6 bg-white/40 border border-white hover:border-indigo-200 rounded-3xl transition-all hover:bg-white hover:shadow-xl hover:shadow-indigo-50/50 group/row group cursor-pointer" onclick="location.href='leads.php'">
                        <div class="flex items-center gap-5">
                            <div class="w-[52px] h-[52px] bg-slate-100 rounded-2xl flex items-center justify-center text-slate-500 font-black text-xl border-2 border-white shadow-sm group-hover/row:bg-indigo-600 group-hover/row:text-white transition-all overflow-hidden uppercase">
                                <?php echo substr($lead['name'], 0, 1); ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-black text-slate-800 group-hover/row:text-indigo-600 transition-all tracking-tight leading-none text-lg"><?php echo htmlspecialchars($lead['name']); ?></span>
                                <span class="text-[11px] font-bold text-slate-400 mt-2 uppercase tracking-widest"><?php echo htmlspecialchars($lead['company'] ?: 'Particular'); ?></span>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-6">
                            <div class="hidden md:block">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Estado</span>
                                <span class="px-3 py-1 bg-white border border-slate-100 rounded-full text-[9px] font-black uppercase text-indigo-500 shadow-sm"><?php echo strtoupper($lead['status'] ?? 'NUEVO'); ?></span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-lg font-black text-slate-800 italic tabular-nums"><?php echo number_format($lead['proposal_price'], 0, ',', '.'); ?>€</span>
                                <span class="text-[10px] font-black text-slate-400 uppercase opacity-50"><?php echo date('H:i', strtotime($lead['created_at'])); ?> h</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl text-slate-300 group-hover/row:bg-indigo-600 group-hover/row:text-white transition-all">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- ROI Tracker Card -->
            <div class="lg:col-span-4 bg-slate-900 p-12 rounded-[4rem] text-white shadow-3xl shadow-slate-200 relative overflow-hidden flex flex-col justify-between group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/30 via-transparent to-transparent opacity-60 group-hover:opacity-100 transition-all duration-700"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-14">
                        <div class="w-12 h-12 bg-indigo-500 rounded-[1.5rem] flex items-center justify-center text-white ring-8 ring-indigo-500/10 shadow-glow">
                            <i data-lucide="zap" class="w-6 h-6 fill-white"></i>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight italic uppercase">Eficacia <span class="text-indigo-400">Total</span></h3>
                    </div>

                    <div class="space-y-12">
                        <div class="space-y-5">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Tasa de Conversión</span>
                                <span class="text-3xl font-black italic tabular-nums text-indigo-300">24.8%</span>
                            </div>
                            <div class="w-full h-4 bg-slate-800 rounded-full overflow-hidden border border-slate-700/50 p-1 shadow-inner">
                                <div class="w-[24.8%] h-full bg-indigo-400 rounded-full shadow-[0_0_20px_rgba(129,140,248,0.5)] transition-all duration-1000"></div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Churn de Prospectos</span>
                                <span class="text-3xl font-black italic tabular-nums text-amber-300">4.2%</span>
                            </div>
                            <div class="w-full h-4 bg-slate-800 rounded-full overflow-hidden border border-slate-700/50 p-1 shadow-inner">
                                <div class="w-[10.2%] h-full bg-amber-400 rounded-full shadow-[0_0_20px_rgba(251,191,36,0.5)] transition-all duration-1000"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-16">
                    <button class="w-full py-6 bg-white text-slate-900 font-black rounded-3xl text-xs uppercase tracking-[0.3em] hover:scale-[1.03] transition-all shadow-2xl shadow-slate-900/50 active:scale-95">
                        Expandir Analíticas
                    </button>
                    <p class="text-center text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-6 opacity-60 italic">Algoritmo de predicción activo</p>
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
