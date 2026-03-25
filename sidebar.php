<?php
$current_page = basename($_SERVER['PHP_SELF']);
$navItems = [
    ['name' => 'Panel de Control', 'icon' => 'layout', 'href' => 'index', 'id' => 'index.php'],
    ['name' => 'Clientes', 'icon' => 'users', 'href' => 'leads', 'id' => 'leads.php'],
    ['name' => 'Documentos', 'icon' => 'folder', 'href' => 'explorer', 'id' => 'explorer.php'],
    ['name' => 'Estadísticas', 'icon' => 'bar-chart-2', 'href' => 'stats', 'id' => 'stats.php'],
];
?>
<aside id="main-sidebar" aria-label="Navegación Principal" class="hidden sm:flex fixed top-0 left-0 z-40 w-64 h-screen flex-col justify-between bg-zinc-950 border-r border-zinc-800">
  <div class="px-6 py-8">
    <!-- Brand -->
    <a href="index" class="flex items-center gap-3 mb-10 group focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-indigo-500 rounded-md" aria-label="Ir al Panel de Control">
      <div class="w-8 h-8 bg-zinc-100 rounded-md flex items-center justify-center border border-zinc-300 transition-colors group-hover:bg-zinc-200" aria-hidden="true">
        <i data-lucide="triangle" class="w-5 h-5 text-zinc-950 fill-zinc-950"></i>
      </div>
      <div>
        <h2 class="text-[16px] font-bold text-zinc-100 tracking-tight leading-none">CRM Marcloi</h2>
        <div class="flex items-center gap-1.5 mt-1.5">
          <span class="flex h-1 w-1 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="text-[9px] font-mono font-bold text-zinc-500 uppercase tracking-widest bg-zinc-900 px-1 py-0.5 rounded border border-zinc-800/50">v.8436b70</span>
        </div>
      </div>
    </a>

    <nav aria-label="Menú Lateral" class="space-y-1">
      <h3 class="sr-only">Menú Principal</h3>
      <ul class="space-y-1 m-0 p-0 list-none">
        <?php foreach ($navItems as $item): 
            $isActive = ($current_page == $item['id']);
        ?>
          <li>
            <a href="<?php echo $item['href']; ?>" 
               <?php echo $isActive ? 'aria-current="page"' : ''; ?>
               class="group flex items-center gap-3 px-3 py-2 rounded-md transition-colors text-[14px] font-medium <?php echo $isActive ? 'bg-zinc-800 text-zinc-100' : 'text-zinc-400 hover:bg-zinc-900 hover:text-zinc-200'; ?> focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
              <i data-lucide="<?php echo $item['icon']; ?>" class="w-4.5 h-4.5 <?php echo $isActive ? 'text-zinc-100' : 'text-zinc-500 group-hover:text-zinc-300'; ?>" aria-hidden="true"></i>
              <?php echo $item['name']; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>

  <div class="p-6 border-t border-zinc-900">
    <button onclick="toggleModal()" aria-haspopup="dialog" aria-expanded="false" aria-controls="addLeadModal" class="w-full flex items-center justify-center gap-2 py-2 bg-zinc-100 rounded-md text-[14px] font-bold text-zinc-950 hover:bg-zinc-300 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 mb-6">
      <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
      Añadir Registro
    </button>

    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full overflow-hidden border border-zinc-700 bg-zinc-800" aria-hidden="true">
          <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Admin" alt="" class="w-full h-full object-cover">
        </div>
        <div class="flex flex-col">
          <span class="text-[14px] font-semibold text-zinc-200 leading-tight">Administrador</span>
          <span class="text-[12px] text-zinc-500 leading-tight block">admin@marcloi.es</span>
        </div>
      </div>
      <a href="logout" aria-label="Cerrar Sesión" class="p-2 text-zinc-500 hover:text-zinc-200 transition-colors rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
        <i data-lucide="log-out" class="w-4 h-4" aria-hidden="true"></i>
      </a>
    </div>
  </div>
</aside>
