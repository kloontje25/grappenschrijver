<?php
// ============================================================
// index.php — Startpagina: toont alle grappen + filterbuttons
// ============================================================

require 'db.php';  // Laad de databaseverbinding

// --- Stap 1: Haal alle categorieën op voor de filterbuttons ---
$categorieen = $pdo->query("SELECT * FROM categorieen ORDER BY naam")->fetchAll();

// --- Stap 2: Bepaal of er gefilterd wordt op een categorie ---
// $_GET['categorie'] bevat het ID als er op een filter-knop is geklikt (bijv. ?categorie=3)
// (int) zorgt dat het altijd een geheel getal is → veilig tegen SQL-injectie
$filterCategorie = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;

// --- Stap 3: Haal de grappen op (gefilterd of alle) ---
if ($filterCategorie > 0) {
    // Gefilterd: haal alleen GOEDGEKEURDE grappen op met de gekozen categorie.
    $stmt = $pdo->prepare("
        SELECT g.id, g.tekst
        FROM grappen g
        JOIN grap_categorie gc ON g.id = gc.grap_id
        WHERE gc.categorie_id = ?
          AND g.goedgekeurd = 1
        ORDER BY g.id DESC
    ");
    $stmt->execute([$filterCategorie]);
} else {
    // Niet gefilterd: haal alle GOEDGEKEURDE grappen op, nieuwste eerst
    $stmt = $pdo->query("SELECT id, tekst, goedgekeurd FROM grappen WHERE goedgekeurd = 1 ORDER BY id DESC");
}
$grappen = $stmt->fetchAll();

// --- Hulpfunctie: haal de categorienamen op van één grap ---
// Door de koppeltabel te doorzoeken weten we welke categorieën bij een grap horen.
function haalCategorieenOp(PDO $pdo, int $grap_id): array {
    $stmt = $pdo->prepare("
        SELECT c.naam
        FROM categorieen c
        JOIN grap_categorie gc ON c.id = gc.categorie_id
        WHERE gc.grap_id = ?
        ORDER BY c.naam
    ");
    $stmt->execute([$grap_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);  // Geeft een simpele lijst van namen terug
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grappenopslag</title>
    <link rel="icon" href="logo.webp" type="image/webp">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Bovenste balk met titel en knop om grap toe te voegen -->
    <header>
        <h1><a href="index.php" style="color:inherit; text-decoration:none;">Grappenopslag</a></h1>
        <a href="indienen.php" class="knop">Grap indienen</a>
    </header>

    <!-- Filterbuttons: één knop per categorie + "Alle grappen" -->
    <section class="filters">
        <span class="filter-label" style="align-content: center;">Filter:</span>
        <!-- Actieve knop krijgt de CSS-klasse 'actief' zodat hij oplicht -->
        <a href="index.php"
           class="filter-knop <?= ($filterCategorie === 0) ? 'actief' : '' ?>">
            Alle grappen
        </a>

        <?php foreach ($categorieen as $cat): ?>
            <a href="?categorie=<?= $cat['id'] ?>"
               class="filter-knop <?= ($filterCategorie === (int)$cat['id']) ? 'actief' : '' ?>">
                <?= htmlspecialchars($cat['naam']) ?>
                <!-- htmlspecialchars() voorkomt XSS: zet < > & om naar veilige tekens -->
            </a>
        <?php endforeach; ?>
    </section>

    <!-- Hoofdgedeelte: de grappen als kaarten -->
    <main>
        <?php if (empty($grappen)): ?>
            <!-- Toon een melding als er geen grappen zijn (of geen resultaten bij filter) -->
            <p class="leeg">
                Geen grappen gevonden.
                <a href="indienen.php">Voeg de eerste grap toe!</a>
            </p>

        <?php else: ?>
            <?php foreach ($grappen as $grap): ?>
                <article class="grap-kaart">
                    <!-- De tekst van de grap (nl2br zet enters om naar <br>) -->
                    <p class="grap-tekst">
                        <?= nl2br(htmlspecialchars($grap['tekst'])) ?>
                    </p>

                    <!-- Onderste balk: categorielabels -->
                    <div class="grap-footer">
                        <span class="categorieen">
                            <?php foreach (haalCategorieenOp($pdo, $grap['id']) as $naam): ?>
                                <span class="label">
                                    <?= htmlspecialchars($naam) ?>
                                </span>
                            <?php endforeach; ?>
                        </span>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

</body>
</html>
