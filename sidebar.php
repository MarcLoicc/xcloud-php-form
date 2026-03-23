<nav class="sidebar">
    <div class="sidebar-logo">
        <i data-lucide="command" style="color: var(--accent);"></i>
        <span>CRM Pro</span>
    </div>
    
    <ul class="nav-links">
        <li>
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i data-lucide="layout-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="leads.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'active' : ''; ?>">
                <i data-lucide="users-2"></i>
                <span>Leads</span>
            </a>
        </li>
        <li>
            <a href="add-lead.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add-lead.php' ? 'active' : ''; ?>">
                <i data-lucide="plus-circle"></i>
                <span>Nuevo Registro</span>
            </a>
        </li>
    </ul>
    
    <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid var(--border);">
        <a href="?logout=1" class="logout-btn" style="width: 100%; border: none; background: transparent; display: flex; align-items: center; gap: 10px; padding: 10px; cursor: pointer; text-decoration: none;">
            <i data-lucide="log-out" style="width: 18px;"></i>
            <span>Desconectar</span>
        </a>
    </div>
</nav>

<link rel="stylesheet" href="style.css?v=1.0.3">
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
