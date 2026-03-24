<?php
require_once 'db.php';
header('Content-Type: text/plain');

$leads = [
    ['2026-03-19 12:00:45', 'Joan', 'joantoledo@gmail.com', 0.00, 'enviar_propuesta'],
    ['2026-03-16 13:27:58', 'Alberto', 'proyectos@fordesigner.net', 3950.00, 'enviar_propuesta'],
    ['2026-03-16 11:45:28', 'Gina', 'gina@betropiqa.com', 2450.00, 'propuesta_enviada'],
    ['2026-03-16 09:59:49', 'Iván Ortega', 'consulting14@hotmail.com', 3000.00, 'ganado'],
    ['2026-03-12 17:02:50', 'Paco', 'fgali.arquitectura@gmail.com', 0.00, 'propuesta_enviada'],
    ['2025-10-10 12:43:15', 'Laura Martín', 'laura.martin@cofmservicios31.es', 6835.00, 'perdido'],
    ['2025-10-01 09:49:20', 'Roberto Moreno', 'roberto.moreno@loeches.es', 32065.00, 'perdido'],
    ['2025-09-30 12:16:38', 'Álvaro', 'alvaro.garcia@ayerga.com', 4809.75, 'ganado'],
    ['2025-09-29 16:40:24', 'Luis', '', 544.50, 'ganado'],
    ['2025-08-29 15:28:36', 'José Hormigas', '', 400.00, 'ganado'],
    ['2025-08-29 13:14:45', 'Desconocido', '', 907.50, 'perdido'],
    ['2025-06-24 10:22:02', 'Alfredo Abad', 'pelayo.garcia-conde@sybol.id', 1633.50, 'perdido'],
    ['2025-06-13 13:44:44', 'Antonio Benitez', 'antonio.benitez@administrativando.es', 0.00, 'no_responde'],
    ['2025-05-17 13:12:37', 'Andres Lead Sales', 'ismael@imparables.agency', 0.00, 'no_responde'],
    ['2025-05-17 13:10:44', 'Colaboración', '', 0.00, 'llamar_tarde'],
    ['2025-05-13 12:48:45', 'Luis Castro Martínez', '', 3194.40, 'ganado'],
    ['2025-02-11 18:04:00', 'Meme Alcaraz', 'bymeme14@gmail.com', 3478.75, 'ganado'],
    ['2026-03-24 14:19:57', 'Ana Fexma', 'anafexmacampoamor@gmail.com', 0.00, 'enviar_propuesta'],
    ['2026-03-24 12:17:00', 'José Ángel', 'jachavez10@hotmail.com', 0.00, 'enviar_propuesta'],
    ['2026-03-23 17:17:15', 'Steve Mauricio', 'stevetorres82@hotmail.com', 0.00, 'enviar_propuesta'],
    ['2026-03-16 18:00:09', 'Juanjo', 'info@palmeralcarpinteria.com', 0.00, 'propuesta_enviada'],
    ['2026-02-03 18:16:27', 'Manuel Morales', '', 0.00, 'perdido'],
    ['2025-12-01 13:08:58', 'Jaime', 'jaime020alaman@gmail.com', 3175.00, 'no_responde'],
    ['2025-11-17 17:09:53', 'Antonio', 'antonio.lopez@saurteig.de', 4809.75, 'perdido'],
    ['2025-10-27 10:26:53', 'Almudena laboratorioceranium.com', 'almudena.gavilanes@laboratorioceranium.com', 8197.75, 'perdido'],
    ['2025-10-17 12:02:47', 'Inés María García Lartategui', 'imgarcia@arquimea.com', 1131.35, 'perdido'],
    ['2025-10-09 13:12:58', 'Mercedes García', 'abogada@palomazabalgo.com', 4198.70, 'no_responde'],
    ['2025-09-23 10:41:34', 'Angel', 'angelsl.aero@gmail.com', 2601.50, 'no_responde'],
    ['2025-09-13 21:59:25', 'Silvia Fernández Organista', 'fdez.organista@gmail.com', 5021.50, 'perdido'],
    ['2025-09-10 16:51:59', 'Daniel Calles Giraud', '', 700.00, 'ganado'],
    ['2025-06-13 14:04:47', 'Javier Montero', 'javier@somosguinda.com', 300.00, 'ganado'],
    ['2025-06-10 13:32:30', 'Moncarpe', 'att.cliente@moncarpederechobancario.com', 0.00, 'no_responde'],
    ['2025-02-18 12:42:33', 'Laura Monteagudo', '', 1100.00, 'perdido'],
    ['2025-02-18 09:05:11', 'Andres Medina', 'andres.medina@leadsales.io', 1100.00, 'no_cualificado'],
    ['2026-03-24 12:29:07', 'Alfonso', 'gp3@gp3construcciones.es', 0.00, 'enviar_propuesta'],
    ['2026-03-20 17:34:09', 'María Martín', 'martin.mendez.maria@gmail.com', 0.00, 'enviar_propuesta'],
    ['2026-03-20 08:22:57', 'Sonia', 'sonia.om35@gmail.com', 0.00, 'enviar_propuesta'],
    ['2026-03-19 12:33:15', 'Jordi', 'jordirabassa@hotmail.com', 0.00, 'enviar_propuesta'],
    ['2026-03-17 13:41:18', 'Lead Franklin', 'franklinasilval@gmail.com', 0.00, 'perdido'],
    ['2026-03-16 14:00:47', 'Lead Rolando Galano', 'rgalanou@yahoo.es', 0.00, 'propuesta_enviada'],
    ['2026-02-11 18:16:01', 'IVAN MARTINEZ MARTINEZ', 'ivan.martinez@altomiratrading.com', 0.00, 'no_cualificado'],
    ['2026-02-02 16:37:43', 'Victoria Rodríguez', 'geometriasaltacostura@gmail.com', 0.00, 'no_cualificado'],
    ['2026-01-29 18:09:38', 'Cesar Ruiz', 'cesar1981leganes@gmail.com', 0.00, 'perdido'],
    ['2026-01-28 14:26:22', 'Pepa', 'gcagapito@gmail.com', 0.00, 'perdido'],
    ['2025-12-18 12:33:29', 'Juan Buitrago', '', 0.00, 'no_responde'],
    ['2025-11-27 18:19:38', 'Lina', 'granadoslina2412@gmail.com', 5975.00, 'no_responde'],
    ['2025-11-12 12:53:22', 'Gonzalo', 'almabarrestudio@gmail.com', 4809.75, 'perdido'],
    ['2025-11-11 20:21:28', 'Ivan Gonzalez', 'ivangh.fotografia@gmail.com', 0.00, 'perdido'],
    ['2025-10-30 12:43:17', 'Paula Garcia - tuopcionlegal', 'paulagg@letra2.net', 4809.75, 'no_responde'],
    ['2025-10-27 18:21:34', 'Andreea Florea CABRERA\'S BAR', 'floreaandreea390@gmail.com', 2994.75, 'no_responde'],
    ['2025-10-09 20:35:11', 'Laura Gómez', 'distrito001.distrito001@gmail.com', 3599.75, 'perdido'],
    ['2025-09-25 12:16:49', 'Carlos Pastor Paz', 'cpastorpaz@sagrado-corazon.es', 2238.50, 'perdido'],
    ['2025-09-23 10:09:45', 'Óscar', 'oscarfornasiero@hotmail.com', 1512.50, 'perdido'],
    ['2025-08-07 11:07:34', 'Ibrahim Homsi', 'ibrahomsi1911@gmail.com', 1633.50, 'no_cualificado'],
    ['2025-07-28 18:28:10', 'Jamil Andres Escobar Williams', '', 0.00, 'no_responde'],
    ['2025-06-13 13:57:20', 'Roberto B', 'gestion@burnett.es', 0.00, 'no_responde'],
    ['2025-05-27 17:05:08', 'David Ramos -Be On Retail', '', 1800.00, 'perdido'],
    ['2025-04-24 12:35:03', 'ÁLVARO', 'cp.josebergamin.madrid@educa.madrid.org', 1815.00, 'no_cualificado'],
    ['2025-02-18 12:49:01', 'Angela Coronel', 'angela.coronel.aizaga@gmail.com', 1368.00, 'perdido'],
    ['2026-03-19 20:19:01', 'Cristina Castillo', 'proyectos@cristinacastillo.com', 0.00, 'nuevo'],
];

$updated = 0;
$inserted = 0;

foreach ($leads as $l) {
    $created = $l[0];
    $name = $l[1];
    $email = $l[2];
    $price = (float)$l[3];
    $status = $l[4];

    // Intentar buscar por Email (si tiene) o por Nombre exacto
    $stmt = $conn->prepare("SELECT id FROM leads WHERE (email = ? AND email != '') OR name = ?");
    $stmt->bind_param("ss", $email, $name);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // ACTUALIZAR EXISTENTE
        $id = $res->fetch_assoc()['id'];
        $upd = $conn->prepare("UPDATE leads SET email=?, proposal_price=?, status=?, created_at=? WHERE id=?");
        $upd->bind_param("sdssi", $email, $price, $status, $created, $id);
        $upd->execute();
        echo "Actualizado: $name\n";
        $updated++;
    } else {
        // INSERTAR NUEVO
        $ins = $conn->prepare("INSERT INTO leads (name, email, proposal_price, status, created_at, source) VALUES (?, ?, ?, ?, ?, 'organico')");
        $ins->bind_param("ssdss", $name, $email, $price, $status, $created);
        $ins->execute();
        echo "Insertado: $name\n";
        $inserted++;
    }
}

echo "\n--- RESUMEN FINAL ---";
echo "\nLeads actualizados: $updated";
echo "\nLeads nuevos creados: $inserted";
$conn->close();
?>
