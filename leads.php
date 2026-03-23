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
    <!-- Bloqueo total para buscadores e IA -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <!-- Evitar caché en el navegador -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>Lista de Leads - xCloud PHP CRM</title>
    <!-- Versionado del CSS para forzar actualización -->
    <link rel="stylesheet" href="style.css?v=1.0.1">
</head>
<body>
    <div class="container leads-page">
        <header>
            <h1>📊 Registro de Leads</h1>
            <p>Lista de prospectos de MariaDB (xCloud)</p>
            <a href="?logout=1" class="logout-btn">Cerrar Sesión 🔒</a>
        </header>

        <section class="leads-list">
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
                        <td><span style="background: rgba(99, 102, 241, 0.1); color: #a5b4fc; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.85rem;"><?php echo htmlspecialchars($row['email']); ?></span></td>
                        <td style="color: var(--text-muted);"><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #cbd5e1;"><?php echo htmlspecialchars($row['message']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        
        <div class="footer">
            <a href="index.php" class="back-link">Volver al Formulario</a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
