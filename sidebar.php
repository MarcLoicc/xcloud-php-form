<nav class="sidebar">
    <div class="sidebar-logo">
        <i data-lucide="layout-dashboard"></i>
        <span>CRM MarcLoic</span>
    </div>
    <ul class="nav-links">
        <li>
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i data-lucide="bar-chart-3"></i>
                <span>Panel Principal</span>
            </a>
        </li>
        <li>
            <a href="leads.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'active' : ''; ?>">
                <i data-lucide="users"></i>
                <span>Lista de Leads</span>
            </a>
        </li>
        <li>
            <a href="add-lead.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add-lead.php' ? 'active' : ''; ?>">
                <i data-lucide="user-plus"></i>
                <span>Nuevo Registro</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <a href="?logout=1" class="logout-btn">
            <i data-lucide="log-out"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</nav>

<link rel="stylesheet" href="style.css?v=1.0.3">
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
