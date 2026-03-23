<?php require_once 'auth.php'; ?>
<?php
require_once 'db.php';
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Lista de Leads - CRM MarcLoic</title>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <h1>Gestión de Leads</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Listado completo de clientes potenciales registrados en el sistema.</p>

        <div class="glass-panel">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Mensaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;"><?php echo date('d/m/y H:i', strtotime($row['created_at'])); ?></td>
                            <td style="font-weight: 600; color: white;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><span style="background: rgba(99, 102, 241, 0.1); color: #a5b4fc; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.85rem; border: 1px solid rgba(99, 102, 241, 0.2);"><?php echo htmlspecialchars($row['email']); ?></span></td>
                            <td style="color: var(--text-muted); font-weight: 500;"><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #94a3b8;"><?php echo htmlspecialchars($row['message']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if ($result->num_rows == 0): ?>
                    <p style="text-align: center; padding: 3rem; color: var(--text-muted);">Aún no tienes leads registrados. <a href="add-lead.php" style="color: var(--primary);">Registra el primero aquí</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
<?php $conn->close(); ?>
