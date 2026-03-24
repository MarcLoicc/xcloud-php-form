<?php
$current_page = basename($_SERVER['PHP_SELF']);
$navItems = [
    ['name' => 'Monitor', 'icon' => 'layout-dashboard', 'href' => 'index.php', 'id' => 'index.php'],
    ['name' => 'Leads', 'icon' => 'users', 'href' => 'leads.php', 'id' => 'leads.php'],
    ['name' => 'Archivo Maestro', 'icon' => 'folder-open', 'href' => 'explorer.php', 'id' => 'explorer.php'],
];
?>
<aside id="main-sidebar" class="hidden sm:flex fixed top-0 left-0 z-40 w-64 h-screen flex-col justify-between sidebar-serious shadow-2xl">
  <div class="px-5 py-8">
    <!-- Brand -->
    <div class="flex items-center gap-3 mb-12 px-2 group cursor-pointer">
      <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-100 group-hover:rotate-6 transition-all">
        <i data-lucide="shield-check" class="w-6 h-6 text-white stroke-[2.5]"></i>
      </div>
      <div>
        <h2 class="text-sm font-black text-white tracking-widest leading-none uppercase">CRM MARCLOI</h2>
        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.4em] mt-2 block opacity-80">ESTADO: SEGURO</span>
      </div>
    </div>

    <nav class="space-y-2">
      <?php foreach ($navItems as $item): 
          $isActive = ($current_page == $item['id']);
      ?>
        <a href="<?php echo $item['href']; ?>" class="group flex items-center justify-between px-4 py-3.5 rounded-lg transition-all border <?php echo $isActive ? 'bg-indigo-600 text-white border-indigo-500 shadow-xl' : 'text-slate-400 border-transparent hover:bg-slate-800 hover:text-white hover:border-slate-700'; ?>">
          <div class="flex items-center gap-4">
            <i data-lucide="<?php echo $item['icon']; ?>" class="w-4.5 h-4.5 transition-all <?php echo $isActive ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400'; ?> stroke-[2.5]"></i>
            <span class="text-[13px] font-bold tracking-tight uppercase"><?php echo $item['name']; ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="p-6 border-t border-slate-800 bg-slate-900/50">
    <button onclick="toggleModal()" class="w-full flex items-center justify-center gap-3 py-4 bg-white rounded-lg text-xs font-black text-slate-950 hover:bg-slate-100 transition-all shadow-xl active:scale-95 group uppercase tracking-widest mb-6 border border-white">
      <i data-lucide="plus" class="w-4.5 h-4.5 group-hover:rotate-90 transition-all"></i>
      REGISTRO MASTER
    </button>

    <div class="flex items-center justify-between p-4 bg-slate-800/50 border border-slate-700/50 rounded-xl shadow-inner">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-slate-700 rounded-lg flex items-center justify-center overflow-hidden border border-slate-600">
          <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marc" alt="User" class="w-full h-full object-cover">
        </div>
        <div class="flex flex-col">
          <span class="text-[11px] font-bold text-white tracking-tight uppercase">Marco Loi</span>
          <span class="text-[8px] font-black text-indigo-400 uppercase tracking-widest block mt-0.5">DEV ADMIN</span>
        </div>
      </div>
      <a href="logout.php" class="p-2 text-slate-500 hover:text-red-400 transition-all">
        <i data-lucide="log-out" class="w-4 h-4"></i>
      </a>
    </div>
  </div>
</aside>
