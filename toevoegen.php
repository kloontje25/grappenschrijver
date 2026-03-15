<?php
// ============================================================
// toevoegen.php — Formulier om een nieuwe grap op te slaan
// ============================================================

require 'db.php';  // Laad de databaseverbinding

$melding = '';  // Variabele voor fout- of succesmeldingen

// --- Verwerk het formulier als het is verstuurd (POST-verzoek) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Haal de ingevoerde waarden op uit het formulier
    $tekst            = trim($_POST['tekst']            ?? '');
    $gekozenCategorieen = $_POST['categorieen']          ?? [];  // Array van aangevinkte checkboxes

    // Validatie: grap mag niet leeg zijn
    if ($tekst === '') {
        $melding = 'Vul een grap in!';

    } else {
        // --------------------------------------------------------
        // STAP 1: Sla de grap op in de tabel 'grappen'
        // --------------------------------------------------------
        // We gebruiken een prepared statement met ? als placeholder.
        // PDO vult de waarde veilig in, zodat SQL-injectie onmogelijk is.
        $stmt = $pdo->prepare("INSERT INTO grappen (tekst) VALUES (?)");
        $stmt->execute([$tekst]);

        // Haal het automatisch gegenereerde ID op van de nieuwe rij
        $grap_id = (int)$pdo->lastInsertId();


        // --------------------------------------------------------
        // STAP 2: Koppel alle categorieën aan de grap via de koppeltabel
        // --------------------------------------------------------
        if (!empty($gekozenCategorieen)) {
            $stmt = $pdo->prepare("INSERT INTO grap_categorie (grap_id, categorie_id) VALUES (?, ?)");
            foreach ($gekozenCategorieen as $cat_id) {
                $stmt->execute([$grap_id, (int)$cat_id]);
                // (int) zorgt dat het altijd een getal is → veilig
            }
        }

        // --------------------------------------------------------
        // STAP 3: Toon een bevestiging — grap wacht op goedkeuring
        // --------------------------------------------------------
        $melding = 'Je grap is ingediend! De beheerder moet hem eerst goedkeuren voordat hij verschijnt.';
    }
}

// Haal bestaande categorieën op om als checkboxes te tonen
$categorieen = $pdo->query("SELECT * FROM categorieen ORDER BY naam")->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grap toevoegen — Grappendatabase</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Bovenste balk -->
    <header>
        <h1>&#128514; Grappendatabase</h1>
        <a href="index.php" class="knop knop-terug">&#8592; Terug</a>
    </header>

    <main class="formulier-wrapper">
        <h2>Nieuwe grap toevoegen</h2>

        <!-- Toon melding (rood = fout, groen = succes) -->
        <?php if ($melding !== ''): ?>
            <?php $klasse = str_starts_with($melding, 'Je grap') ? 'melding-ok' : 'foutmelding'; ?>
            <p class="<?= $klasse ?>"><?= htmlspecialchars($melding) ?></p>
        <?php endif; ?>

        <!-- Het formulier: method="POST" stuurt data naar dezelfde pagina -->
        <form method="POST" action="toevoegen.php">

            <!-- Tekstveld voor de grap -->
            <label for="tekst">Grap:</label>
            <textarea
                id="tekst"
                name="tekst"
                rows="5"
                placeholder="Typ hier je grap..."
                required
            ></textarea>

            <!-- Checkboxes voor bestaande categorieën -->
            <label>Categorie(ën):</label>
            <div class="checkbox-groep">
                <?php foreach ($categorieen as $cat): ?>
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="categorieen[]"
                            value="<?= $cat['id'] ?>"
                        >
                        <?= htmlspecialchars($cat['naam']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="knop">&#128190; Grap opslaan</button>
        </form>
    </main>

</body>
</html>
