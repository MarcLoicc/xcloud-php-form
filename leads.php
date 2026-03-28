<?php require_once 'auth.php'; ?>
<?php
date_default_timezone_set('Europe/Madrid');
require_once 'db.php';
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");

$existingTags = ['Metaads', 'Arquitectos', 'VIP', 'Urgente']; // Predefined default tags
$tagQuery = $conn->query("SELECT DISTINCT tags FROM leads WHERE tags IS NOT NULL AND tags != ''");
if ($tagQuery) {
    while ($tRow = $tagQuery->fetch_assoc()) {
        foreach (explode(',', $tRow['tags']) as $p) {
            $tag = trim($p);
            if (!empty($tag) && !in_array($tag, $existingTags)) $existingTags[] = $tag;
        }
    }
}
sort($existingTags);
sort($existingTags);

function getStatusBadge($status) {
    $map = [
        'nuevo' => ['label' => 'Nuevo', 'class' => 'bg-indigo-900/30 text-indigo-300 ring-1 ring-inset ring-indigo-500/30'],
        'no_responde' => ['label' => 'Sin Respuesta', 'class' => 'bg-zinc-800 text-zinc-300 ring-1 ring-inset ring-zinc-600'],
        'llamar_tarde' => ['label' => 'Llamar más tarde', 'class' => 'bg-amber-900/30 text-amber-300 ring-1 ring-inset ring-amber-500/30'],
        'enviar_propuesta' => ['label' => 'Enviar Propuesta', 'class' => 'bg-blue-900/30 text-blue-300 ring-1 ring-inset ring-blue-500/30'],
        'propuesta_enviada' => ['label' => 'Propuesta Enviada', 'class' => 'bg-purple-900/30 text-purple-300 ring-1 ring-inset ring-purple-500/30'],
        'ganado' => ['label' => 'Ganado', 'class' => 'bg-emerald-900/30 text-emerald-300 ring-1 ring-inset ring-emerald-500/30'],
        'perdido' => ['label' => 'Perdido', 'class' => 'bg-red-900/30 text-red-300 ring-1 ring-inset ring-red-500/30'],
        'no_cualificado' => ['label' => 'No Cualificado', 'class' => 'bg-zinc-800 text-zinc-400 ring-1 ring-inset ring-zinc-700'],
        'interesado_tarde' => ['label' => 'Interesado (Futuro)', 'class' => 'bg-cyan-900/30 text-cyan-300 ring-1 ring-inset ring-cyan-500/30'],
    ];
    return $map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'class' => 'bg-zinc-800 text-zinc-300 ring-1 ring-inset ring-zinc-600'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - CRM Marcloi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-zinc-950 min-h-screen text-zinc-100 antialiased">
    <?php include 'sidebar.php'; ?>

    <main class="sm:ml-64 min-h-screen p-8 lg:p-12 mb-20" id="main-content">
        <!-- Leads Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-900 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 tracking-tight">Clientes</h1>
                <p class="text-[14px] text-zinc-400 mt-1 font-medium">Gestiona tu registro de clientes y haz seguimiento de estados.</p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="toggleModal()" aria-haspopup="dialog" aria-expanded="false" aria-controls="addLeadModal" class="px-5 py-2.5 bg-zinc-100 rounded-md text-[14px] font-bold text-zinc-950 hover:bg-zinc-300 transition-colors flex items-center gap-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i> Crear Nuevo Cliente
                </button>
            </div>
        </header>

        <!-- Search Controls -->
        <section aria-labelledby="filters-heading" class="mb-6 flex flex-col lg:flex-row items-center justify-between gap-4">
            <h2 id="filters-heading" class="sr-only">Filtros de Tabla</h2>
            
            <div class="w-full lg:w-1/3 relative">
                <label for="filterGlobal" class="sr-only">Buscar clientes</label>
                <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                <input type="text" id="filterGlobal" placeholder="Buscar por nombre o empresa..." class="w-full pl-10 pr-4 py-2 bg-zinc-900 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-500">
            </div>

            <div class="flex items-center gap-3 w-full lg:w-auto overflow-x-auto pb-2 lg:pb-0 scrollbar-none">
                <div class="relative">
                    <label for="filterStatus" class="sr-only">Filtrar por estado</label>
                    <select id="filterStatus" class="bg-zinc-900 border border-zinc-800 rounded-md pl-4 pr-10 py-2 text-[14px] font-medium text-zinc-300 focus:ring-2 focus:ring-indigo-500 transition-colors appearance-none cursor-pointer">
                        <option value="all">Cualquier estado</option>
                        <option value="nuevo">Nuevo</option>
                        <option value="no_responde">Sin Respuesta</option>
                        <option value="llamar_tarde">Llamar más tarde</option>
                        <option value="interesado_tarde">Interesado (Futuro)</option>
                        <option value="enviar_propuesta">Enviar Propuesta</option>
                        <option value="propuesta_enviada">Propuesta Enviada</option>
                        <option value="ganado">Ganado</option>
                        <option value="perdido">Perdido</option>
                        <option value="no_cualificado">No Cualificado</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                </div>
                
                <div class="relative">
                    <label for="filterSource" class="sr-only">Filtrar por origen</label>
                    <select id="filterSource" class="bg-zinc-900 border border-zinc-800 rounded-md pl-4 pr-10 py-2 text-[14px] font-medium text-zinc-300 focus:ring-2 focus:ring-indigo-500 transition-colors appearance-none cursor-pointer">
                        <option value="all">Cualquier origen</option>
                        <option value="pago">Márketing de Pago</option>
                        <option value="organico">Orgánico</option>
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-md px-4 py-2 flex items-center gap-2" aria-live="polite" aria-atomic="true">
                    <span id="visibleLeadsCount" class="text-[14px] font-bold text-zinc-100"><?php echo $result->num_rows; ?></span>
                    <span class="text-[14px] text-zinc-500">registros</span>
                </div>
            </div>
        </section>

        <!-- Data Table -->
        <section aria-labelledby="table-heading" class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <h2 id="table-heading" class="sr-only">Base de Datos de Clientes</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-zinc-950/50 border-b border-zinc-800">
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Cliente</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Teléfono</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider">Origen</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Valor</th>
                            <th scope="col" class="px-6 py-4 text-[12px] font-semibold text-zinc-400 uppercase tracking-wider text-right">Fecha</th>
                            <th scope="col" class="sr-only">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        <?php 
                        function formatPhone($phone) {
                            if (!$phone) return '---';
                            $clean = preg_replace('/[^0-9]/', '', $phone);
                            if (strpos($clean, '34') === 0 && strlen($clean) > 9) { $clean = substr($clean, 2); }
                            if (strlen($clean) === 9) { return substr($clean, 0, 3) . ' ' . substr($clean, 3, 3) . ' ' . substr($clean, 6, 3); }
                            return $phone;
                        }

                        function formatName($name) {
                            if (!$name) return '';
                            return mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8');
                        }

                        function getAvatarColor($name) {
                            $colors = [
                                'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
                                'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                'bg-sky-500/20 text-sky-400 border-sky-500/30',
                                'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                'bg-violet-500/20 text-violet-400 border-violet-500/30',
                                'bg-fuchsia-500/20 text-fuchsia-400 border-fuchsia-500/30',
                                'bg-cyan-500/20 text-cyan-400 border-cyan-500/30'
                            ];
                            $hash = crc32($name);
                            return $colors[abs($hash) % count($colors)];
                        }

                        function getInitials($name) {
                            if (!$name) return '??';
                            $parts = explode(' ', trim($name));
                            if (count($parts) >= 2) {
                                return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1), 'UTF-8');
                            }
                            return mb_strtoupper(mb_substr($name, 0, 2), 'UTF-8');
                        }

                        while($row = $result->fetch_assoc()): 
                            $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $rowDate = date('Y-m-d', strtotime($row['created_at']));
                            $displayDate = date('M d, Y', strtotime($row['created_at']));
                            $statusInfo = getStatusBadge($row['status'] ?? 'nuevo');
                            $displayName = formatName($row['name']);
                            $avatarStyle = getAvatarColor($row['name']);
                        ?>
                        <tr class="lead-row hover:bg-zinc-800/30 transition-colors group" 
                            data-id="<?php echo $row['id']; ?>"
                            data-source="<?php echo $row['source']; ?>"
                            data-status="<?php echo $row['status'] ?? 'nuevo'; ?>">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg border flex items-center justify-center text-[13px] font-bold tracking-tighter <?php echo $avatarStyle; ?>" aria-hidden="true">
                                        <?php echo getInitials($displayName); ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[14px] font-bold text-zinc-100 cursor-pointer hover:text-white transition-colors" onclick="showLeadDetails(<?php echo $json_data; ?>)"><?php echo htmlspecialchars($displayName); ?></span>
                                        <span class="text-[12px] text-zinc-500"><?php echo htmlspecialchars($row['company'] ?: 'Particular'); ?></span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-[14px] text-zinc-400 tabular-nums">
                                    <?php echo htmlspecialchars(formatPhone($row['phone'])); ?>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="cursor-pointer" onclick="quickEdit(this, 'status', '<?php echo $row['id']; ?>')">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-[12px] font-medium <?php echo $statusInfo['class']; ?>">
                                        <?php echo $statusInfo['label']; ?>
                                    </span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="text-[13px] text-zinc-400 flex items-center gap-2 cursor-pointer hover:text-zinc-200" onclick="quickEdit(this, 'source', '<?php echo $row['id']; ?>')">
                                    <i data-lucide="<?php echo $row['source'] === 'pago' ? 'target' : 'globe'; ?>" class="w-3 h-3"></i>
                                    <?php echo $row['source'] === 'pago' ? 'Pago' : 'Orgánico'; ?>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <span class="text-[14px] font-bold text-zinc-100 cursor-pointer hover:text-indigo-400 transition-colors" onclick="quickEdit(this, 'proposal_price', '<?php echo $row['id']; ?>')">
                                    €<?php echo number_format($row['proposal_price'] ?? 0, 2); ?>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <span class="text-[13px] text-zinc-500 cursor-pointer hover:text-zinc-300 transition-colors" onclick="quickEdit(this, 'created_at', '<?php echo $row['id']; ?>')">
                                    <?php echo $displayDate; ?>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <button onclick='showLeadDetails(<?php echo $json_data; ?>)' aria-label="Editar <?php echo htmlspecialchars($row['name']); ?>" class="p-2 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                                    <i data-lucide="edit-2" class="w-4 h-4" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-zinc-800 flex items-center justify-between text-[13px] text-zinc-500">
                <p>Fin de los resultados.</p>
            </div>
        </section>
    </main>

    <!-- Modal Detail SaaS Accessible -->
    <div id="detailModal" role="dialog" aria-modal="true" aria-labelledby="modal-title" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-zinc-900 border border-zinc-800 w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]" id="detailModalContent" tabindex="-1">
            <form id="editLeadForm" enctype="multipart/form-data" class="flex flex-col h-full">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="det-id">
                
                <!-- HEADER MODAL -->
                <div class="px-8 py-6 border-b border-zinc-800 flex justify-between items-start bg-zinc-950/50 sticky top-0 z-10 shrink-0">
                    <div class="w-full mr-8">
                        <label for="det-name" class="sr-only">Nombre del Cliente</label>
                        <input type="text" name="name" id="det-name" class="w-full bg-transparent border border-transparent focus:bg-zinc-900 focus:border-zinc-700 focus:ring-2 focus:ring-indigo-500 rounded-md text-2xl font-bold text-zinc-100 transition-colors px-3 py-1.5 -ml-3" required placeholder="Nombre del contacto">
                        
                        <label for="det-company" class="sr-only">Empresa</label>
                        <input type="text" name="company" id="det-company" class="w-full bg-transparent border border-transparent focus:bg-zinc-900 focus:border-zinc-700 focus:ring-2 focus:ring-indigo-500 rounded-md text-zinc-400 text-[15px] mt-1 px-3 py-1.5 -ml-3 transition-colors" placeholder="Empresa (Opcional)">
                    </div>
                    <button type="button" onclick="closeDetailModal()" aria-label="Cerrar ventana" class="p-2 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 shrink-0">
                        <i data-lucide="x" class="w-6 h-6" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- CONTENIDO MODAL SCROLLABLE -->
                <div class="p-8 overflow-y-auto w-full max-h-[60vh] custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                        
                        <!-- Columna Izquierda (Principal) -->
                        <div class="md:col-span-7 space-y-8">
                            <!-- Datos Esenciales -->
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label for="det-status" class="block text-[13px] font-semibold text-zinc-300 mb-2">Estado del Lead</label>
                                    <div class="relative">
                                        <select name="status" id="det-status" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 pl-3 pr-8 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors appearance-none shadow-inner">
                                            <option value="nuevo">Nuevo</option>
                                            <option value="no_responde">Sin Respuesta</option>
                                            <option value="llamar_tarde">Llamar más tarde</option>
                                            <option value="interesado_tarde">Interesado (Futuro)</option>
                                            <option value="enviar_propuesta">Enviar Propuesta</option>
                                            <option value="propuesta_enviada">Propuesta Enviada</option>
                                            <option value="ganado">Ganado</option>
                                            <option value="perdido">Perdido</option>
                                            <option value="no_cualificado">No Cualificado</option>
                                        </select>
                                        <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div>
                                    <label for="det-price" class="block text-[13px] font-semibold text-zinc-300 mb-2">Valor Obtenido (€)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-[14px]" aria-hidden="true">€</span>
                                        <input type="number" step="0.01" name="proposal_price" id="det-price" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 pl-8 pr-3 text-zinc-100 font-medium focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] shadow-inner" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-6 col-span-2">
                                    <div>
                                        <label for="det-date-only" class="block text-[13px] font-semibold text-zinc-300 mb-2">Fecha de Entrada</label>
                                        <input type="date" id="det-date-only" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 px-3 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors shadow-inner">
                                    </div>
                                    <div>
                                        <label for="det-time-only" class="block text-[13px] font-semibold text-zinc-300 mb-2">Hora</label>
                                        <input type="time" id="det-time-only" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 px-3 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors shadow-inner">
                                    </div>
                                    <input type="hidden" name="created_at" id="det-full-date">
                                </div>
                            </div>

                            <!-- Datos de Contacto -->
                            <fieldset class="space-y-4 pt-6 border-t border-zinc-800/50">
                                <legend class="block text-[14px] font-bold text-zinc-100 mb-4">Información de Contacto</legend>
                                <div class="relative">
                                    <label for="det-website" class="sr-only">Sitio Web</label>
                                    <i data-lucide="globe" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                                    <input type="url" name="website" id="det-website" class="w-full pl-10 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner" placeholder="ej. https://www.empresa.com">
                                </div>
                                <div class="relative">
                                    <label for="det-email" class="sr-only">Correo electrónico</label>
                                    <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                                    <input type="email" name="email" id="det-email" class="w-full pl-10 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner" placeholder="ej. correo@empresa.com">
                                </div>
                                <div class="relative">
                                    <label for="det-phone" class="sr-only">Número de teléfono</label>
                                    <i data-lucide="phone" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                                    <input type="tel" name="phone" id="det-phone" class="w-full pl-10 pr-4 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner" placeholder="ej. +34 600 000 000">
                                </div>
                            </fieldset>

                             <!-- Notas -->
                            <div class="pt-6 border-t border-zinc-800/50">
                                <label for="det-message" class="block text-[14px] font-bold text-zinc-100 mb-3">Notas y Contexto</label>
                                <!-- Vista Renderizada (para Odoo y HTML) -->
                                <div id="det-message-rich" class="w-full bg-zinc-900/50 border border-zinc-800 rounded-lg p-4 mb-4 overflow-x-auto text-[14px] text-zinc-300 leading-relaxed max-h-[500px] overflow-y-auto hidden selection:bg-indigo-500/30"></div>
                                
                                <textarea name="message" id="det-message" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none min-h-[140px] text-[14px] text-zinc-100 transition-colors placeholder:text-zinc-600 shadow-inner" placeholder="Añade aquí los detalles de la reunión, necesidades del cliente..."></textarea>
                                
                                <style>
                                    /* Sobrescribir fondos blancos de Odoo y tablas para que se vean bien en modo oscuro */
                                    #det-message-rich * { background-color: transparent !important; color: inherit !important; border-color: #3f3f46 !important; }
                                    #det-message-rich table { width: 100% !important; border-collapse: collapse; margin-bottom: 1rem; }
                                    #det-message-rich td { padding: 8px !important; vertical-align: top; }
                                    #det-message-rich h3 { color: #818cf8 !important; border-bottom: 2px solid #312e81 !important; margin: 1.5rem 0 1rem 0; padding-bottom: 4px; font-size: 15px; text-transform: uppercase; letter-spacing: 0.05em; }
                                    #det-message-rich strong { color: #f4f4f5 !important; }
                                </style>
                            </div>
                        </div>

                        <!-- Columna Derecha (Secundaria) -->
                        <div class="md:col-span-5 space-y-8 bg-zinc-950 p-6 rounded-lg border border-zinc-800 shadow-inner">
                            <div>
                                <label for="det-source" class="block text-[13px] font-semibold text-zinc-300 mb-2">Canal de Adquisición</label>
                                <div class="relative">
                                    <select name="source" id="det-source" class="w-full bg-zinc-900 border border-zinc-800 rounded-md py-2.5 pl-3 pr-8 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors appearance-none">
                                        <option value="organico">Tráfico Orgánico / Directo</option>
                                        <option value="pago">Márketing de Pago (Ads)</option>
                                    </select>
                                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                                </div>
                            </div>
                            
                            <!-- Archivos del Lead -->
                            <div>
                                <h3 class="block text-[13px] font-semibold text-zinc-300 mb-3">Archivos Adjuntos</h3>
                                
                                <div id="det-files-container" class="space-y-3 mb-4 empty:hidden">
                                    <!-- Dynamic File Link -->
                                    <a id="det-file-link" href="#" target="_blank" class="hidden items-center gap-3 p-3 bg-zinc-900 border border-zinc-800 hover:border-indigo-500/50 rounded-md transition-colors group">
                                        <div class="w-8 h-8 rounded bg-indigo-900/30 text-indigo-400 flex items-center justify-center shrink-0">
                                            <i data-lucide="file-text" class="w-4 h-4" aria-hidden="true"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="text-[13px] font-medium text-zinc-200 truncate group-hover:text-indigo-300 transition-colors" id="det-file-name">Documento</p>
                                            <p class="text-[11px] text-zinc-500 uppercase tracking-widest mt-0.5">Ver archivo</p>
                                        </div>
                                    </a>
                                    <!-- Dynamic Audio Link -->
                                    <a id="det-audio-link" href="#" target="_blank" class="hidden items-center gap-3 p-3 bg-zinc-900 border border-zinc-800 hover:border-emerald-500/50 rounded-md transition-colors group">
                                        <div class="w-8 h-8 rounded bg-emerald-900/30 text-emerald-400 flex items-center justify-center shrink-0">
                                            <i data-lucide="mic" class="w-4 h-4" aria-hidden="true"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="text-[13px] font-medium text-zinc-200 truncate group-hover:text-emerald-300 transition-colors" id="det-audio-name">Nota de Voz</p>
                                            <p class="text-[11px] text-zinc-500 uppercase tracking-widest mt-0.5">Escuchar grabación</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="space-y-3">
                                    <div class="relative">
                                        <label for="det-new-doc" class="block text-[12px] text-zinc-400 mb-2">Subir Nuevo Archivo (PDF, DOCX, Audio...)</label>
                                        <input type="file" name="lead_file" id="det-new-doc" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.zip,audio/*" class="block w-full text-[12px] text-zinc-400 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[12px] file:font-bold file:bg-zinc-800 file:text-zinc-100 hover:file:bg-zinc-700 transition-colors cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <!-- Etiquetas -->
                            <div>
                                <label for="det-tags" class="block text-[13px] font-semibold text-zinc-300 mb-2">Clasificación / Etiquetas (separadas por coma)</label>
                                <input type="text" name="tags" id="det-tags" placeholder="ej. vip, urgente" class="w-full bg-zinc-900 border border-zinc-800 rounded-md py-2.5 px-3 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors mb-3">
                                <div class="flex flex-wrap gap-2" role="group" aria-label="Etiquetas sugeridas">
                                    <?php foreach($existingTags as $tag): ?>
                                        <button type="button" onclick="addTagEdit('<?php echo htmlspecialchars($tag); ?>')" class="px-2.5 py-1 bg-zinc-800 border border-zinc-700 hover:bg-zinc-700 rounded-md text-[12px] font-bold text-zinc-300 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 shadow-sm">
                                            + <?php echo htmlspecialchars($tag); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER MODAL -->
                <div class="px-8 py-5 border-t border-zinc-800 flex items-center justify-between bg-zinc-950 shrink-0">
                    <button type="button" onclick="deleteLead()" class="text-red-400 hover:text-red-300 text-[14px] font-semibold transition-colors flex items-center gap-2 focus-visible:outline focus-visible:outline-2 focus-visible:outline-red-500 rounded-md px-2 py-2">
                        <i data-lucide="trash-2" class="w-4 h-4" aria-hidden="true"></i> Eliminar Registro
                    </button>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDetailModal()" class="px-5 py-2.5 text-zinc-400 text-[14px] font-semibold rounded-md hover:text-zinc-100 hover:bg-zinc-800 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-zinc-500">Cancelar</button>
                        <button type="submit" id="updateBtn" class="px-6 py-2.5 bg-zinc-100 hover:bg-zinc-300 text-zinc-950 text-[14px] font-bold rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 shadow-sm flex items-center gap-2">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include_once 'modal-add-lead.php'; ?>

    <script>
        lucide.createIcons();

        function addTagEdit(tag) {
            const el = document.getElementById('det-tags');
            let vals = el.value.split(',').map(v => v.trim()).filter(v => v !== "");
            if(!vals.includes(tag)) {
                vals.push(tag);
                el.value = vals.join(', ');
            }
        }

        /* Accessibile Filtering via JS */
        const globalInp = document.getElementById('filterGlobal');
        const statusSel = document.getElementById('filterStatus');
        const sourceSel = document.getElementById('filterSource');
        const rows = document.querySelectorAll('.lead-row');
        const countSpan = document.getElementById('visibleLeadsCount');

        function normalizeStr(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
        }

        function applyFilters() {
            const query = normalizeStr(globalInp.value);
            const status = statusSel.value;
            const source = sourceSel.value;

            let visible = 0;
            rows.forEach(r => {
                const text = normalizeStr(r.innerText);
                const rStatus = r.dataset.status;
                const rSrc = r.dataset.source;

                const matchText = text.includes(query);
                const matchStatus = (status === 'all' || rStatus === status);
                const matchSrc = (source === 'all' || rSrc === source);

                const isV = matchText && matchStatus && matchSrc;
                r.style.display = isV ? '' : 'none';
                if(isV) visible++;
            });
            countSpan.textContent = visible;
        }

        [globalInp, statusSel, sourceSel].forEach(el => {
            el.addEventListener('input', applyFilters);
        });

        function quickEdit(el, field, id) {
            if (el.dataset.editing === 'true') return;
            el.dataset.editing = 'true';
            
            const originalContent = el.innerHTML;
            let input;

            if (field === 'status') {
                input = document.createElement('select');
                input.className = 'bg-zinc-900 text-zinc-100 text-[12px] rounded border border-zinc-700 px-1 py-0.5';
                const options = [
                    {val: 'nuevo', label: 'Nuevo'},
                    {val: 'no_responde', label: 'Sin Respuesta'},
                    {val: 'llamar_tarde', label: 'Llamar más tarde'},
                    {val: 'interesado_tarde', label: 'Interesado (Futuro)'},
                    {val: 'enviar_propuesta', label: 'Enviar Propuesta'},
                    {val: 'propuesta_enviada', label: 'Propuesta Enviada'},
                    {val: 'ganado', label: 'Ganado'},
                    {val: 'perdido', label: 'Perdido'},
                    {val: 'no_cualificado', label: 'No Cualificado'}
                ];
                options.forEach(opt => {
                    const o = document.createElement('option');
                    o.value = opt.val; o.textContent = opt.label;
                    if (el.closest('tr').dataset.status === opt.val) o.selected = true;
                    input.appendChild(o);
                });
            } else if (field === 'source') {
                input = document.createElement('select');
                input.className = 'bg-zinc-900 text-zinc-100 text-[12px] rounded border border-zinc-700 px-1 py-0.5';
                [['organico','Orgánico'],['pago','Pago']].forEach(opt => {
                    const o = document.createElement('option');
                    o.value = opt[0]; o.textContent = opt[1];
                    if (el.closest('tr').dataset.source === opt[0]) o.selected = true;
                    input.appendChild(o);
                });
            } else {
                input = document.createElement('input');
                input.type = (field === 'proposal_price') ? 'number' : (field === 'created_at' ? 'date' : 'text');
                input.value = el.innerText.trim().replace('€', '').replace(',', '');
                input.className = 'bg-zinc-900 text-zinc-100 text-[13px] rounded border border-zinc-700 px-2 py-1 w-full max-w-[120px]';
            }

            el.innerHTML = '';
            el.appendChild(input);
            input.focus();

            const save = () => {
                const newValue = input.value;
                const formData = new FormData();
                formData.append('id', id);
                formData.append('field', field);
                formData.append('value', newValue);

                fetch('api_quick_update.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            location.reload(); // Recarga simple para actualizar badges y demás sin complicar el JS
                        } else {
                            el.innerHTML = originalContent;
                            delete el.dataset.editing;
                        }
                    });
            };

            input.onblur = save;
            if (input.tagName === 'INPUT') {
                input.onkeydown = (e) => { if (e.key === 'Enter') save(); if (e.key === 'Escape') { el.innerHTML = originalContent; delete el.dataset.editing; } };
            }
        }

        /* Accessible Modal Focus Trapping / Handling */
        const modalD = document.getElementById('detailModal');
        const modalContent = document.getElementById('detailModalContent');
        const editLeadForm = document.getElementById('editLeadForm');
        let previousActiveElement;
        
        function showLeadDetails(data) {
            previousActiveElement = document.activeElement; 
            
            document.getElementById('det-id').value = data.id || '';
            document.getElementById('det-name').value = data.name || '';
            document.getElementById('det-company').value = data.company || '';
            document.getElementById('det-website').value = data.website || '';
            document.getElementById('det-email').value = data.email || '';
            document.getElementById('det-phone').value = data.phone || '';
            document.getElementById('det-tags').value = data.tags || '';
            document.getElementById('det-source').value = data.source || 'organico';
            document.getElementById('det-status').value = data.status || 'nuevo';
            document.getElementById('det-price').value = data.proposal_price || 0;
            const msgInp = document.getElementById('det-message');
            const msgRich = document.getElementById('det-message-rich');
            const msgVal = data.message || '';
            msgInp.value = msgVal;
            
            // Si contiene HTML (Odoo), mostrar el renderizado y ocultar el textarea
            if (msgVal.includes('<') && msgVal.includes('>')) {
                msgRich.innerHTML = msgVal;
                msgRich.classList.remove('hidden');
                msgInp.classList.add('hidden'); 
            } else {
                msgRich.classList.add('hidden');
                msgInp.classList.remove('hidden');
            }
            
            // Formatear fecha y hora por separado para mejor UX
            if(data.created_at) {
                const parts = data.created_at.split(' ');
                document.getElementById('det-date-only').value = parts[0];
                document.getElementById('det-time-only').value = parts[1].substring(0, 5);
            }
            
            // Handle Document display (Unified)
            const fileLink = document.getElementById('det-file-link');
            if(data.file_path && data.file_path !== "") {
                const fileNameOnly = data.file_path.split('/').pop();
                fileLink.href = 'download.php?file=' + fileNameOnly;
                document.getElementById('det-file-name').textContent = fileNameOnly || 'Documento';
                fileLink.classList.remove('hidden');
                fileLink.classList.add('flex');
            } else {
                fileLink.classList.add('hidden');
                fileLink.classList.remove('flex');
            }
 
            // Handle Audio display
            const audioLink = document.getElementById('det-audio-link');
            if(data.audio_path && data.audio_path !== "") {
                const audioNameOnly = data.audio_path.split('/').pop();
                audioLink.href = 'download.php?file=' + audioNameOnly;
                document.getElementById('det-audio-name').textContent = audioNameOnly || 'Grabación de Audio';
                audioLink.classList.remove('hidden');
                audioLink.classList.add('flex');
            } else {
                audioLink.classList.add('hidden');
                audioLink.classList.remove('flex');
            }

            modalD.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
            
            setTimeout(() => { document.getElementById('det-name').focus(); }, 50);
        }

        function closeDetailModal() {
            modalD.classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (previousActiveElement) {
                previousActiveElement.focus();
            }
        }

        function deleteLead() {
            const id = document.getElementById('det-id').value;
            if(!confirm('¿Estás seguro de que quieres eliminar este registro de forma permanente?')) return;
            const fd = new FormData();
            fd.append('id', id);
            fd.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            fetch('delete_lead', { method: 'POST', body: fd })
            .then(r => r.json()).then(res => {
                if(res.success) location.reload();
            });
        }

        editLeadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Combinar fecha y hora antes de enviar
            const d = document.getElementById('det-date-only').value;
            const t = document.getElementById('det-time-only').value || '00:00:00';
            if(d) document.getElementById('det-full-date').value = d + ' ' + t + ':00';

            const updateBtn = document.getElementById('updateBtn');
            updateBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Guardando...';
            updateBtn.classList.add('opacity-75', 'cursor-not-allowed');
            lucide.createIcons();

            const fd = new FormData(this);
            fetch('update_lead', { method: 'POST', body: fd })
            .then(async response => {
                const text = await response.text(); 
                try {
                    const res = JSON.parse(text);
                    if(res.success) {
                        location.reload();
                    } else {
                        alert('Error del servidor: ' + (res.message || 'Error desconocido'));
                        updateBtn.innerHTML = 'Guardar Cambios';
                        updateBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                } catch (e) {
                    console.error('La respuesta no es un JSON válido:', text);
                    // Si se ha guardado pero falla el JSON, es muy probable que haya guardado (como dices), así que refrescamos igualmente.
                    if (text.includes('"success":true')) {
                        location.reload();
                    } else {
                        alert('Ocurrió un error en la respuesta del servidor. Revisa la consola o refresca.');
                        updateBtn.innerHTML = 'Guardar Cambios';
                        updateBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                }
            })
            .catch((err) => {
                console.error('Error de red:', err);
                alert('Ocurrió un error en la conexión');
                updateBtn.innerHTML = 'Guardar Cambios';
                updateBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        });

        // Close on ESC and backdrop click
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modalD.classList.contains('hidden')) {
                closeDetailModal();
            }
        });
        
        modalD.addEventListener('click', (e) => {
            if (e.target === modalD) closeDetailModal();
        });
    </script>
    <?php include_once 'modal-add-lead.php'; ?>
</body>
</html>
<?php $conn->close(); ?>
