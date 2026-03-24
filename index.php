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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Acme SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12" id="main-content">
        <!-- Dashboard Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Overview</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Real-time business metrics and recent activity.</p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="location.reload()" aria-label="Refresh Dashboard" class="p-2.5 bg-zinc-900 border border-zinc-800 rounded-md text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                    <i data-lucide="refresh-cw" class="w-4 h-4" aria-hidden="true"></i>
                </button>
            </div>
        </header>

        <!-- Stats Section -->
        <section aria-labelledby="metrics-heading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <h2 id="metrics-heading" class="sr-only">Key Performance Indicators</h2>
            
            <article class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[14px] font-semibold text-zinc-400">Total Customers</h3>
                    <i data-lucide="users" class="w-4 h-4 text-zinc-500" aria-hidden="true"></i>
                </div>
                <p class="text-3xl font-bold text-zinc-100" aria-label="<?php echo $totalLeads; ?> Total Customers"><?php echo number_format($totalLeads); ?></p>
            </article>

            <article class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[14px] font-semibold text-zinc-400">Projected Revenue</h3>
                    <i data-lucide="dollar-sign" class="w-4 h-4 text-zinc-500" aria-hidden="true"></i>
                </div>
                <p class="text-3xl font-bold text-zinc-100" aria-label="<?php echo number_format($revenue, 2, ',', '.'); ?> Euros Projected Revenue">€<?php echo number_format($revenue, 2, '.', ','); ?></p>
            </article>

            <article class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[14px] font-semibold text-zinc-400">Paid Acquisition</h3>
                    <i data-lucide="target" class="w-4 h-4 text-zinc-500" aria-hidden="true"></i>
                </div>
                <p class="text-3xl font-bold text-zinc-100" aria-label="<?php echo $sources['pago'] ?? 0; ?> leads from paid channels"><?php echo $sources['pago'] ?? 0; ?></p>
            </article>

            <article class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[14px] font-semibold text-zinc-400">Organic Acquisition</h3>
                    <i data-lucide="globe" class="w-4 h-4 text-zinc-500" aria-hidden="true"></i>
                </div>
                <p class="text-3xl font-bold text-zinc-100" aria-label="<?php echo $sources['organico'] ?? 0; ?> leads from organic channels"><?php echo $sources['organico'] ?? 0; ?></p>
            </article>
        </section>

        <!-- Activity Section -->
        <section aria-labelledby="activity-heading" class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-zinc-800">
                <h2 id="activity-heading" class="text-[16px] font-bold text-zinc-100">Recent Activity</h2>
                <a href="leads.php" class="text-[14px] font-medium text-zinc-400 hover:text-zinc-100 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 rounded-sm px-1">View all</a>
            </div>

            <div class="divide-y divide-zinc-800">
                <?php while($lead = $recentLeads->fetch_assoc()): ?>
                <a href="leads.php" aria-label="View details for <?php echo htmlspecialchars($lead['name']); ?>" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 sm:p-6 hover:bg-zinc-800/50 transition-colors focus-visible:bg-zinc-800/50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-indigo-500 group">
                    <div class="flex items-center gap-4 mb-4 sm:mb-0">
                        <div class="w-10 h-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-300 font-bold text-[14px] shrink-0" aria-hidden="true">
                            <?php echo substr($lead['name'], 0, 1); ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[15px] font-bold text-zinc-100"><?php echo htmlspecialchars($lead['name']); ?></span>
                            <span class="text-[14px] text-zinc-500"><?php echo htmlspecialchars($lead['company'] ?: 'No Company'); ?></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 sm:gap-8 justify-between sm:justify-end w-full sm:w-auto">
                        <div class="flex flex-col sm:items-end">
                            <span class="text-[14px] font-bold text-zinc-200" aria-label="Amount: <?php echo number_format($lead['proposal_price'] ?? 0, 0, ',', '.'); ?> Euros">€<?php echo number_format($lead['proposal_price'] ?? 0, 0, '.', ','); ?></span>
                            <span class="text-[13px] text-zinc-500 capitalize"><?php echo str_replace('_', ' ', $lead['status'] ?? 'nuevo'); ?></span>
                        </div>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-zinc-600 group-hover:text-zinc-300 transition-colors shrink-0" aria-hidden="true"></i>
                    </div>
                </a>
                <?php endwhile; ?>
                <?php if ($recentLeads->num_rows === 0): ?>
                    <div class="p-8 text-center">
                        <p class="text-[14px] text-zinc-500">No recent activity found.</p>
                    </div>
                <?php endif; ?>
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
