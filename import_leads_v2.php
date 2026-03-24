<?php
require_once 'db.php';
header('Content-Type: text/plain');

// LISTA DE LEADS A IMPORTAR (Mapeando los campos del usuario)
// Formato: [Etapa_Sistema, Es_Ganado, Descripcion/Nombre, Precio, Persona, Empresa]
$raw_leads = [
    ['nuevo', false, 'Lead contacto - Cristina Castillo (Formulario Home)', 0.00, 'Cristina Castillo', ''],
    ['enviar_propuesta', false, 'Presupuesto Web - Joan', 0.00, 'Joan', 'gestoriaemadrid'],
    ['enviar_propuesta', false, 'Ana Fexma', 0.00, 'Ana Fexma', 'anafexma'],
    ['enviar_propuesta', false, 'José Ángel', 0.00, 'José Ángel', ''],
    ['enviar_propuesta', false, 'Steve Mauricio', 0.00, 'Steve Mauricio', ''],
    ['enviar_propuesta', false, 'Alfonso', 0.00, 'Alfonso', 'gp3construcciones'],
    ['enviar_propuesta', false, 'Presupuesto web - María Martín', 0.00, 'María Martín', ''],
    ['enviar_propuesta', false, 'Lead contacto - Sonia (Formulario Home)', 0.00, 'Sonia', ''],
    ['enviar_propuesta', false, 'Presupuesto web - Jordi', 0.00, 'Jordi', ''],
    ['propuesta_enviada', false, 'Presupuesto web - Gina', 2450.00, 'Gina', 'Betropiqa'],
    ['propuesta_enviada', false, 'PACO GALIÑANES - Arquitecto Paco', 0.00, 'Paco', 'PACO GALIÑANES'],
    ['propuesta_enviada', false, 'Presupuesto Web Juanjo', 0.00, 'Juanjo', 'Palmeral'],
    ['propuesta_enviada', false, 'Lead contacto - Rolando Galano (Formulario Home)', 0.00, 'Lead contacto - Rolando Galano (Formulario Home)', ''],
    ['enviar_propuesta', false, 'Presupuesto Web - Alberto ARQUITECTO', 3950.00, 'Alberto', 'fordesigner'],
    ['ganado', true, 'Presupuesto Web - Iván Ortega', 3000.00, 'Iván Ortega', ''],
    ['ganado', true, 'Página web Ayerga', 4809.75, 'Álvaro', 'Ayerga'],
    ['ganado', true, 'Blog Natutube', 544.50, 'Luis', 'Natutube'],
    ['ganado', true, 'Portal Interno Inmobiliaria Azuqueca', 400.00, 'José Hormigas', ''],
    ['ganado', true, 'Rediseño web Natutube', 3194.40, 'Luis Castro Martínez', 'Natutube'],
    ['ganado', true, 'Presupuesto web Alicante Zenit FC', 3478.75, 'Meme Alcaraz', ''],
    ['ganado', true, 'Petroleo y gas consultores', 700.00, 'Daniel Calles Giraud', 'petroleoygasconsultores'],
    ['ganado', true, 'Web Los pequeños gigantes de la lectura - Guinda B2B', 300.00, 'Javier Montero', 'Somos Guinda'],
    ['perdido', false, 'Presupuesto Web - COFM Servicios 31', 6835.00, 'Laura Martín', 'Cofm Servicios 31'],
    ['perdido', false, 'Presupuesto web Ayuntamiento Loeches', 32065.00, 'Roberto Moreno', 'Ayuntamiento Loeches'],
    ['perdido', false, 'Landing Page Personal Trainer', 907.50, 'Natural Move', ''],
    ['perdido', false, 'Sybol - Landing web', 1633.50, 'Alfredo Abad', 'Sybol'],
    ['perdido', false, 'Toroshopping - Manuel Morales', 0.00, 'Manuel Morales', ''],
    ['perdido', false, 'Presupuesto Web Bocker - Antonio', 4809.75, 'Antonio', 'Bocker'],
    ['perdido', false, 'Presupuesto web laboratorioceranium.com', 8197.75, 'Almudena', 'laboratorioceranium.com'],
    ['perdido', false, 'Presupuesto web www.volinga.ai', 1131.35, 'Inés María García Lartategui', 'Volinga.ai'],
    ['perdido', false, 'Centro psicología Chamberí [Stand by]', 5021.50, 'Silvia Fernández Organista', ''],
    ['perdido', false, 'Portfolio Laura.M', 1100.00, 'Laura Monteagudo', ''],
    ['perdido', false, 'Lead contacto - Franklin (Formulario Home)', 0.00, 'Franklin', ''],
    ['perdido', false, 'Cesar - Intimx.es', 0.00, 'Cesar Ruiz', 'Intimx'],
    ['perdido', false, 'Pepa - pepapint.es', 0.00, 'Pepa', 'pepapint'],
    ['perdido', false, 'Presupuesto web Alma Barré - Gonzalo', 4809.75, 'Gonzalo', 'Alma Barré'],
    ['perdido', false, 'Web fotoperiodista - Ivan Gonzalez', 0.00, 'Ivan Gonzalez', ''],
    ['perdido', false, 'Presupuesto web Distrito001', 3599.75, 'Laura Gómez', ''],
    ['perdido', false, 'Centro Educativo Sagrado Corazón', 2238.50, 'Carlos Pastor Paz', 'Centro Educativo Sagrado Corazón'],
    ['perdido', false, 'Página web Inmobiliaria Óscar', 1512.50, 'Óscar', ''],
    ['perdido', false, 'Desarrollo Playmobil [Stand by]', 1800.00, 'David Ramos', 'Be On Retail'],
    ['perdido', false, 'Web Charlas', 1368.00, 'Angela Coronel', ''],
    ['no_cualificado', false, 'www.leadsales.io', 1100.00, 'Andres Medina', ''],
    ['no_cualificado', false, 'Presupuesto Web - IVAN MARTINEZ MARTINEZ', 0.00, 'IVAN MARTINEZ MARTINEZ', 'Altomira Trading'],
    ['no_cualificado', false, 'Geometrías Alta costura- VICTORIA RODRÍGUEZ', 0.00, 'Victoria Rodríguez', 'Geometrías Alta costura'],
    ['no_cualificado', false, 'Dark Kitchen - Luxury delivery', 1633.50, 'Ibrahim Homsi', ''],
    ['no_cualificado', false, 'CEIP JOSÉ BERGAMÍN', 1815.00, 'ÁLVARO', 'CEIP JOSÉ BERGAMÍN'],
    ['llamar_tarde', false, 'Boda Raquel y Calin', 0.00, 'Raquel y Calin', ''],
    ['no_responde', false, 'Rediseño HOME FREE administrativando.es', 0.00, 'Antonio Benitez', 'Administrativando'],
    ['no_responde', false, 'Colaboración SEO Local', 0.00, 'Andres Lead Sales', 'Ismael Ruíz González'],
    ['no_responde', false, 'Deep Spain - Jaime', 3175.00, 'Jaime', ''],
    ['no_responde', false, 'palomazabalgo.com', 4198.70, 'Mercedes García', 'Paloma Zabalgo'],
    ['no_responde', false, 'Concesionario Furgonetas Valdemoro', 2601.50, 'Angel', 'Furgonetasabuenprecio'],
    ['no_responde', false, 'Abogados Moncarpe', 0.00, 'Derecho Bancario', 'Moncarpe'],
    ['no_responde', false, 'Centro de Formación Minerva', 0.00, 'Juan Buitrago', 'Centro de Formación Minerva'],
    ['no_responde', false, 'Lina EntreCalles', 5975.00, 'Lina', 'Entre calles'],
    ['no_responde', false, 'tuopcionlegal - Paula García', 4809.75, 'Paula Garcia', 'Letra2'],
    ['no_responde', false, 'Andreea Florea CABRERA\'S BAR', 2994.75, 'Andreea Florea', 'CABRERA\'S BAR'],
    ['no_responde', false, 'Educar Sin Fronteras', 0.00, 'Jamil Andres Escobar', 'Educar Sin Fronteras'],
    ['no_responde', false, 'Web completa Burnett', 0.00, 'Roberto B', 'Burnett Investment SL'],
];

$inserted = 0;
$skipped = 0;

foreach ($raw_leads as $l) {
    $status = $l[0];
    $isGanado = $l[1];
    $desc = $l[2];
    $price = (float)$l[3];
    $contact = $l[4];
    $company = $l[5];

    // Lógica Anti-Duplicados (Nombre o Persona + Empresa)
    $checkQ = $conn->prepare("SELECT id FROM leads WHERE (name = ? OR name = ?) OR (company = ? AND company != '')");
    $checkQ->bind_param("sss", $desc, $contact, $company);
    $checkQ->execute();
    $res = $checkQ->get_result();
    
    if ($res->num_rows > 0) {
        echo "SALTADO (Ya existe): $contact / $desc\n";
        $skipped++;
        continue;
    }

    $stmt = $conn->prepare("INSERT INTO leads (name, company, status, proposal_price, source, created_at) VALUES (?, ?, ?, ?, 'organico', NOW())");
    $finalName = !empty($contact) ? $contact : $desc;
    $stmt->bind_param("sssd", $finalName, $company, $status, $price);
    
    if ($stmt->execute()) {
        echo "IMPORTADO: $finalName ($status)\n";
        $inserted++;
    } else {
        echo "ERROR en $finalName: " . $conn->error . "\n";
    }
}

echo "\n--- RESUMEN ---";
echo "\nLeads insertados: $inserted";
echo "\nLeads saltados (duplicados): $skipped";
echo "\nIMPORTACION FINALIZADA";

$conn->close();
?>
