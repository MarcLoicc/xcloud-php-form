<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<!-- Tailwind Configuration & Theme Support -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          bg: '#f8fafc',
          card: '#ffffff',
          sidebar: '#ffffff',
          border: '#e2e8f0',
          primary: '#4f46e5',
          'text-main': '#0f172a',
          'text-muted': '#64748b'
        },
        fontFamily: {
          sans: ['Outfit', 'sans-serif'],
        },
      }
    }
  }
</script>

<aside class="fixed left-0 top-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white border-r border-slate-200">
   <div class="h-full px-6 py-10 overflow-y-auto flex flex-col">
      <a href="index" class="flex items-center ps-2 mb-10 group">
         <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
            <i data-lucide="zap" class="w-5 h-5 text-white"></i>
         </div>
         <span class="self-center text-xl font-bold whitespace-nowrap text-slate-900 ml-3 tracking-tight">CRM <span class="text-indigo-600">Pro</span></span>
      </a>
      
      <ul class="space-y-1.5 font-medium flex-1">
         <?php 
            $current_page = basename($_SERVER['PHP_SELF']); 
            $nav_items = [
                ['url' => 'index', 'icon' => 'layout-dashboard', 'label' => 'Dashboard', 'match' => 'index.php'],
                ['url' => 'leads', 'icon' => 'layers', 'label' => 'Leads', 'match' => 'leads.php'],
                ['url' => 'explorer', 'icon' => 'folder', 'label' => 'Archivos', 'match' => 'explorer.php']
            ];
            foreach($nav_items as $item):
                $is_active = ($current_page == $item['match']);
         ?>
         <li>
            <a href="<?php echo $item['url']; ?>" class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo $is_active ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'; ?>">
               <i data-lucide="<?php echo $item['icon']; ?>" class="w-4 h-4 <?php echo $is_active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600'; ?>"></i>
               <span class="ms-3 text-sm font-semibold"><?php echo $item['label']; ?></span>
            </a>
         </li>
         <?php endforeach; ?>
      </ul>

      <div class="pt-4 mt-4 border-t border-slate-100">
         <a href="?logout=1" class="flex items-center px-4 py-3 text-slate-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all group">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span class="ms-3 text-sm font-semibold">Cerrar Sesión</span>
         </a>
      </div>
   </div>
</aside>

<!-- Cargar el Modal en todas las páginas donde esté el sidebar -->
<?php include 'modal-add-lead.php'; ?>
<script>lucide.createIcons();</script>
