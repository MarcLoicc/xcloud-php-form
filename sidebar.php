<?php
$current_page = basename($_SERVER['PHP_SELF']);
$navItems = [
    ['name' => 'Monitor', 'icon' => 'layout-dashboard', 'href' => 'index.php', 'id' => 'index.php'],
    ['name' => 'Leads', 'icon' => 'users', 'href' => 'leads.php', 'id' => 'leads.php'],
    ['name' => 'Archivo Maestro', 'icon' => 'folder-open', 'href' => 'explorer.php', 'id' => 'explorer.php'],
];
?>
<aside id="main-sidebar" class="hidden sm:flex fixed top-0 left-0 z-40 w-64 h-screen flex-col justify-between sidebar-serious shadow-sm">
  <div class="px-5 py-8">
    <!-- Brand -->
    <div class="flex items-center gap-3 mb-10 px-2 group cursor-pointer">
      <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center shadow-lg shadow-slate-100 group-hover:scale-105 transition-all">
        <i data-lucide="shield-check" class="w-6 h-6 text-white stroke-[2.5]"></i>
      </div>
      <div>
        <h2 class="text-sm font-bold text-slate-900 tracking-tight leading-none uppercase">CRM MARCLOI</h2>
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 block opacity-60">ADMIN CONSOLE v5</span>
      </div>
    </div>

    <nav class="space-y-1">
      <?php foreach ($navItems as $item): 
          $isActive = ($current_page == $item['id']);
      ?>
        <a href="<?php echo $item['href']; ?>" class="group flex items-center justify-between px-3 py-2.5 rounded-lg transition-all <?php echo $isActive ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 border border-transparent hover:border-slate-100'; ?>">
          <div class="flex items-center gap-3">
            <i data-lucide="<?php echo $item['icon']; ?>" class="w-4.5 h-4.5 transition-all <?php echo $isActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-900'; ?> stroke-[2.5]"></i>
            <span class="text-[12px] font-semibold tracking-tight uppercase"><?php echo $item['name']; ?></span>
          </div>
          <?php if ($item['name'] == 'Leads'): ?>
            <span class="flex h-5 min-w-5 items-center justify-center rounded-md bg-slate-100 px-1.5 text-[9px] font-bold text-slate-600 border border-slate-200 <?php echo $isActive ? 'bg-slate-800 text-white border-transparent' : ''; ?>">
              24
            </span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="p-5 border-t border-slate-100 bg-slate-50/20">
    <button onclick="toggleModal()" class="w-full flex items-center justify-center gap-2 py-3.5 bg-slate-900 rounded-lg text-[10px] font-bold text-white hover:bg-slate-800 transition-all shadow-lg active:scale-95 group uppercase tracking-widest mb-6">
      <i data-lucide="plus" class="w-4 h-4 group-hover:rotate-90 transition-all"></i>
      Nuevo Registro
    </button>

    <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-xl shadow-sm">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-slate-100 rounded-lg border border-slate-100 flex items-center justify-center overflow-hidden">
          <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marc" alt="User" class="w-full h-full object-cover">
        </div>
        <div class="flex flex-col overflow-hidden">
          <span class="text-[10px] font-bold text-slate-900 tracking-tight truncate uppercase">Marco Loi</span>
          <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest block truncate opacity-60">Root Admin</span>
        </div>
      </div>
      <a href="logout.php" class="p-2 text-slate-300 hover:text-red-500 transition-all" title="Cerrar Sesión">
        <i data-lucide="log-out" class="w-4 h-4"></i>
      </a>
    </div>
  </div>
</aside>
