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
    <title>Archivo Maestro - CRM Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#09090b] text-zinc-400 p-8 sm:p-20">
    <div class="max-w-6xl mx-auto">
        <header class="mb-14 flex flex-col md:flex-row md:items-end justify-between gap-8 animate-in fade-in slide-in-from-top-4 duration-700">
            <div>
                <a href="index.php" class="text-blue-500 hover:text-blue-400 text-xs font-black uppercase tracking-widest flex items-center gap-2 mb-4 group transition-all">
                   <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i> Panel Dashboard
                </a>
                <h1 class="text-6xl font-black text-white tracking-tighter uppercase italic">Archivo Maestro</h1>
                <p class="mt-2 text-zinc-600 text-lg tracking-tight">Exploración centralizada de documentos y grabaciones alojadas en xCloud.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-5 py-3 bg-zinc-900 border border-zinc-800 rounded-3xl text-[10px] font-black tracking-[0.3em] text-zinc-500 uppercase">
                    <?php echo count($files); ?> Entradas totales
                </span>
            </div>
        </header>

        <div class="bg-zinc-950 border border-zinc-900 rounded-[3rem] overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-900/40 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-700 border-b border-zinc-900">
                            <th class="px-12 py-8">Lead / Tipo</th>
                            <th class="px-8 py-8">Identificador de Archivo</th>
                            <th class="px-8 py-8 text-right">Peso</th>
                            <th class="px-12 py-8 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900">
                        <?php foreach($files as $file): 
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $isAudio = ($ext == 'webm' || $ext == 'ogg' || $ext == 'mp3');
                            $size = round(filesize($dir . $file) / 1024 / 1024, 2);
                            
                            // Extraer nombre del lead si sigue el patrón DOC_Name_Date
                            $parts = explode('_', $file);
                            $leadName = $parts[1] ?? 'Desconocido';
                            $leadNameFormatted = str_replace('-', ' ', $leadName);
                        ?>
                        <tr class="hover:bg-blue-600/5 transition-all group">
                            <td class="px-12 py-6">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 flex items-center justify-center rounded-2xl <?php echo $isAudio ? 'bg-red-500/10 text-red-500' : 'bg-blue-600/10 text-blue-500'; ?> group-hover:scale-110 transition-transform shadow-lg">
                                        <i data-lucide="<?php echo $isAudio ? 'mic-2' : 'file-text'; ?>"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-black text-base uppercase tracking-tight"><?php echo htmlspecialchars($leadNameFormatted); ?></p>
                                        <p class="text-[9px] font-bold text-zinc-700 uppercase tracking-widest mt-1"><?php echo $isAudio ? 'Grabación Llamada' : 'Documento Lead'; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-bold text-zinc-500 block truncate max-w-xs transition-colors group-hover:text-zinc-300"><?php echo htmlspecialchars($file); ?></span>
                            </td>
                            <td class="px-8 py-6 text-right font-mono text-[10px] text-zinc-800">
                                <?php echo $size; ?> MB
                            </td>
                            <td class="px-12 py-6 text-right">
                                <a href="uploads/<?php echo htmlspecialchars($file); ?>" target="_blank" class="inline-flex items-center gap-3 px-6 py-3.5 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-2xl text-white font-black text-[10px] uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-2xl">
                                    <i data-lucide="eye" class="w-4 h-4"></i> Ver Registro
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($files)): ?>
                <div class="p-60 text-center select-none grayscale opacity-10">
                    <i data-lucide="folder-search-2" class="w-24 h-24 mx-auto mb-8"></i>
                    <h3 class="font-black text-sm tracking-[0.5em] uppercase">No hay registros almacenados</h3>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
