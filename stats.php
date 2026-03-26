<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BI Dashboard Excel Style - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 10px; }
        
        .excel-table { @apply w-full border-collapse text-[11px] bg-white text-zinc-800 mb-8; }
        .header-top { @apply bg-white border border-zinc-300 font-bold text-center py-1 px-2; }
        .header-visitas { @apply bg-white border border-zinc-300 text-center font-bold py-1; }
        
        .header-sub { @apply bg-[#4472c4] text-white border border-white font-bold py-1 px-2 text-center; }
        .cell-data { @apply border border-zinc-200 py-1 px-2 text-center font-mono; }
        .cell-product { @apply border border-zinc-200 py-1 px-2 text-left text-blue-600 font-medium; }
        .cell-perc { @apply border border-zinc-200 py-1 px-2 text-center font-bold; }
        
        .bg-up { background: #c6efce; color: #006100; }
        .bg-down { background: #ffc7ce; color: #9c0006; }
        
        .row-total { @apply bg-zinc-200 font-black text-zinc-950 uppercase; }
    </style>
</head>
<body class="bg-white min-h-screen text-zinc-900 p-4">

    <header class="mb-6 flex items-center justify-between border-b pb-4">
        <h1 class="text-xl font-black uppercase italic tracking-tighter text-zinc-800">Analytics Master Report</h1>
        <button onclick="window.location.reload()" class="bg-zinc-100 p-2 rounded border hover:bg-zinc-200">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
        </button>
    </header>

    <main class="overflow-x-auto">
        <div class="flex flex-nowrap gap-8 min-w-[1600px]" id="dashboard-container">
            <!-- Table 1: Semana YoY -->
            <div class="w-[400px]">
                <table class="excel-table">
                    <thead>
                        <tr><th class="header-top border-b-0">Semana YoY</th><th colspan="3" class="header-visitas border-b-0 border-l-0">Visitas</th></tr>
                        <tr>
                            <th class="header-sub w-[160px]">Producto</th>
                            <th class="header-sub" id="label-w-yoy-p1">Semana Ant</th>
                            <th class="header-sub" id="label-w-yoy-p2">Semana Act</th>
                            <th class="header-sub">%</th>
                        </tr>
                    </thead>
                    <tbody id="body-w-yoy"></tbody>
                    <tfoot id="foot-w-yoy" class="row-total"></tfoot>
                </table>
            </div>

            <!-- Table 2: Semana WoW -->
            <div class="w-[400px]">
                <table class="excel-table">
                    <thead>
                        <tr><th class="header-top border-b-0">Semana WoW</th><th colspan="3" class="header-visitas border-b-0 border-l-0">Visitas</th></tr>
                        <tr>
                            <th class="header-sub w-[160px]">Producto</th>
                            <th class="header-sub" id="label-w-wow-p1">Semana Ant</th>
                            <th class="header-sub" id="label-w-wow-p2">Semana Act</th>
                            <th class="header-sub">%</th>
                        </tr>
                    </thead>
                    <tbody id="body-w-wow"></tbody>
                    <tfoot id="foot-w-wow" class="row-total"></tfoot>
                </table>
            </div>

            <!-- Table 3: Acumulado Mes actual -->
            <div class="w-[400px]">
                <table class="excel-table">
                    <thead>
                        <tr><th class="header-top border-b-0">Acumulado Mes Actual</th><th colspan="3" class="header-visitas border-b-0 border-l-0">Visitas</th></tr>
                        <tr>
                            <th class="header-sub w-[160px]">Producto</th>
                            <th class="header-sub" id="label-m-yoy-p1">MTD Año Ant</th>
                            <th class="header-sub" id="label-m-yoy-p2">MTD Act</th>
                            <th class="header-sub">%</th>
                        </tr>
                    </thead>
                    <tbody id="body-m-yoy"></tbody>
                    <tfoot id="foot-m-yoy" class="row-total"></tfoot>
                </table>
            </div>

            <!-- Table 4: Acumulado Anual YoY -->
            <div class="w-[400px]">
                <table class="excel-table">
                    <thead>
                        <tr><th class="header-top border-b-0">Acumulado Anual YoY</th><th colspan="3" class="header-visitas border-b-0 border-l-0">Visitas</th></tr>
                        <tr>
                            <th class="header-sub w-[160px]">Producto</th>
                            <th class="header-sub" id="label-y-yoy-p1">YTD Año Ant</th>
                            <th class="header-sub" id="label-y-yoy-p2">YTD Act</th>
                            <th class="header-sub">%</th>
                        </tr>
                    </thead>
                    <tbody id="body-y-yoy"></tbody>
                    <tfoot id="foot-y-yoy" class="row-total"></tfoot>
                </table>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        async function loadAnalytics() {
            try {
                const response = await fetch('api_ga_stats.php');
                const result = await response.json();
                if (result.status === 'success') renderTables(result.data);
            } catch (error) { console.error('Error:', error); }
        }

        function createRow(product, curr, prev, perc) {
            const percClass = perc >= 0 ? 'bg-up' : 'bg-down';
            return `
                <tr>
                    <td class="cell-product underline">\${product}</td>
                    <td class="cell-data">\${prev.toLocaleString()}</td>
                    <td class="cell-data">\${curr.toLocaleString()}</td>
                    <td class="cell-perc \${percClass}">\${perc >= 0 ? '+' : ''}\${perc}%</td>
                </tr>
            `;
        }

        function createTotalRow(currTotal, prevTotal) {
            const percTotal = prevTotal > 0 ? round((currTotal - prevTotal) / prevTotal * 100, 1) : 0;
            const percClass = percTotal >= 0 ? 'bg-up' : 'bg-down';
            return `
                <tr>
                    <td class="cell-product uppercase font-black">TOTAL</td>
                    <td class="cell-data font-black">\${prevTotal.toLocaleString()}</td>
                    <td class="cell-data font-black">\${currTotal.toLocaleString()}</td>
                    <td class="cell-perc font-black \${percClass}">\${percTotal >= 0 ? '+' : ''}\${percTotal}%</td>
                </tr>
            `;
        }

        const round = (num, dec) => Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);

        function renderTables(data) {
            const keys = ['semana_yoy', 'semana_wow', 'mes_yoy', 'anual_yoy'];
            
            keys.forEach(key => {
                const body = document.getElementById(`body-${key.replace(/_/g, '-')}`);
                const foot = document.getElementById(`foot-${key.replace(/_/g, '-')}`);
                let bodyHtml = '';
                let totalCurr = 0, totalPrev = 0;

                data.forEach(item => {
                    const d = item[key];
                    bodyHtml += createRow(item.product, d.curr, d.prev, d.perc);
                    totalCurr += d.curr;
                    totalPrev += d.prev;
                });

                body.innerHTML = bodyHtml;
                foot.innerHTML = createTotalRow(totalCurr, totalPrev);
            });
        }

        loadAnalytics();
    </script>
</body>
</html>
