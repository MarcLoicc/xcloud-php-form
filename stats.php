<?php require_once 'auth.php'; ?>
<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BI Dashboard - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap');
        :root { font-family: 'Montserrat', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #09090b; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; border: 2px solid #09090b; }
        
        .excel-table { border-collapse: separate; border-spacing: 0; font-size: 12px; }
        .excel-table th, .excel-table td { border-bottom: 1px solid #27272a; border-right: 1px dotted #27272a; padding: 12px 16px; }
        .header-top { font-weight: 900; font-size: 13px; letter-spacing: 0.1em; }
        .header-sub { background: #4f46e5; color: #fff; font-weight: 900; text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #312e81 !important; }
        .cell-prod { color: #a5b4fc; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.02em; }
        .cell-val { font-family: ui-monospace, monospace; text-align: center; font-size: 13px; }
        .perc-up { background: rgba(16,185,129,0.15); color: #34d399; font-weight: 900; }
        .perc-down { background: rgba(244,63,94,0.1); color: #fb7185; font-weight: 900; }
        .row-total td { background: #18181b; font-weight: 900; color: #fff; border-top: 2px solid #4f46e5 !important; font-size: 14px; }

        /* M-TREND */
        .tt { border-collapse: separate; border-spacing: 0; font-size: 12px; width:100%; }
        .tt th, .tt td { border-bottom: 1px solid #27272a; padding: 10px 12px; white-space: nowrap; }
        .tt tbody tr:hover td { background: rgba(63,63,70,0.2); }
        .tt-mes { background: #09090b; color: #a5b4fc; font-weight: 900; min-width: 150px; text-transform: uppercase; border-right: 2px solid #312e81 !important; padding: 10px 20px; }
        .tt-grp { background: #1e1b4b; color: #c7d2fe; font-weight:900; font-size:10px; text-align:center; border-right:1px solid #312e81; }
        .tt-sub { background: #4f46e5; color:#fff; font-weight:900; font-size:10px; border-bottom:2px solid #312e81 !important; border-right:1px solid rgba(255,255,255,0.1); text-align:center; }
        .tt-v { background:#3730a3; color:#fff; font-weight:900; font-size:10px; border-bottom:2px solid #312e81 !important; border-right:2px solid #312e81; text-align:center; }
        .tt-prev { color:#71717a; text-align:center; font-family:monospace; }
        .tt-curr { color:#f4f4f5; text-align:center; font-family:monospace; font-weight:700; background:rgba(63,63,70,0.05); }
        .tt-up { font-weight:900; color:#34d399; background:rgba(16,185,129,0.1); border-right:2px solid #312e81; text-align:center; }
        .tt-down { font-weight:900; color:#fb7185; background:rgba(244,63,94,0.08); border-right:2px solid #312e81; text-align:center; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased flex items-start">
    <?php include 'sidebar.php'; ?>
    <main class="sm:ml-64 flex-1 min-h-screen pt-8 px-4 lg:px-8 pb-20 flex flex-col max-w-[100vw]">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-zinc-900 mb-6 shrink-0">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center border border-indigo-500/30">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <h1 class="text-3xl font-black text-white italic uppercase">Analytics Dashboard</h1>
                </div>
            </div>
            <button onclick="loadAnalytics(true)" class="bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 px-5 rounded-xl border border-indigo-400/50 flex items-center gap-3 group">
                <i id="btn-icon" data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span class="text-xs font-bold uppercase tracking-widest">Sincronizar API</span>
            </button>
        </header>

        <div id="init-loader" class="flex flex-col items-center justify-center p-24 text-zinc-500 mt-10">
            <i data-lucide="loader-2" class="w-12 h-12 animate-spin mb-4 text-indigo-500"></i>
            <p>Cargando datos históricos y API...</p>
        </div>

        <div class="overflow-x-auto custom-scrollbar flex-1 pb-6 w-full" style="display:none;" id="master-container">
            <div class="bg-zinc-950 rounded-2xl border-2 border-zinc-800/80 shadow-2xl overflow-hidden inline-block min-w-max">
                <table class="excel-table w-full whitespace-nowrap m-0">
                    <thead>
                        <tr>
                            <th class="border-none bg-zinc-950 sticky left-0 z-20 border-r border-indigo-500/30"></th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📅 SEMANA YoY</th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">🔄 SEMANA WoW</th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📅 MES ACUM. (MTD)</th>
                            <th colspan="3" class="header-top text-center bg-[#1e1b4b] text-indigo-200 border-x border-[#312e81]">📈 ANUAL ACUM. (YTD)</th>
                        </tr>
                        <tr>
                            <th class="header-sub text-left pl-5">Producto Base</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-yoy-prev">-</th><th class="header-sub w-24" id="head-yoy-curr">-</th><th class="header-sub w-20 bg-indigo-600">Var %</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-wow-prev">-</th><th class="header-sub w-24" id="head-wow-curr">-</th><th class="header-sub w-20 bg-indigo-600">Var %</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-mtd-prev">-</th><th class="header-sub w-24" id="head-mtd-curr">-</th><th class="header-sub w-20 bg-indigo-600">Var %</th>
                            <th class="header-sub w-24 border-l border-white/10" id="head-ytd-prev">-</th><th class="header-sub w-24" id="head-ytd-curr">-</th><th class="header-sub w-20 bg-indigo-600">Var %</th>
                        </tr>
                    </thead>
                    <tbody id="body-target"></tbody>
                    <tfoot class="row-total">
                        <tr>
                            <td class="sticky left-0 bg-[#18181b] z-10 text-left px-5 border-r border-indigo-500/50">RESUMEN GLOBAL</td>
                            <td colspan="3" class="cell-val" id="total-w_yoy">...</td>
                            <td colspan="3" class="cell-val" id="total-w_wow">...</td>
                            <td colspan="3" class="cell-val" id="total-m_yoy">...</td>
                            <td colspan="3" class="cell-val" id="total-y_yoy">...</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-12 mb-20 w-full" style="display:none;" id="trend-container">
            <h2 class="text-xl font-bold text-white uppercase mb-6 flex items-center gap-3">
                <i data-lucide="calendar" class="w-5 h-5 text-indigo-400"></i> Tendencia Mensual (2025 vs 2026)
            </h2>
            <div class="overflow-x-auto custom-scrollbar shadow-2xl rounded-2xl border-2 border-zinc-800">
                <table class="tt">
                    <thead>
                        <tr>
                            <th class="tt-mes" rowspan="2">MES</th>
                            <th class="tt-grp" colspan="3">📈 TOTAL VISITAS</th>
                            <th class="tt-grp" colspan="3">💻 WEB (DESKTOP)</th>
                            <th class="tt-grp" colspan="3">📱 MÓVIL / TAB</th>
                            <th class="tt-grp" colspan="3">⏳ RETENCIÓN</th>
                        </tr>
                        <tr>
                            <th class="tt-sub">2025</th><th class="tt-sub">2026</th><th class="tt-v">VAR %</th>
                            <th class="tt-sub">2025</th><th class="tt-sub">2026</th><th class="tt-v">VAR %</th>
                            <th class="tt-sub">2025</th><th class="tt-sub">2026</th><th class="tt-v">VAR %</th>
                            <th class="tt-sub">2025</th><th class="tt-sub">2026</th><th class="tt-v">VAR %</th>
                        </tr>
                    </thead>
                    <tbody id="trend-body"></tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        const date = new Date(); const year = 2026; const lastYear = 2025;
        function getIso(d) {
            const t = new Date(d.valueOf()); const n = (t.getDay()+6)%7; t.setDate(t.getDate()-n+3);
            const th = t.valueOf(); t.setMonth(0,1); if(t.getDay()!==4) t.setMonth(0,1+((4-t.getDay())+7)%7);
            return 1+Math.ceil((th-t)/604800000);
        }
        function formatH(sOff, eOff, fY=null) {
            const e = new Date(); e.setDate(e.getDate()-eOff); const s = new Date(); s.setDate(s.getDate()-sOff);
            const w = getIso(e); const m = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"][e.getMonth()];
            return `Sem ${w}<br><span class='text-[10px] text-zinc-500'>(${s.getDate()}-${e.getDate()} ${m}) ${fY||e.getFullYear()}</span>`;
        }
        document.getElementById('head-yoy-curr').innerHTML=formatH(7,0,2026); document.getElementById('head-yoy-prev').innerHTML=formatH(7,0,2025);
        document.getElementById('head-wow-curr').innerHTML=formatH(7,0,2026); document.getElementById('head-wow-prev').innerHTML=formatH(14,7,2026);
        const mNames=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
        const cM=mNames[date.getMonth()].toUpperCase();
        document.getElementById('head-mtd-prev').innerHTML=`MTD ${cM}<br><span class='text-[10px] text-zinc-500'>2025</span>`;
        document.getElementById('head-mtd-curr').innerHTML=`MTD ${cM}<br><span class='text-[10px] text-zinc-500'>2026</span>`;
        document.getElementById('head-ytd-prev').innerHTML=`YTD ANUAL<br><span class='text-[10px] text-zinc-500'>2025</span>`;
        document.getElementById('head-ytd-curr').innerHTML=`YTD ANUAL<br><span class='text-[10px] text-zinc-500'>2026</span>`;

        function vCls(r) { return r>0?'tt-up':(r<0?'tt-down':'tt-prev'); }

        async function loadAnalytics(isRef=false) {
            try {
                if(isRef) document.getElementById('btn-icon').classList.add('animate-spin');
                const iJSON = await (await fetch('api_ga_stats.php?report=init')).json();
                if(iJSON.status!=='success') throw new Error(iJSON.message);
                let bHtml=''; let tot={ w_yoy:0, m_yoy:0, y_yoy:0 };
                Object.keys(iJSON.data).forEach(p => {
                    const id=btoa(p).replace(/=/g,''); const prod=iJSON.data[p]; const f=prod.fixed||{w_yoy:0,m_yoy:0,y_yoy:0};
                    tot.w_yoy+=(f.w_yoy||0); tot.m_yoy+=(f.m_yoy||0); tot.y_yoy+=(f.y_yoy||0);
                    bHtml+=`<tr class='hover:bg-zinc-900 group'><td class='cell-prod sticky left-0 bg-zinc-950 group-hover:bg-zinc-900 z-10 border-r border-indigo-500/30'>${prod.name}</td>
                        <td class='cell-val text-zinc-500'>${(f.w_yoy||0).toLocaleString()}</td><td class='cell-val font-bold text-white bg-zinc-800/20' id='curr-w_yoy-${id}'>...</td><td class='cell-val' id='perc-w_yoy-${id}'>-</td>
                        <td colspan='3' class='cell-val text-zinc-600' id='w_wow-${id}'>...</td>
                        <td class='cell-val text-zinc-500'>${(f.m_yoy||0).toLocaleString()}</td><td class='cell-val font-bold text-white bg-zinc-800/20' id='curr-m_yoy-${id}'>...</td><td class='cell-val' id='perc-m_yoy-${id}'>-</td>
                        <td class='cell-val text-zinc-500'>${(f.y_yoy||0).toLocaleString()}</td><td class='cell-val font-bold text-white bg-zinc-800/20' id='curr-y_yoy-${id}'>...</td><td class='cell-val' id='perc-y_yoy-${id}'>-</td></tr>`;
                });
                document.getElementById('body-target').innerHTML=bHtml;
                document.getElementById('init-loader').style.display='none'; document.getElementById('master-container').style.display='block';
                lucide.createIcons();
                ['w_yoy','w_wow','m_yoy','y_yoy'].forEach(q => fetchAndR(q, isRef)); fetchTrend(isRef);
            } catch(e) { console.error(e); }
        }

        async function fetchAndR(rep, ref) {
            const dataJSON = await (await fetch(`api_ga_stats.php?report=${rep}${ref?'&refresh=true':''}`)).json();
            if(dataJSON.status==='success') {
                const data=dataJSON.data; let tC=0, tP=0;
                for(const path in data) {
                    const d=data[path]; const id=btoa(path).replace(/=/g,'');
                    if(rep==='w_wow') {
                        const td=document.getElementById(`w_wow-${id}`);
                        if(td) td.outerHTML=`<td class='cell-val text-zinc-500'>${d.prev.toLocaleString()}</td><td class='cell-val font-bold text-white bg-zinc-800/20'>${d.curr.toLocaleString()}</td><td class='${d.raw_perc>=0?'perc-up':'perc-down'}'>${d.perc}</td>`;
                    } else {
                        const cTd=document.getElementById(`curr-${rep}-${id}`), pTd=document.getElementById(`perc-${rep}-${id}`);
                        if(cTd) cTd.innerText=d.curr.toLocaleString(); if(pTd) { pTd.innerText=d.perc; pTd.className=`cell-val ${d.raw_perc>=0?'perc-up':'perc-down'}`; }
                    }
                    tC+=d.curr; tP+=d.prev;
                }
                const tp=(tP>0)?(Math.round((tC-tP)/tP*10000)/100):0;
                const sign=(tp>0?'+':'');
                document.getElementById(`total-${rep}`).outerHTML=`<td class='cell-val text-zinc-500'>${tP.toLocaleString()}</td><td class='cell-val font-bold text-white bg-zinc-800/20'>${tC.toLocaleString()}</td><td class='cell-val ${tp>=0?'perc-up':'perc-down'}'>${sign}${tp}%</td>`;
            }
            if(rep==='y_yoy' && document.getElementById('btn-icon')) document.getElementById('btn-icon').classList.remove('animate-spin');
        }

        async function fetchTrend(ref) {
            const res = await (await fetch(`api_ga_stats.php?report=monthly_trend${ref?'&refresh=true':''}`)).json();
            if(res.status==='success') {
                let html='';
                res.data.forEach(m => {
                    html+=`<tr><td class='tt-td-mes tt-mes'>${m.month_name}</td>
                        <td class='tt-prev'>${m.prev.toLocaleString()}</td><td class='tt-curr'>${m.curr.toLocaleString()}</td><td class='${vCls(m.raw_perc)}'>${m.perc}</td>
                        <td class='tt-prev'>${(m.prev_web||0).toLocaleString()}</td><td class='tt-curr'>${(m.curr_web||0).toLocaleString()}</td><td class='${vCls(m.raw_perc_web)}'>${m.perc_web}</td>
                        <td class='tt-prev'>${(m.prev_mob||0).toLocaleString()}</td><td class='tt-curr'>${(m.curr_mob||0).toLocaleString()}</td><td class='${vCls(m.raw_perc_mob)}'>${m.perc_mob}</td>
                        <td class='tt-prev'>${Math.round(m.prev_ret||0)}s</td><td class='tt-curr'>${Math.round(m.curr_ret||0)}s</td><td class='${vCls(m.raw_perc_ret)}'>${m.perc_ret}</td></tr>`;
                });
                document.getElementById('trend-body').innerHTML=html; document.getElementById('trend-container').style.display='block'; lucide.createIcons();
            }
        }
        loadAnalytics();
    </script>
</body>
</html>