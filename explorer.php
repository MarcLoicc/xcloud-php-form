<?php require_once 'auth.php'; ?>
<?php
date_default_timezone_set('Europe/Madrid');
$dir = "uploads/";
if (!is_dir($dir)) mkdir($dir, 0777, true);
$files = array_diff(scandir($dir), array('.', '..'));
$files = array_reverse($files); // Recientes primero
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>
    
    <main class="sm:ml-64 min-h-screen p-8 lg:p-12" id="main-content">
        <!-- Explorer Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Documentos y Archivos</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Gestiona grabaciones de llamadas y documentos adjuntos al sistema.</p>
            </div>
            <div class="bg-zinc-900 border border-zinc-800 p-3 rounded-md flex items-center gap-4">
                <i data-lucide="hard-drive" class="w-5 h-5 text-zinc-500" aria-hidden="true"></i>
                <div>
                    <span class="block text-[12px] text-zinc-400 font-medium">Archivos Totales</span>
                    <span class="block text-[14px] font-bold text-zinc-100"><span id="fileCount"><?php echo count($files); ?></span> archivos</span>
                </div>
            </div>
        </header>

        <!-- Filters Section -->
        <section aria-labelledby="filters-heading" class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-zinc-900 border border-zinc-800 p-4 rounded-xl">
            <h2 id="filters-heading" class="sr-only">Filtros de Archivos</h2>
            
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto" role="group" aria-label="Filtrar por tipo de archivo">
                <button type="button" aria-pressed="true" onclick="filterType('all', this)" class="filter-btn px-4 py-2 bg-zinc-100 text-zinc-950 text-[13px] font-bold rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">Todos</button>
                <button type="button" aria-pressed="false" onclick="filterType('audio', this)" class="filter-btn px-4 py-2 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 text-[13px] font-medium rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 border border-zinc-700 hover:border-zinc-600">Audios</button>
                <button type="button" aria-pressed="false" onclick="filterType('doc', this)" class="filter-btn px-4 py-2 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 text-[13px] font-medium rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 border border-zinc-700 hover:border-zinc-600">Documentos</button>
            </div>

            <div class="relative w-full md:w-80">
                <label for="searchInput" class="sr-only">Buscar archivos</label>
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                <input type="text" id="searchInput" placeholder="Buscar por nombre..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-[14px] text-zinc-100 placeholder:text-zinc-600 outline-none transition-colors">
            </div>
        </section>

        <!-- File List / Table -->
        <section aria-labelledby="file-list-heading" class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <h2 id="file-list-heading" class="sr-only">Lista de Archivos</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-950/50 border-b border-zinc-800">
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Archivo / Contexto</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-center">Fecha</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Nombre del Sistema</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Tamaño</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="fileTableBody" class="divide-y divide-zinc-800">
                        <?php foreach($files as $file): 
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $isAudio = ($ext == 'webm' || $ext == 'ogg' || $ext == 'mp3' || $ext == 'wav');
                            $size = round(filesize($dir . $file) / 1024 / 1024, 2);
                            
                            $parts = explode('_', pathinfo($file, PATHINFO_FILENAME)); 
                            $leadName = $parts[1] ?? 'Sin asignar';
                            $leadNameFormatted = str_replace('-', ' ', $leadName);
                            
                            $dateStr = $parts[2] ?? 'N/D';
                            $timeStr = $parts[3] ?? '';
                            if (strlen($timeStr) == 4) {
                                $timeStr = substr($timeStr, 0, 2) . ':' . substr($timeStr, 2, 2);
                            }
                            
                            $typeClass = $isAudio ? 'audio' : 'doc';
                            $searchString = strtolower($file . ' ' . $leadNameFormatted);
                        ?>
                        <tr class="file-row hover:bg-zinc-800/30 transition-colors group" data-type="<?php echo $typeClass; ?>" data-search="<?php echo htmlspecialchars($searchString); ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-md border text-zinc-300 <?php echo $isAudio ? 'bg-indigo-900/30 border-indigo-500/30 text-indigo-400' : 'bg-slate-800 border-slate-700 text-slate-300'; ?>" aria-hidden="true">
                                        <i data-lucide="<?php echo $isAudio ? 'mic' : 'file-text'; ?>" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-[14px] font-bold text-zinc-100"><?php echo htmlspecialchars($leadNameFormatted); ?></p>
                                        <p class="text-[12px] text-zinc-500 font-medium"><?php echo $isAudio ? 'Grabación de Audio' : 'Documento'; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-zinc-200 font-semibold block text-[13px]"><?php echo htmlspecialchars($dateStr); ?></span>
                                <?php if($timeStr): ?>
                                    <span class="text-[12px] text-zinc-500"><?php echo htmlspecialchars($timeStr); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block text-[13px] font-mono text-zinc-400 bg-zinc-950 px-2 py-1 rounded border border-zinc-800 truncate max-w-[200px]" title="<?php echo htmlspecialchars($file); ?>">
                                    <?php echo htmlspecialchars($file); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-[13px] text-zinc-400 font-medium tabular-nums">
                                <?php echo $size; ?> MB
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="download?file=<?php echo urlencode($dir . $file); ?>" aria-label="Descargar <?php echo htmlspecialchars($file); ?>" class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-md text-zinc-300 text-[13px] font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                                    <i data-lucide="<?php echo $isAudio ? 'headphones' : 'external-link'; ?>" class="w-3.5 h-3.5" aria-hidden="true"></i>
                                    <?php echo $isAudio ? 'Oír' : 'Ver'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div id="noResults" class="hidden py-16 text-center" aria-live="polite">
                    <i data-lucide="search-x" class="w-12 h-12 mx-auto mb-4 text-zinc-700" aria-hidden="true"></i>
                    <p class="text-[14px] font-semibold text-zinc-400">No se han encontrado resultados.</p>
                </div>

                <?php if (empty($files)): ?>
                <div class="py-16 text-center">
                    <i data-lucide="folder-open" class="w-12 h-12 mx-auto mb-4 text-zinc-700" aria-hidden="true"></i>
                    <p class="text-[14px] font-semibold text-zinc-400">Todavía no hay archivos subidos.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
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
            // Reset styles on all buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-zinc-100', 'text-zinc-950', 'font-bold');
                btn.classList.add('bg-zinc-800', 'text-zinc-300', 'hover:bg-zinc-700', 'font-medium', 'border', 'border-zinc-700', 'hover:border-zinc-600');
                btn.setAttribute('aria-pressed', 'false');
            });
            // Apply active styles
            buttonElement.classList.remove('bg-zinc-800', 'text-zinc-300', 'hover:bg-zinc-700', 'font-medium', 'border', 'border-zinc-700', 'hover:border-zinc-600');
            buttonElement.classList.add('bg-zinc-100', 'text-zinc-950', 'font-bold');
            buttonElement.setAttribute('aria-pressed', 'true');
            
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
