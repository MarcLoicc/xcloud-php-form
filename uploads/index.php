<?php
require_once '../auth.php'; // Protección de seguridad del CRM

$dir = './';
$files = array_diff(scandir($dir), array('.', '..', 'index.php', '.htaccess'));
natsort($files); // Orden natural de los archivos
$files = array_reverse($files); // Los más recientes primero
?>
<!DOCTYPE html>
<html lang="es" class="bg-[#09090b] text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorador de Archivos - CRM Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#09090b] text-zinc-400 p-8 sm:p-20">
    <div class="max-w-5xl mx-auto">
        <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <a href="../index.php" class="text-blue-500 hover:text-blue-400 text-xs font-black uppercase tracking-widest flex items-center gap-2 mb-4 group transition-all">
                   <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i> Volver al CRM
                </a>
                <h1 class="text-5xl font-black text-white tracking-tighter">Archivo Maestro</h1>
                <p class="mt-2 text-zinc-600 text-lg">Explorador de documentos y audios alojados en xCloud.</p>
            </div>
            <div class="text-right">
                <span class="px-4 py-2 bg-zinc-900 border border-zinc-800 rounded-2xl text-[10px] font-black tracking-[0.2em] text-zinc-500 uppercase">
                    <?php echo count($files); ?> Entradas totales
                </span>
            </div>
        </header>

        <div class="bg-zinc-950 border border-zinc-900 rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-900/50 text-[10px] font-black uppercase tracking-widest text-zinc-700 border-b border-zinc-900">
                            <th class="px-10 py-6">Tipo / Icono</th>
                            <th class="px-8 py-6">Nombre del Archivo</th>
                            <th class="px-8 py-6 text-right">Tamaño</th>
                            <th class="px-10 py-6 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900">
                        <?php foreach($files as $file): 
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $isAudio = ($ext == 'webm' || $ext == 'ogg' || $ext == 'mp3');
                            $isDoc = ($ext == 'pdf' || $ext == 'docx' || $ext == 'png' || $ext == 'jpg');
                            $size = round(filesize($file) / 1024 / 1024, 2);
                        ?>
                        <tr class="hover:bg-blue-600/5 transition-colors group">
                            <td class="px-10 py-5">
                                <div class="w-12 h-12 flex items-center justify-center rounded-2xl <?php echo $isAudio ? 'bg-red-500/10 text-red-500' : 'bg-blue-600/10 text-blue-500'; ?>">
                                    <i data-lucide="<?php echo $isAudio ? 'volume-2' : 'file-text'; ?>"></i>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-zinc-200 block truncate max-w-sm"><?php echo htmlspecialchars($file); ?></span>
                                <span class="text-[9px] uppercase font-black tracking-widest opacity-20"><?php echo strtoupper($ext); ?> FILE</span>
                            </td>
                            <td class="px-8 py-5 text-right font-mono text-xs text-zinc-800">
                                <?php echo $size; ?> MB
                            </td>
                            <td class="px-10 py-5 text-right">
                                <a href="<?php echo htmlspecialchars($file); ?>" target="_blank" class="inline-flex items-center gap-2 p-3 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-xl text-white transition-all hover:scale-105 active:scale-95 shadow-lg">
                                    <i data-lucide="download-cloud" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($files)): ?>
                <div class="p-40 text-center">
                    <i data-lucide="folder-x" class="w-16 h-16 mx-auto mb-6 opacity-5"></i>
                    <h3 class="font-black text-xs tracking-widest text-zinc-800 uppercase">La carpeta está vacía actualmente</h3>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
