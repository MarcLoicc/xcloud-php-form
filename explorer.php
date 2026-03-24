<?php
require_once 'auth.php'; // Protección de seguridad del CRM

$dir = 'uploads/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$files = array_diff(scandir($dir), array('.', '..', 'index.php', '.htaccess'));
natsort($files); // Orden natural
$files = array_reverse($files); // Recientes primero
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Archivos - CRM Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans">
    <?php include 'sidebar.php'; ?>
    <main class="sm:ml-64 p-8 min-h-screen">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 pb-6 border-b border-slate-200">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Archivo <span class="text-indigo-600">Maestro</span></h1>
                <p class="text-slate-500 text-sm mt-1">Gestión de grabaciones y documentos adjuntos.</p>
            </div>
            <div class="px-5 py-2 bg-white border border-slate-200 rounded-xl text-[11px] font-black text-slate-400 uppercase tracking-widest shadow-sm">
                <span id="fileCount" class="text-indigo-600"><?php echo count($files); ?></span> Archivos Registrados
            </div>
        </header>

        <!-- Filtros -->
        <div class="bg-white border border-slate-200 p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6 mb-8 shadow-sm">
            <div class="flex items-center gap-3 overflow-x-auto w-full md:w-auto">
                <button onclick="filterType('all', this)" class="filter-btn active px-5 py-2.5 bg-indigo-600 text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-indigo-100 uppercase">Todos</button>
                <button onclick="filterType('audio', this)" class="filter-btn px-5 py-2.5 bg-slate-50 text-slate-500 border border-slate-200 hover:bg-slate-100 text-xs font-black rounded-xl transition-all uppercase">Audios</button>
                <button onclick="filterType('doc', this)" class="filter-btn px-5 py-2.5 bg-slate-50 text-slate-500 border border-slate-200 hover:bg-slate-100 text-xs font-black rounded-xl transition-all uppercase">Documentos</button>
            </div>

            <div class="relative w-full md:w-96 group">
                <i data-lucide="search" class="w-4 h-4 absolute left-4 top-3.5 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                <input type="text" id="searchInput" placeholder="Filtrar por nombre o tipo..." 
                       class="w-full pl-11 pr-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-600 text-slate-900 text-sm outline-none transition-all placeholder-slate-300">
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                            <th class="px-8 py-5">Archivo / Lead Asociado</th>
                            <th class="px-6 py-5 text-center">Fecha</th>
                            <th class="px-6 py-5">Nombre de Sistema</th>
                            <th class="px-6 py-5 text-right">Tamaño</th>
                            <th class="px-8 py-5 text-right font-bold">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="fileTableBody" class="divide-y divide-slate-100">
                        <?php foreach($files as $file): 
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $isAudio = ($ext == 'webm' || $ext == 'ogg' || $ext == 'mp3' || $ext == 'wav');
                            $size = round(filesize($dir . $file) / 1024 / 1024, 2);
                            
                            $parts = explode('_', pathinfo($file, PATHINFO_FILENAME)); 
                            $leadName = $parts[1] ?? 'Sin asignar';
                            $leadNameFormatted = str_replace('-', ' ', $leadName);
                            
                            $dateStr = $parts[2] ?? 'N/A';
                            $timeStr = $parts[3] ?? '';
                            if (strlen($timeStr) == 4) {
                                $timeStr = substr($timeStr, 0, 2) . ':' . substr($timeStr, 2, 2);
                            }
                            
                            $typeClass = $isAudio ? 'audio' : 'doc';
                            $searchString = strtolower($file . ' ' . $leadNameFormatted);
                        ?>
                        <tr class="file-row hover:bg-slate-50/80 transition-all group" data-type="<?php echo $typeClass; ?>" data-search="<?php echo htmlspecialchars($searchString); ?>">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 flex items-center justify-center rounded-xl <?php echo $isAudio ? 'bg-red-50 text-red-500 border-red-100' : 'bg-indigo-50 text-indigo-600 border-indigo-100'; ?> border">
                                        <i data-lucide="<?php echo $isAudio ? 'mic' : 'file-text'; ?>" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($leadNameFormatted); ?></p>
                                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest"><?php echo $isAudio ? 'Archivo de Audio' : 'Documento'; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="text-slate-900 font-bold block text-xs tabular-nums"><?php echo htmlspecialchars($dateStr); ?></span>
                                <?php if($timeStr): ?>
                                    <span class="text-[10px] font-medium text-slate-400"><?php echo htmlspecialchars($timeStr); ?> h</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-[11px] font-mono text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 truncate max-w-[200px] block" title="<?php echo htmlspecialchars($file); ?>">
                                    <?php echo htmlspecialchars($file); ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right text-xs font-bold text-slate-400 tabular-nums">
                                <?php echo $size; ?> MB
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="download?file=<?php echo urlencode($dir . $file); ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 text-[10px] font-black uppercase hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                    <i data-lucide="<?php echo $isAudio ? 'headphones' : 'external-link'; ?>" class="w-4 h-4"></i>
                                    <?php echo $isAudio ? 'Oír' : 'Ver'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div id="noResults" class="hidden py-24 text-center">
                    <i data-lucide="search-x" class="w-16 h-16 mx-auto mb-6 text-slate-200"></i>
                    <h3 class="font-black text-sm tracking-widest uppercase text-slate-400">Sin resultados para esta búsqueda</h3>
                </div>

                <?php if (empty($files)): ?>
                <div class="py-24 text-center">
                    <i data-lucide="folder-open" class="w-16 h-16 mx-auto mb-6 text-slate-200"></i>
                    <h3 class="font-black text-sm tracking-widest uppercase text-slate-400">No hay archivos alojados aún</h3>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        let currentType = 'all';
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.file-row');
        const noResults = document.getElementById('noResults');
        const fileCountSpan = document.getElementById('fileCount');

        function filterType(type, buttonElement) {
            currentType = type;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
                btn.classList.add('bg-slate-50', 'text-slate-500', 'border-slate-200');
            });
            buttonElement.classList.remove('bg-slate-50', 'text-slate-500', 'border-slate-200');
            buttonElement.classList.add('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            applyFilters();
        }

        searchInput.addEventListener('input', applyFilters);

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            let visibleCount = 0;
            rows.forEach(row => {
                const type = row.getAttribute('data-type');
                const searchData = row.getAttribute('data-search');
                const matchesType = (currentType === 'all' || currentType === type);
                const matchesSearch = searchData.includes(searchTerm);
                if (matchesType && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            fileCountSpan.textContent = visibleCount;
            noResults.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }
    </script>
</body>
</html>
