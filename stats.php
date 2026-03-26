<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Excel GA4 - CRM Marcloi</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: Calibri, Arial, sans-serif; background-color: #fff; margin: 20px; color: #000; }
        .report-title { font-size: 20px; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; font-style: italic; }
        
        .dashboard-wrapper { display: flex; flex-wrap: nowrap; gap: 40px; overflow-x: auto; padding-bottom: 20px; }
        
        table { border-collapse: collapse; width: 450px; margin-bottom: 20px; font-size: 13px; table-layout: fixed; }
        th, td { border: 1px solid #999; padding: 4px 8px; text-align: center; }
        
        /* Headers Style */
        .head-main { background-color: #fff; font-weight: bold; height: 30px; }
        .head-sub { background-color: #4472c4; color: white; border-color: white; font-weight: bold; height: 25px; }
        
        /* Cells Style */
        .cell-prod { text-align: left; color: #0000ee; text-decoration: underline; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .cell-val { font-family: "Courier New", Courier, monospace; }
        
        /* Percentages */
        .perc-up { background-color: #c6efce; color: #006100; font-weight: bold; }
        .perc-down { background-color: #ffc7ce; color: #9c0006; font-weight: bold; }
        
        /* Totals */
        .row-total { background-color: #d9d9d9; font-weight: bold; }
        
        .loading-overlay { padding: 40px; text-align: center; font-style: italic; color: #666; }
    </style>
</head>
<body>

    <div class="report-title uppercase">Analytics Command Center - Master Excel View</div>

    <div class="dashboard-wrapper" id="master-container">
        <div class="loading-overlay" id="loader">Cargando datos de GA4...</div>
    </div>

    <script>
        // Etiquetas dinámicas según la fecha actual
        const date = new Date();
        const year = date.getFullYear();
        const lastYear = year - 1;
        
        async function loadAnalytics() {
            try {
                const response = await fetch('api_ga_stats.php');
                const result = await response.json();
                if (result.status === 'success') {
                    document.getElementById('loader').style.display = 'none';
                    renderAll(result.data);
                }
            } catch (e) {
                document.getElementById('loader').innerText = "Error: No se pudieron cargar los datos.";
            }
        }

        function createTableHtml(title, p1Label, p2Label, dataKey, data) {
            let bodyHtml = '';
            let tCurr = 0, tPrev = 0;

            data.forEach(item => {
                const d = item[dataKey];
                const pClass = d.perc >= 0 ? 'perc-up' : 'perc-down';
                tCurr += d.curr; tPrev += d.prev;
                
                bodyHtml += `
                    <tr>
                        <td class="cell-prod">\${item.product}</td>
                        <td class="cell-val">\${d.prev.toLocaleString()}</td>
                        <td class="cell-val">\${d.curr.toLocaleString()}</td>
                        <td class="cell-val \${pClass}">\${d.perc >= 0 ? '+' : ''}\${d.perc}%</td>
                    </tr>
                `;
            });

            const totalPerc = tPrev > 0 ? Math.round(((tCurr - tPrev) / tPrev) * 100 * 10) / 10 : 0;
            const tPClass = totalPerc >= 0 ? 'perc-up' : 'perc-down';

            return `
                <div>
                    <table>
                        <thead>
                            <tr class="head-main">
                                <th style="border-bottom:none; border-right:none; text-align:left;">\${title}</th>
                                <th colspan="3" style="border-bottom:none; border-left:none; background:#fff;">Visitas</th>
                            </tr>
                            <tr class="head-sub">
                                <th style="width:180px">Producto</th>
                                <th style="width:80px">\${p1Label}</th>
                                <th style="width:80px">\${p2Label}</th>
                                <th style="width:70px">%</th>
                            </tr>
                        </thead>
                        <tbody>\${bodyHtml}</tbody>
                        <tfoot class="row-total">
                            <tr>
                                <td style="text-align:left">TOTAL</td>
                                <td class="cell-val">\${tPrev.toLocaleString()}</td>
                                <td class="cell-val">\${tCurr.toLocaleString()}</td>
                                <td class="cell-val \${tPClass}">\${totalPerc >= 0 ? '+' : ''}\${totalPerc}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
        }

        function renderAll(data) {
            const container = document.getElementById('master-container');
            
            // Replicamos los 4 bloques
            const h1 = createTableHtml('Semana YoY', 'Seman Ant Año', 'Semana Actual', 'semana_yoy', data);
            const h2 = createTableHtml('Semana WoW', 'Semana Anterior', 'Semana Actual', 'semana_wow', data);
            const h3 = createTableHtml('Acumulado Mes Actual', 'MTD ' + lastYear, 'MTD ' + year, 'mes_yoy', data);
            const h4 = createTableHtml('Acumulado Anual YoY', 'YTD ' + lastYear, 'YTD ' + year, 'anual_yoy', data);
            
            container.innerHTML = h1 + h2 + h3 + h4;
        }

        loadAnalytics();
    </script>
</body>
</html>
