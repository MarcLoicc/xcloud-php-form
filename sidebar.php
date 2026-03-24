<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<!-- Tailwind CSS & Lucide Icons -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          bg: '#09090b',
          card: '#121214',
          border: '#1f1f23',
          primary: '#3b82f6',
        },
        fontFamily: {
          sans: ['Outfit', 'sans-serif'],
        },
      }
    }
  }
</script>

<aside class="fixed left-0 top-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-bg border-r border-border">
   <div class="h-full px-6 py-10 overflow-y-auto flex flex-col">
      <a href="index.php" class="flex items-center ps-2 mb-10 group">
         <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center shadow-lg shadow-primary/20">
            <i data-lucide="zap" class="w-5 h-5 text-white"></i>
         </div>
         <span class="self-center text-lg font-bold whitespace-nowrap text-white ml-3 tracking-tight">CRM Premium</span>
      </a>
      
      <ul class="space-y-1 font-medium flex-1">
         <li>
            <a href="index.php" class="flex items-center px-4 py-3 text-zinc-400 rounded-xl hover:bg-zinc-900/50 hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-zinc-900 text-white' : ''; ?>">
               <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
               <span class="ms-3 text-sm">Dashboard</span>
            </a>
         </li>
         <li>
            <a href="leads.php" class="flex items-center px-4 py-3 text-zinc-400 rounded-xl hover:bg-zinc-900/50 hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'bg-zinc-900 text-white' : ''; ?>">
               <i data-lucide="layers" class="w-4 h-4"></i>
               <span class="ms-3 text-sm">Leads</span>
            </a>
         </li>
         <li>
            <a href="explorer.php" class="flex items-center px-4 py-3 text-zinc-400 rounded-xl hover:bg-zinc-900/50 hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'explorer.php' ? 'bg-zinc-900 text-white' : ''; ?>">
               <i data-lucide="folder" class="w-4 h-4"></i>
               <span class="ms-3 text-sm">Archivos</span>
            </a>
         </li>
      </ul>


      <div class="pt-4 mt-4 border-t border-border">
         <a href="?logout=1" class="flex items-center px-4 py-3 text-zinc-500 rounded-xl hover:bg-red-500/5 hover:text-red-400 transition-all group">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span class="ms-3 text-sm">Cerrar Sesión</span>
         </a>
      </div>
   </div>
</aside>


<!-- Cargar el Modal en todas las páginas donde esté el sidebar -->
<?php include 'modal-add-lead.php'; ?>

<script>lucide.createIcons();</script>
