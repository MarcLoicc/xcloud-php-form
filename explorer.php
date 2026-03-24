<?php
require_once 'auth.php'; // Protección de seguridad del CRM

$dir = 'uploads/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$files = array_diff(scandir($dir), array('.', '..', 'index.php', '.htaccess'));
natsort($files); // Orden natural
$files = array_reverse($files); // Recientes primero
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Acme SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>
    <main class="sm:ml-64 p-8 lg:p-12 min-h-screen" id="main-content">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Documents</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Manage call recordings and attached documents.</p>
            </div>
            <div class="px-4 py-2 bg-zinc-900 border border-zinc-800 rounded-md text-[13px] font-semibold text-zinc-400" aria-live="polite">
                <span id="fileCount" class="text-zinc-100"><?php echo count($files); ?></span> Total Files
            </div>
        </header>

        <!-- Filters -->
        <section aria-labelledby="documents-filters-heading" class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4 bg-zinc-900 border border-zinc-800 p-4 rounded-xl">
            <h2 id="documents-filters-heading" class="sr-only">File Filters</h2>
            
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto" role="group" aria-label="Filter by file type">
                <button type="button" aria-pressed="true" onclick="filterType('all', this)" class="filter-btn px-4 py-2 bg-zinc-100 text-zinc-950 text-[13px] font-bold rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">All</button>
                <button type="button" aria-pressed="false" onclick="filterType('audio', this)" class="filter-btn px-4 py-2 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 text-[13px] font-medium rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 border border-zinc-700 hover:border-zinc-600">Audio</button>
                <button type="button" aria-pressed="false" onclick="filterType('doc', this)" class="filter-btn px-4 py-2 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 text-[13px] font-medium rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 border border-zinc-700 hover:border-zinc-600">Documents</button>
            </div>

            <div class="relative w-full md:w-80">
                <label for="searchInput" class="sr-only">Search files</label>
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                <input type="text" id="searchInput" placeholder="Search by name..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-[14px] text-zinc-100 placeholder:text-zinc-600 outline-none transition-colors">
            </div>
        </section>

        <!-- Table -->
        <section aria-labelledby="documents-table-heading" class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <h2 id="documents-table-heading" class="sr-only">File List</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-950/50 border-b border-zinc-800">
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">File / Context</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-center">Date</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">System Name</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Size</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="fileTableBody" class="divide-y divide-zinc-800">
                        <?php foreach($files as $file): 
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $isAudio = ($ext == 'webm' || $ext == 'ogg' || $ext == 'mp3' || $ext == 'wav');
                            $size = round(filesize($dir . $file) / 1024 / 1024, 2);
                            
                            $parts = explode('_', pathinfo($file, PATHINFO_FILENAME)); 
                            $leadName = $parts[1] ?? 'Unassigned';
                            $leadNameFormatted = str_replace('-', ' ', $leadName);
                            
                            $dateStr = $parts[2] ?? 'N/A';
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
                                        <p class="text-[12px] text-zinc-500 font-medium"><?php echo $isAudio ? 'Audio Recording' : 'Document'; ?></p>
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
                                <a href="download?file=<?php echo urlencode($dir . $file); ?>" aria-label="Download <?php echo htmlspecialchars($file); ?>" class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-md text-zinc-300 text-[13px] font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                                    <i data-lucide="<?php echo $isAudio ? 'headphones' : 'external-link'; ?>" class="w-3.5 h-3.5" aria-hidden="true"></i>
                                    <?php echo $isAudio ? 'Play' : 'View'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div id="noResults" class="hidden py-16 text-center" aria-live="polite">
                    <i data-lucide="search-x" class="w-12 h-12 mx-auto mb-4 text-zinc-700" aria-hidden="true"></i>
                    <p class="text-[14px] font-semibold text-zinc-400">No results found.</p>
                </div>

                <?php if (empty($files)): ?>
                <div class="py-16 text-center">
                    <i data-lucide="folder-open" class="w-12 h-12 mx-auto mb-4 text-zinc-700" aria-hidden="true"></i>
                    <p class="text-[14px] font-semibold text-zinc-400">No files uploaded yet.</p>
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
                btn.classList.add('bg-zinc-800', 'text-zinc-300', 'border-zinc-700', 'font-medium');
                btn.setAttribute('aria-pressed', 'false');
            });
            // Apply active styles
            buttonElement.classList.remove('bg-zinc-800', 'text-zinc-300', 'border-zinc-700', 'font-medium');
            buttonElement.classList.add('bg-zinc-100', 'text-zinc-950', 'font-bold', 'border-transparent');
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
