<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';

// Estadísticas rápidas para el dashboard
$totalLeads = $conn->query("SELECT COUNT(*) as count FROM leads")->fetch_assoc()['count'];
$todayLeads = $conn->query("SELECT COUNT(*) as count FROM leads WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$recentLeads = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Dashboard CRM - MarcLoic</title>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <h1>Dashboard Principal</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Seguimiento de actividad y leads corporativos.</p>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <i data-lucide="users" style="color: var(--primary); margin-bottom: 1rem;"></i>
                <h3>Total Leads</h3>
                <span class="value"><?php echo $totalLeads; ?></span>
            </div>
            <div class="stat-card">
                <i data-lucide="calendar" style="color: #10b981; margin-bottom: 1rem;"></i>
                <h3>Hoy</h3>
                <span class="value"><?php echo $todayLeads; ?></span>
            </div>
            <div class="stat-card">
                <i data-lucide="line-chart" style="color: #8b5cf6; margin-bottom: 1rem;"></i>
                <h3>Actividad</h3>
                <span class="value">Saludable</span>
            </div>
        </div>

        <div class="glass-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2>Leads Recientes</h2>
                <a href="leads.php" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">Ver todos &rarr;</a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $recentLeads->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 500;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><span style="background: rgba(99, 102, 241, 0.1); color: #a5b4fc; padding: 0.2rem 0.5rem; border-radius: 4px;"><?php echo htmlspecialchars($row['email']); ?></span></td>
                            <td style="color: var(--text-muted);"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
