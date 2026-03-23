<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es" class="bg-dark text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Datos Leads - CRM Blue Pro</title>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen">
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-500 mb-2">Relación de Leads</h1>
                <p class="text-gray-400 text-lg tracking-tight">Gestión avanzada de prospectos y propuestas económicas de xCloud.</p>
            </div>
            <button onclick="toggleModal()" class="px-6 py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl flex items-center gap-2 transition-all transform hover:-translate-y-1 shadow-lg shadow-blue-600/20 active:scale-95">
                <i data-lucide="shield-plus" class="w-5 h-5"></i> Registrar Nuevo Lead
            </button>
        </div>

        <div class="bg-dark-card border border-dark-border rounded-[2.5rem] overflow-hidden shadow-2xl backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.15em] border-b border-dark-border bg-dark/40">
                            <th class="px-8 py-6">Lead / Empresa</th>
                            <th class="px-6 py-6 text-center">Fuente</th>
                            <th class="px-6 py-6 text-center">Etiquetas</th>
                            <th class="px-6 py-6 text-right">Propuesta (€)</th>
                            <th class="px-8 py-6 text-right">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-border">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-blue-500/5 transition-all group">
                            <!-- Nombre y Empresa -->
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-white font-bold text-lg leading-6 group-hover:text-blue-400 transition-colors"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="text-zinc-500 text-xs font-semibold uppercase tracking-widest mt-0.5">
                                        <?php echo $row['company'] ? htmlspecialchars($row['company']) : 'Particular'; ?>
                                        <?php if($row['website']): ?>
                                            <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank" class="text-blue-500/50 hover:text-blue-500 ml-2 italic text-[10px] lowercase">&rarr; Sitio Web</a>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Fuente (Pago/Orgánico) -->
                            <td class="px-6 py-5 text-center">
                                <?php if($row['source'] == 'pago'): ?>
                                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[9px] font-black rounded-lg border border-amber-500/20 uppercase tracking-widest">PAGO</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[9px] font-black rounded-lg border border-blue-500/20 uppercase tracking-widest">ORGÁNICO</span>
                                <?php endif; ?>
                            </td>

                            <!-- Etiquetas -->
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-wrap justify-center gap-1.5 min-w-[120px]">
                                    <?php 
                                    $tags = !empty($row['tags']) ? explode(', ', $row['tags']) : [];
                                    foreach($tags as $tag): 
                                        $tagColor = ($tag == 'meaads') ? 'emerald' : 'violet';
                                    ?>
                                        <span class="px-2 py-0.5 bg-<?php echo $tagColor; ?>-500/10 text-<?php echo $tagColor; ?>-400 text-[8px] font-black rounded-md border border-<?php echo $tagColor; ?>-500/20 uppercase tracking-tighter"><?php echo $tag; ?></span>
                                    <?php endforeach; if(empty($tags)) echo '<span class="text-zinc-700 text-[8px] italic font-bold">SIN ETIQUETAS</span>'; ?>
                                </div>
                            </td>

                            <!-- Precio Propuesta -->
                            <td class="px-6 py-5 text-right">
                                <span class="text-white font-black text-lg">
                                    <?php echo number_format($row['proposal_price'], 2, ',', '.'); ?> €
                                </span>
                            </td>

                            <!-- Fecha -->
                            <td class="px-8 py-5 text-right font-medium">
                                <span class="text-zinc-400 text-sm block"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></span>
                                <span class="text-zinc-600 text-[10px] uppercase font-bold"><?php echo date('H:i', strtotime($row['created_at'])); ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if ($result->num_rows == 0): ?>
                    <div class="p-24 text-center text-zinc-700 select-none">
                        <i data-lucide="layers" class="w-16 h-16 mx-auto mb-6 opacity-5"></i>
                        <h3 class="font-black text-xl mb-4 tracking-[0.2em] opacity-10">SISTEMA VACÍO</h3>
                        <button onclick="toggleModal()" class="text-blue-600 font-bold hover:text-blue-500 underline underline-offset-8 transition-all">INICIAR MI EXPLOTACIÓN DE LEADS &rarr;</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
