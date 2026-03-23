<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// Estadísticas Pro
$totalLeads = $conn->query("SELECT COUNT(*) as count FROM leads")->fetch_assoc()['count'];
$todayLeads = $conn->query("SELECT COUNT(*) as count FROM leads WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
// Cálculos simulados para dar sensación de CRM completo
$conversionRate = ($totalLeads > 0) ? round(($todayLeads / $totalLeads) * 100, 1) : 0;
$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>SaaS Dashboard - CRM Pro</title>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Panel de Control</h1>
            <p>Resumen de actividad en tiempo real de tu CRM.</p>
        </div>

        <!-- Estadísticas Pro -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="label">Total Leads</span>
                <span class="value"><?php echo $totalLeads; ?></span>
                <span class="trend up">Total registrado</span>
            </div>
            <div class="stat-card">
                <span class="label">Leads hoy</span>
                <span class="value"><?php echo $todayLeads; ?></span>
                <span class="trend" style="background: rgba(99,102,241,0.1); color: var(--accent);">Nuevos registros</span>
            </div>
            <div class="stat-card">
                <span class="label">Tasa de Registro</span>
                <span class="value"><?php echo $conversionRate; ?>%</span>
                <span class="trend up">↑ Hoy vs Ayer</span>
            </div>
            <div class="stat-card">
                <span class="label">Estado de Sistema</span>
                <span class="value">ACTIVO</span>
                <span class="trend" style="background: rgba(16,185,129,0.1); color: var(--success);">Online</span>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="grid-main">
            <!-- Recent Leads Table -->
            <div class="panel">
                <div class="panel-header">
                    <h2>Última actividad</h2>
                    <a href="leads.php" style="color: var(--accent); text-decoration: none; font-size: 0.85rem; font-weight: 500;">Gestionar todos &rarr;</a>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $recentLeads->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 500;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><span class="badge badge-email"><?php echo htmlspecialchars($row['email']); ?></span></td>
                            <td><span class="badge badge-status">Prospecto</span></td>
                            <td style="color: var(--text-muted); font-size: 0.8rem;"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if ($totalLeads == 0): ?>
                    <p style="text-align: center; padding: 2rem; color: var(--text-muted);">Sin datos disponibles.</p>
                <?php endif; ?>
            </div>

            <!-- Side Widget -->
            <div class="panel" style="background: linear-gradient(180deg, #18181b 0%, #09090b 100%);">
                <div class="icon-box"><i data-lucide="zap" style="color: var(--warning);"></i></div>
                <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">Acción rápida</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">¿Tienes un nuevo lead para registrar manualmente?</p>
                <a href="add-lead.php" class="btn-pro" style="text-align: center; text-decoration: none; display: block; padding: 0.6rem;">Crear Lead +</a>
                
                <hr style="border: 0.5px solid var(--border); margin: 2rem 0;">
                
                <div style="display: flex; gap: 10px; opacity: 0.7;">
                    <i data-lucide="shield-check" style="width: 16px; color: var(--success);"></i>
                    <p style="font-size: 0.75rem;">Protección CRM Activa</p>
                </div>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
