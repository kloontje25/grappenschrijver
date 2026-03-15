<?php
// ============================================================
// admin.php — Beheerderspaneel
//
// Hier kan de beheerder:
//   - grappen goedkeuren of verwijderen
//   - categorieën toevoegen of verwijderen
//
// Alleen toegankelijk als je bent ingelogd als beheerder.
// ============================================================

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
]);

// Niet ingelogd? Doorsturen naar de loginpagina.
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

$melding = '';

// --- Verwerk acties (formulieren sturen altijd via POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF-controle: vergelijk het token uit het formulier met het token in de sessie.
    // hash_equals() voorkomt "timing attacks" bij de vergelijking.
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf_token'] ?? '')) {
        die('Ongeldige aanvraag. Ga terug en probeer opnieuw.');
    }

    $actie = $_POST['actie'] ?? '';

    // --- Grap goedkeuren ---
    if ($actie === 'goedkeuren') {
        $id = (int)$_POST['id'];  // (int) zorgt dat het altijd een getal is → veilig
        $pdo->prepare("UPDATE grappen SET goedgekeurd = 1 WHERE id = ?")->execute([$id]);
        $melding = 'Grap goedgekeurd!';
    }

    // --- Grap verwijderen ---
    if ($actie === 'grap_verwijderen') {
        $id = (int)$_POST['id'];
        // ON DELETE CASCADE in de database verwijdert grap_categorie-koppelingen automatisch
        $pdo->prepare("DELETE FROM grappen WHERE id = ?")->execute([$id]);
        $melding = 'Grap verwijderd.';
    }

    // --- Categorie toevoegen ---
    if ($actie === 'categorie_toevoegen') {
        $naam = trim($_POST['naam'] ?? '');
        if ($naam !== '') {
            // INSERT IGNORE: als de naam al bestaat, geeft dit geen fout
            $pdo->prepare("INSERT IGNORE INTO categorieen (naam) VALUES (?)")->execute([$naam]);
            $melding = 'Categorie "' . htmlspecialchars($naam) . '" toegevoegd.';
        }
    }

    // --- Categorie verwijderen ---
    if ($actie === 'categorie_verwijderen') {
        $id = (int)$_POST['id'];
        // ON DELETE CASCADE verwijdert ook alle koppelingen in grap_categorie automatisch
        $pdo->prepare("DELETE FROM categorieen WHERE id = ?")->execute([$id]);
        $melding = 'Categorie verwijderd.';
    }
}

// --- Data ophalen voor de pagina ---
$wachtend    = $pdo->query("SELECT * FROM grappen WHERE goedgekeurd = 0 ORDER BY datum DESC")->fetchAll();
$goedgekeurd = $pdo->query("SELECT * FROM grappen WHERE goedgekeurd = 1 ORDER BY datum DESC")->fetchAll();
$categorieen = $pdo->query("SELECT * FROM categorieen ORDER BY naam")->fetchAll();

// Het CSRF-token uit de sessie, meegestuurd als verborgen veld in elk formulier
$csrf = $_SESSION['csrf'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheerderspaneel — Grappendatabase</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>&#128274; Beheerderspaneel</h1>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <span style="color:rgba(255,255,255,0.8); align-self:center; font-size:0.9rem;">
                Ingelogd als: <strong><?= htmlspecialchars($_SESSION['admin_naam']) ?></strong>
            </span>
            <a href="index.php"    class="knop knop-terug">&#8592; Naar de site</a>
            <a href="uitloggen.php" class="knop knop-terug">Uitloggen</a>
        </div>
    </header>

    <main style="max-width:820px; margin:24px auto; padding:0 16px;">

        <!-- Succes- of foutmelding bovenaan -->
        <?php if ($melding !== ''): ?>
            <p class="melding-ok"><?= htmlspecialchars($melding) ?></p>
        <?php endif; ?>


        <!-- =================================================
             SECTIE 1: Wachten op goedkeuring
             ================================================= -->
        <section class="admin-sectie">
            <h2>&#9203; Wachten op goedkeuring
                <span class="badge"><?= count($wachtend) ?></span>
            </h2>

            <?php if (empty($wachtend)): ?>
                <p class="leeg">Geen grappen in de wachtrij. &#10003;</p>
            <?php else: ?>
                <?php foreach ($wachtend as $grap): ?>
                    <div class="grap-kaart">
                        <p class="grap-tekst">
                            <?= nl2br(htmlspecialchars($grap['tekst'])) ?>
                        </p>
                        <div class="admin-knoppen">

                            <!-- Goedkeuren -->
                            <form method="POST" action="admin.php" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="actie"      value="goedkeuren">
                                <input type="hidden" name="id"         value="<?= $grap['id'] ?>">
                                <button type="submit" class="knop knop-groen">&#10003; Goedkeuren</button>
                            </form>

                            <!-- Verwijderen (met bevestigingsdialoog) -->
                            <form method="POST" action="admin.php" style="display:inline;"
                                  onsubmit="return confirm('Grap definitief verwijderen?')">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="actie"      value="grap_verwijderen">
                                <input type="hidden" name="id"         value="<?= $grap['id'] ?>">
                                <button type="submit" class="knop knop-rood">&#10007; Verwijderen</button>
                            </form>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>


        <!-- =================================================
             SECTIE 2: Goedgekeurde grappen
             ================================================= -->
        <section class="admin-sectie">
            <h2>&#10003; Goedgekeurde grappen
                <span class="badge badge-groen"><?= count($goedgekeurd) ?></span>
            </h2>

            <?php if (empty($goedgekeurd)): ?>
                <p class="leeg">Nog geen goedgekeurde grappen.</p>
            <?php else: ?>
                <?php foreach ($goedgekeurd as $grap): ?>
                    <div class="grap-kaart">
                        <p class="grap-tekst">
                            <?= nl2br(htmlspecialchars($grap['tekst'])) ?>
                        </p>
                        <div class="admin-knoppen">
                            <form method="POST" action="admin.php" style="display:inline;"
                                  onsubmit="return confirm('Grap definitief verwijderen?')">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="actie"      value="grap_verwijderen">
                                <input type="hidden" name="id"         value="<?= $grap['id'] ?>">
                                <button type="submit" class="knop knop-rood">&#10007; Verwijderen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>


        <!-- =================================================
             SECTIE 3: Categorieën beheren
             ================================================= -->
        <section class="admin-sectie">
            <h2>&#127991; Categorieën beheren</h2>

            <!-- Formulier: nieuwe categorie toevoegen -->
            <form method="POST" action="admin.php" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="actie"      value="categorie_toevoegen">
                <input type="text"   name="naam"
                       placeholder="Nieuwe categorienaam..."
                       maxlength="100"
                       required>
                <button type="submit" class="knop">+ Toevoegen</button>
            </form>

            <!-- Lijst van bestaande categorieën met verwijderknop -->
            <div style="margin-top:14px;">
                <?php foreach ($categorieen as $cat): ?>
                    <div class="categorie-rij">
                        <span><?= htmlspecialchars($cat['naam']) ?></span>
                        <form method="POST" action="admin.php" style="display:inline;"
                              onsubmit="return confirm('Categorie verwijderen?')">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="actie"      value="categorie_verwijderen">
                            <input type="hidden" name="id"         value="<?= $cat['id'] ?>">
                            <button type="submit" class="knop knop-rood knop-klein">&#10007;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

</body>
</html>
