<?php
require_once 'auth.php'; // Protección de seguridad del CRM

$dir = 'uploads/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$files = array_diff(scandir($dir), array('.', '..', 'index.php', '.htaccess'));
natsort($files); // Orden natural
$files = array_reverse($files); // Recientes primero
?>
<!DOCTYPE html>
<html lang="es" class="bg-[#09090b] text-white font-sans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Archivo Maestro - File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#09090b] text-zinc-400 font-sans">
    <?php include 'sidebar.php'; ?>
    <main class="sm:ml-64 p-8 min-h-screen bg-bg">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-white tracking-tight">Archivos</h1>
                <p class="text-zinc-500 text-sm mt-1">Gestión de grabaciones y documentos adjuntos.</p>
            </div>
            <div class="px-3 py-1 bg-zinc-900 border border-border rounded-lg text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                <span id="fileCount" class="text-white"><?php echo count($files); ?></span> Archivos
            </div>
        </header>

        <!-- Filtros -->
        <div class="bg-card border border-border p-4 rounded-xl flex flex-col md:flex-row items-center justify-between gap-6 mb-6">
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
                <button onclick="filterType('all', this)" class="filter-btn active px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg transition-all">Todos</button>
                <button onclick="filterType('audio', this)" class="filter-btn px-4 py-2 bg-zinc-900 text-zinc-400 border border-border hover:text-white text-xs font-semibold rounded-lg transition-all">Audios</button>
                <button onclick="filterType('doc', this)" class="filter-btn px-4 py-2 bg-zinc-900 text-zinc-400 border border-border hover:text-white text-xs font-semibold rounded-lg transition-all">Docs</button>
            </div>

            <div class="relative w-full md:w-80 group">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-zinc-500 group-focus-within:text-primary transition-colors"></i>
                <input type="text" id="searchInput" placeholder="Filtrar archivos..." 
                       class="w-full pl-10 pr-4 py-2 bg-bg border border-border rounded-lg focus:ring-1 focus:ring-primary focus:border-primary text-white text-sm placeholder-zinc-600 transition-all outline-none">
            </div>
        </div>

        <div class="bg-card border border-border rounded-xl overflow-hidden shadow-sm flex-1">
            <div class="overflow-x-auto h-full">
                <table class="w-full text-left relative">
                    <thead class="sticky top-0 z-10 bg-zinc-900/90 backdrop-blur-xl">
                        <tr class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500 border-b border-border bg-zinc-900/30">
                            <th class="px-6 py-4">Archivo / Lead</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Identificador</th>
                            <th class="px-6 py-4 text-right">Tamaño</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="fileTableBody" class="divide-y divide-border">
                        <?php foreach($files as $file): 
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $isAudio = ($ext == 'webm' || $ext == 'ogg' || $ext == 'mp3' || $ext == 'wav');
                            $size = round(filesize($dir . $file) / 1024 / 1024, 2);
                            
                            // Extraer datos (Patrón inteligente: TIPO_NombreLead_YYYY-MM-DD_HHMM.ext)
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
                        <tr class="file-row hover:bg-zinc-900/40 transition-all group" data-type="<?php echo $typeClass; ?>" data-search="<?php echo htmlspecialchars($searchString); ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-lg <?php echo $isAudio ? 'bg-red-500/10 text-red-500' : 'bg-primary/10 text-primary'; ?> border border-border">
                                        <i data-lucide="<?php echo $isAudio ? 'mic' : 'file'; ?>" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($leadNameFormatted); ?></p>
                                        <p class="text-[10px] text-zinc-500 uppercase font-medium"><?php echo $isAudio ? 'Audio' : 'Documento'; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-zinc-300 font-bold block text-sm tracking-widest"><?php echo htmlspecialchars($dateStr); ?></span>
                                <?php if($timeStr): ?>
                                    <span class="text-[10px] font-black text-zinc-600 tracking-widest"><?php echo htmlspecialchars($timeStr); ?>H</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] font-mono font-bold text-zinc-500 block truncate max-w-[200px] transition-colors group-hover:text-zinc-300 bg-zinc-950 px-4 py-2 rounded-xl border border-zinc-800 shadow-inner" title="<?php echo htmlspecialchars($file); ?>">
                                    <?php echo htmlspecialchars($file); ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right font-mono text-[11px] text-zinc-500 font-black">
                                <?php echo $size; ?> MB
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="download?file=<?php echo urlencode($dir . $file); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-900 border border-border rounded-lg text-zinc-300 text-xs font-medium hover:bg-zinc-800 transition-all">
                                    <i data-lucide="<?php echo $isAudio ? 'play' : 'external-link'; ?>" class="w-3.5 h-3.5"></i>
                                    <?php echo $isAudio ? 'Oír' : 'Ver'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div id="noResults" class="hidden p-32 text-center select-none">
                    <i data-lucide="search-x" class="w-20 h-20 mx-auto mb-6 opacity-20 text-zinc-600"></i>
                    <h3 class="font-black text-sm tracking-[0.3em] uppercase text-zinc-500">No se encontraron archivos con ese filtro</h3>
                </div>

                <?php if (empty($files)): ?>
                <div class="p-32 text-center select-none grayscale opacity-10 border-t border-zinc-900">
                    <i data-lucide="folder-search-2" class="w-24 h-24 mx-auto mb-8"></i>
                    <h3 class="font-black text-sm tracking-[0.5em] uppercase">No hay registros alojados actualmente</h3>
                </div>
                <?php endif; ?>
            </div>
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
            
            // Toggle visual de los botones (Pills)
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');
                btn.classList.add('bg-zinc-900', 'text-zinc-400', 'border-zinc-800');
            });
            buttonElement.classList.remove('bg-zinc-900', 'text-zinc-400', 'border-zinc-800');
            buttonElement.classList.add('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');

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
            fileCountSpan.parentElement.classList.toggle('border-primary', visibleCount > 0);
            
            // Mostrar info de "no resultados" 
            if (visibleCount === 0 && rows.length > 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
