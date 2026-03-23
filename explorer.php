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
    <title>Archivo Maestro - File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#09090b] text-zinc-400 p-8 sm:p-20 min-h-screen flex flex-col">
    <div class="max-w-7xl mx-auto w-full flex-1 flex flex-col">
        <header class="mb-10 animate-in fade-in slide-in-from-top-4 duration-700">
            <a href="index.php" class="text-blue-500 hover:text-blue-400 text-xs font-black uppercase tracking-widest flex items-center gap-2 mb-6 group transition-all w-fit">
               <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i> Panel Dashboard
            </a>
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div>
                    <h1 class="text-6xl font-black text-white tracking-tighter uppercase italic">File Manager</h1>
                    <p class="mt-2 text-zinc-600 text-lg tracking-tight">Gestión avanzada de todos tus documentos y grabaciones alojados.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-5 py-3 bg-zinc-900 border border-zinc-800 rounded-3xl text-[10px] font-black tracking-[0.3em] text-zinc-500 uppercase">
                        <span id="fileCount"><?php echo count($files); ?></span> Entradas
                    </span>
                </div>
            </div>
        </header>

        <!-- Filtros y Buscador -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8 bg-zinc-950 p-4 rounded-3xl border border-zinc-900 shadow-2xl">
            
            <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                <button onclick="filterType('all', this)" class="filter-btn active px-6 py-3 bg-blue-600 text-white border border-blue-500 rounded-2xl text-xs font-bold transition-all shadow-lg shadow-blue-600/20">Todos</button>
                <button onclick="filterType('audio', this)" class="filter-btn px-6 py-3 bg-zinc-900 text-zinc-400 border border-zinc-800 hover:bg-zinc-800 hover:text-white rounded-2xl text-xs font-bold transition-all">Audios 🔊</button>
                <button onclick="filterType('doc', this)" class="filter-btn px-6 py-3 bg-zinc-900 text-zinc-400 border border-zinc-800 hover:bg-zinc-800 hover:text-white rounded-2xl text-xs font-bold transition-all">Documentos 📄</button>
            </div>

            <div class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-zinc-500 group-focus-within:text-blue-500 transition-colors">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Buscar por lead o archivo..." 
                       class="w-full pl-14 pr-4 py-3.5 bg-zinc-900 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-600 transition-all font-medium text-sm outline-none">
            </div>

        </div>

        <div class="bg-zinc-950 border border-zinc-900 rounded-[3rem] overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transition-all flex-1">
            <div class="overflow-x-auto h-full">
                <table class="w-full text-left relative">
                    <thead class="sticky top-0 z-10 bg-zinc-900/90 backdrop-blur-xl">
                        <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 border-b border-zinc-900">
                            <th class="px-12 py-8">Lead / Tipo</th>
                            <th class="px-8 py-8">Fecha y Hora</th>
                            <th class="px-8 py-8">Identificador Original</th>
                            <th class="px-8 py-8 text-right">Peso</th>
                            <th class="px-12 py-8 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="fileTableBody" class="divide-y divide-zinc-900 backdrop-blur-sm">
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
                        <tr class="file-row hover:bg-blue-600/5 transition-all group" data-type="<?php echo $typeClass; ?>" data-search="<?php echo htmlspecialchars($searchString); ?>">
                            <td class="px-12 py-6">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 flex items-center justify-center rounded-2xl <?php echo $isAudio ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-blue-600/10 text-blue-500 border border-blue-500/20'; ?> group-hover:scale-110 transition-transform shadow-lg">
                                        <i data-lucide="<?php echo $isAudio ? 'mic-2' : 'file-text'; ?>"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-black text-base uppercase tracking-tight"><?php echo htmlspecialchars($leadNameFormatted); ?></p>
                                        <p class="text-[9px] font-bold <?php echo $isAudio ? 'text-red-500/60' : 'text-blue-500/60'; ?> uppercase tracking-widest mt-1"><?php echo $isAudio ? 'Grabación de Voz' : 'Documento Adjunto'; ?></p>
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
                            <td class="px-12 py-6 text-right">
                                <a href="download.php?file=<?php echo urlencode($dir . $file); ?>" target="_blank" class="inline-flex items-center gap-3 px-6 py-4 bg-zinc-900 hover:bg-[#09090b] border border-zinc-800 <?php echo $isAudio ? 'hover:border-red-500/50 hover:text-red-400' : 'hover:border-blue-500/50 hover:text-blue-400'; ?> rounded-2xl text-white font-black text-[10px] uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-xl group-hover:shadow-blue-500/10">
                                    <i data-lucide="<?php echo $isAudio ? 'play-circle' : 'external-link'; ?>" class="w-4 h-4"></i> <?php echo $isAudio ? 'Reproducir' : 'Abrir Archivo'; ?>
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
                btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-500', 'shadow-lg', 'shadow-blue-600/20');
                btn.classList.add('bg-zinc-900', 'text-zinc-400', 'border-zinc-800');
            });
            buttonElement.classList.remove('bg-zinc-900', 'text-zinc-400', 'border-zinc-800');
            buttonElement.classList.add('bg-blue-600', 'text-white', 'border-blue-500', 'shadow-lg', 'shadow-blue-600/20');

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
