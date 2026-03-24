<?php
$current_page = basename($_SERVER['PHP_SELF']);
$navItems = [
    ['name' => 'Monitor', 'icon' => 'layout-dashboard', 'href' => 'index.php', 'id' => 'index.php'],
    ['name' => 'Leads', 'icon' => 'users', 'href' => 'leads.php', 'id' => 'leads.php'],
    ['name' => 'Archivo Maestro', 'icon' => 'folder-open', 'href' => 'explorer.php', 'id' => 'explorer.php'],
    ['name' => 'Seguridad', 'icon' => 'shield-check', 'href' => '#', 'id' => 'security'],
];
?>
<aside id="main-sidebar" class="hidden sm:flex fixed top-0 left-0 z-40 w-72 h-screen p-6 flex-col justify-between border-r border-white/60 bg-white/30 backdrop-blur-3xl shadow-[50px_0_100px_-50px_rgba(30,41,59,0.05)] transition-all overflow-y-auto">
  <div>
    <!-- Brand -->
    <div class="flex items-center gap-4 mb-14 px-2">
      <div class="w-14 h-14 bg-indigo-600 rounded-[2rem] flex items-center justify-center transform hover:rotate-6 transition-all shadow-xl shadow-indigo-100 ring-4 ring-white/40">
        <i data-lucide="shield-check" class="w-8 h-8 text-white stroke-[2.5]"></i>
      </div>
      <div>
        <h2 class="text-xl font-black text-slate-800 tracking-tighter leading-none italic">CRM <span class="text-indigo-600 not-italic">MARCLOI</span></h2>
        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-100/60 px-2 py-0.5 rounded-full mt-1.5 inline-block">Enterprise 5.4</span>
      </div>
    </div>

    <nav class="space-y-3">
      <?php foreach ($navItems as $item): 
          $isActive = ($current_page == $item['id']);
      ?>
        <a href="<?php echo $item['href']; ?>" class="group relative flex items-center justify-between px-5 py-4 rounded-3xl transition-all <?php echo $isActive ? 'bg-indigo-600 shadow-2xl shadow-indigo-200' : 'hover:bg-white/50 border border-transparent hover:border-white/80'; ?>">
          <div class="flex items-center gap-4">
            <i data-lucide="<?php echo $item['icon']; ?>" class="w-6 h-6 transition-all <?php echo $isActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-500'; ?> stroke-[2.5]"></i>
            <span class="text-[13px] font-black transition-all <?php echo $isActive ? 'text-white' : 'text-slate-500 group-hover:text-slate-900'; ?> uppercase tracking-tight"><?php echo $item['name']; ?></span>
          </div>
          <?php if ($item['name'] == 'Leads'): ?>
            <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-indigo-50 px-2 text-[9px] font-black text-indigo-600 ring-2 ring-white/10 group-hover:bg-indigo-600 group-hover:text-white transition-all">
              24
            </span>
          <?php endif; ?>
          <?php if ($isActive): ?>
            <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-8 bg-white rounded-full"></div>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="space-y-6 pt-6 border-t border-white/80">
    <button onclick="toggleModal()" class="w-full flex items-center justify-center gap-3 py-5 bg-slate-900 rounded-[2rem] text-xs font-black text-white hover:bg-black transition-all shadow-2xl shadow-slate-200 hover:shadow-indigo-100 active:scale-95 group overflow-hidden relative">
      <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
      <i data-lucide="plus-circle" class="w-5 h-5 group-hover:rotate-90 transition-all opacity-80"></i>
      <span class="tracking-[0.2em] uppercase">Nuevo Registro</span>
    </button>

    <div class="flex items-center justify-between px-2">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 bg-indigo-100 rounded-2xl border-2 border-white flex items-center justify-center overflow-hidden shadow-sm ring-2 ring-indigo-50">
          <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marc" alt="User" class="w-full h-full object-cover">
        </div>
        <div class="flex flex-col">
          <span class="text-[11px] font-black text-slate-800 tracking-tight leading-none uppercase">Loi Marc</span>
          <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1 italic">Master Admin</span>
        </div>
      </div>
      <a href="logout.php" class="p-3 bg-red-50 hover:bg-red-500 hover:text-white text-red-500 rounded-2xl transition-all shadow-sm" title="Salir de Sesión">
        <i data-lucide="log-out" class="w-5 h-5"></i>
      </a>
    </div>
  </div>
</aside>
