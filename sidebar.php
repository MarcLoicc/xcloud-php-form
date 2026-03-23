<!-- Tailwind CSS & Lucide Icons -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          dark: '#09090b',
          'dark-card': '#18181b',
          'dark-border': '#27272a',
          accent: '#0066FF', // Azul Eléctrico Pro
        },
        fontFamily: {
          sans: ['Outfit', 'sans-serif'],
        },
      }
    }
  }
</script>

<aside class="fixed left-0 top-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-zinc-950 border-r border-zinc-900">
   <div class="h-full px-6 py-10 overflow-y-auto flex flex-col">
      <a href="index.php" class="flex items-center ps-2.5 mb-10 group">
         <div class="p-2 bg-blue-600/10 rounded-lg group-hover:bg-blue-600/20 transition-all">
            <i data-lucide="command" class="w-6 h-6 text-blue-500"></i>
         </div>
         <span class="self-center text-xl font-bold whitespace-nowrap text-white ml-3 tracking-tight uppercase">CRM PRO</span>
      </a>
      
      <ul class="space-y-4 font-bold flex-1">
         <li>
            <a href="index.php" class="flex items-center p-4 text-zinc-500 rounded-2xl hover:bg-zinc-900 hover:text-white group transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-zinc-900 text-white border border-zinc-800' : ''; ?>">
               <i data-lucide="layout-grid" class="w-5 h-5 transition duration-75"></i>
               <span class="ms-3 uppercase tracking-widest text-[10px]">Dashboard</span>
            </a>
         </li>
         <li>
            <a href="leads.php" class="flex items-center p-4 text-zinc-500 rounded-2xl hover:bg-zinc-900 hover:text-white group transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'bg-zinc-900 text-white border border-zinc-800' : ''; ?>">
               <i data-lucide="users" class="w-5 h-5 transition duration-75"></i>
               <span class="ms-3 uppercase tracking-widest text-[10px]">Lista de Leads</span>
            </a>
         </li>
         <li>
            <a href="explorer.php" class="flex items-center p-4 text-zinc-500 rounded-2xl hover:bg-zinc-900 hover:text-white group transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'explorer.php' ? 'bg-zinc-900 text-white border border-zinc-800' : ''; ?>">
               <i data-lucide="folder-search" class="w-5 h-5 transition duration-75"></i>
               <span class="ms-3 uppercase tracking-widest text-[10px]">Archivo Maestro</span>
            </a>
         </li>
      </ul>


      <div class="pt-4 mt-4 border-t border-dark-border">
         <a href="?logout=1" class="flex items-center p-3 text-red-400/80 rounded-xl hover:bg-red-500/10 hover:text-red-400 transition-all group">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            <span class="ms-3">Desconectar</span>
         </a>
      </div>
   </div>
</aside>

<!-- Cargar el Modal en todas las páginas donde esté el sidebar -->
<?php include 'modal-add-lead.php'; ?>

<script>lucide.createIcons();</script>
