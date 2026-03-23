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
    <title>Leads - CRM Blue Pro</title>
</head>
<body class="bg-dark text-white font-sans">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 p-6 sm:p-12 min-h-screen">
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-500 mb-2">Base de Datos</h1>
                <p class="text-gray-400 text-lg">Listado completo de clientes potenciales registrados.</p>
            </div>
            <!-- Trigger Modal en Azul Eléctrico -->
            <button onclick="toggleModal()" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl flex items-center gap-2 transition-all transform hover:-translate-y-1 shadow-lg shadow-blue-600/20 active:scale-95">
                <i data-lucide="plus-circle" class="w-5 h-5"></i> Registrar Nuevo
            </button>
        </div>

        <div class="bg-dark-card border border-dark-border rounded-3xl overflow-hidden shadow-2xl backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-500 text-[10px] font-bold uppercase tracking-widest border-b border-dark-border bg-dark/30">
                            <th class="px-6 py-5">Fecha</th>
                            <th class="px-6 py-5">Nombre</th>
                            <th class="px-6 py-5">Contacto</th>
                            <th class="px-6 py-5">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-border">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-blue-500/5 transition-colors group">
                            <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                <?php echo date('d/m/y', strtotime($row['created_at'])); ?>
                                <span class="block text-[10px] opacity-40 uppercase"><?php echo date('H:i', strtotime($row['created_at'])); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-white font-bold text-base block"><?php echo htmlspecialchars($row['name']); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-2 text-blue-400 text-sm font-semibold">
                                        <i data-lucide="mail" class="w-3.5 h-3.5 opacity-50"></i> <?php echo htmlspecialchars($row['email']); ?>
                                    </span>
                                    <?php if($row['phone']): ?>
                                    <span class="inline-flex items-center gap-2 text-gray-500 text-[11px] font-medium">
                                        <i data-lucide="phone" class="w-3.5 h-3.5 opacity-40"></i> <?php echo htmlspecialchars($row['phone']); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-sm truncate">
                                <p class="text-sm text-gray-400 italic">
                                    <?php echo $row['message'] ? htmlspecialchars($row['message']) : '<span class="opacity-10">Sin notas</span>'; ?>
                                </p>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if ($result->num_rows == 0): ?>
                    <div class="p-20 text-center text-gray-600">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                        <h3 class="font-bold text-lg mb-8 uppercase tracking-widest">No hay registros</h3>
                        <button onclick="toggleModal()" class="text-blue-500 font-black hover:text-blue-400 underline underline-offset-8">REGISTRAR MI PRIMER LEAD &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
