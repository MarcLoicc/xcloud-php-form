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
                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        
        <div class="footer">
            <a href="index.html" class="back-link">Volver al Formulario</a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
