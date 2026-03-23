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
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Lista de Leads - CRM Pro</title>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen">
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-500 mb-2">Gestión de Leads</h1>
                <p class="text-gray-400 text-lg">Listado completo de clientes potenciales registrados en el sistema de xCloud.</p>
            </div>
            <a href="add-lead.php" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl flex items-center gap-2 transition-all transform hover:-translate-y-1 shadow-lg shadow-indigo-600/20">
                <i data-lucide="plus" class="w-5 h-5"></i> Registrar Nuevo
            </a>
        </div>

        <div class="bg-dark-card border border-dark-border rounded-3xl overflow-hidden shadow-2xl backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-500 text-xs font-bold uppercase tracking-widest border-b border-dark-border bg-dark/30">
                            <th class="px-6 py-5">Fecha de Registro</th>
                            <th class="px-6 py-5">Nombre Completo</th>
                            <th class="px-6 py-5">Contacto</th>
                            <th class="px-6 py-5">Mensaje</th>
                            <th class="px-6 py-5 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-border">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-indigo-500/5 transition-colors group">
                            <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                <span class="block text-xs opacity-50"><?php echo date('H:i', strtotime($row['created_at'])); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-white font-bold text-base block"><?php echo htmlspecialchars($row['name']); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-2 text-indigo-300 text-sm font-semibold">
                                        <i data-lucide="mail" class="w-3.5 h-3.5"></i> <?php echo htmlspecialchars($row['email']); ?>
                                    </span>
                                    <?php if($row['phone']): ?>
                                    <span class="inline-flex items-center gap-2 text-gray-500 text-xs">
                                        <i data-lucide="phone" class="w-3.5 h-3.5"></i> <?php echo htmlspecialchars($row['phone']); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-400 italic max-w-xs truncate" title="<?php echo htmlspecialchars($row['message']); ?>">
                                    <?php echo $row['message'] ? '"'.htmlspecialchars($row['message']).'"' : '<span class="opacity-20">Sin notas</span>'; ?>
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black rounded-lg uppercase border border-emerald-500/20 tracking-tighter">
                                    Activo
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if ($result->num_rows == 0): ?>
                    <div class="p-20 text-center">
                        <div class="w-16 h-16 bg-dark-border/50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">No hay leads todavía</h3>
                        <p class="text-gray-500 max-w-xs mx-auto mb-8">Empieza a registrar prospectos para verlos aquí listados con su información completa.</p>
                        <a href="add-lead.php" class="text-indigo-400 font-bold hover:text-indigo-300 transition-colors underline underline-offset-8">Añadir mi primer lead &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
