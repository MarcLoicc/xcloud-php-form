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
          accent: '#6366f1',
        },
        fontFamily: {
          sans: ['Outfit', 'sans-serif'],
        },
      }
    }
  }
</script>

<aside class="fixed left-0 top-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-dark border-r border-dark-border">
   <div class="h-full px-5 py-8 overflow-y-auto flex flex-col">
      <a href="index.php" class="flex items-center ps-2.5 mb-10 group">
         <div class="p-2 bg-indigo-600/10 rounded-lg group-hover:bg-indigo-600/20 transition-all">
            <i data-lucide="command" class="w-6 h-6 text-indigo-500"></i>
         </div>
         <span class="self-center text-xl font-bold whitespace-nowrap text-white ml-3 tracking-tight">CRM PRO</span>
      </a>
      
      <ul class="space-y-2 font-medium flex-1">
         <li>
            <a href="index.php" class="flex items-center p-3 text-gray-400 rounded-xl hover:bg-dark-card hover:text-white group transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-dark-card text-white border border-dark-border' : ''; ?>">
               <i data-lucide="layout-grid" class="w-5 h-5 transition duration-75"></i>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         <li>
            <a href="leads.php" class="flex items-center p-3 text-gray-400 rounded-xl hover:bg-dark-card hover:text-white group transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'bg-dark-card text-white border border-dark-border' : ''; ?>">
               <i data-lucide="users" class="w-5 h-5 transition duration-75"></i>
               <span class="ms-3">Leads</span>
            </a>
         </li>
         <li>
            <a href="add-lead.php" class="flex items-center p-3 text-gray-400 rounded-xl hover:bg-dark-card hover:text-white group transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'add-lead.php' ? 'bg-dark-card text-white border border-dark-border' : ''; ?>">
               <i data-lucide="plus-circle" class="w-5 h-5 transition duration-75"></i>
               <span class="ms-3">Nuevo Registro</span>
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

<script>lucide.createIcons();</script>
