<?php
// ============================================================
// wachtwoord.php — Hulpscript om een beheerderwachtwoord in te stellen
//
// HOE TE GEBRUIKEN:
//   1. Upload dit bestand tijdelijk naar je server.
//   2. Open het in de browser (bijv. mijnsite.nl/wachtwoord.php).
//   3. Vul je gewenste wachtwoord in en klik op "Genereer hash".
//   4. Kopieer de gegenereerde SQL en voer die uit in phpMyAdmin.
//
// Dit bestand is bewust simpel en heeft geen verdere beveiliging —
// verwijder het zodra je het hebt gebruikt.
// ============================================================

$hash = '';
$sql  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ww = $_POST['wachtwoord'] ?? '';
    if ($ww !== '') {
        // password_hash() maakt een veilige bcrypt-hash van het wachtwoord.
        // Elke keer een andere hash (door een willekeurig "salt"), maar password_verify() herkent hem altijd.
        $hash = password_hash($ww, PASSWORD_DEFAULT);
        $gebruiker = htmlspecialchars(trim($_POST['gebruikersnaam'] ?? 'admin'));
        $sql = "INSERT INTO admins (gebruikersnaam, wachtwoord) VALUES ('{$gebruiker}', '{$hash}');";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord instellen</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>&#128274; Beheerder aanmaken</h1>
    </header>

    <main class="formulier-wrapper">

        <h2>Stap 1 — Vul gegevens in</h2>

        <form method="POST">
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input type="text" id="gebruikersnaam" name="gebruikersnaam"
                   value="admin" maxlength="50" required>

            <label for="wachtwoord">Gewenst wachtwoord:</label>
            <input type="password" id="wachtwoord" name="wachtwoord"
                   placeholder="Kies een sterk wachtwoord" required>

            <button type="submit" class="knop">Genereer SQL</button>
        </form>

        <?php if ($hash !== ''): ?>
            <hr style="margin:24px 0; border:none; border-top:1px solid #d0d8e8;">
            <h2>Stap 2 — Voer deze SQL uit in phpMyAdmin</h2>
            <p style="color:#555; margin-bottom:10px;">
                Kopieer de onderstaande SQL en plak hem in het tabblad <strong>SQL</strong> van phpMyAdmin.
            </p>
            <div style="background:#f0f4f8; padding:14px; border-radius:6px; word-break:break-all; font-family:monospace; font-size:0.9rem;">
                <?= htmlspecialchars($sql) ?>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>
