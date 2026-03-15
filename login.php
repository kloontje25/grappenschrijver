<?php
// ============================================================
// login.php — Inlogpagina voor de beheerder
// ============================================================

// Sessie starten met veilige instellingen:
// - cookie_httponly: het sessiecookie is NIET leesbaar via JavaScript (voorkomt XSS-diefstal)
// - cookie_samesite: voorkomt dat het cookie meegestuurd wordt via externe links (extra CSRF-bescherming)
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
]);

// Als de beheerder al is ingelogd, direct doorsturen naar het beheerderspaneel
if (!empty($_SESSION['admin'])) {
    header('Location: admin.php');
    exit;
}

require 'db.php';

$fout = '';

// --- Verwerk het inlogformulier (alleen bij POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord     = $_POST['wachtwoord'] ?? '';

    // Haal de beheerder op uit de database via de gebruikersnaam.
    // Prepared statement: onmogelijk om SQL-injectie te doen via de invoervelden.
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE gebruikersnaam = ?");
    $stmt->execute([$gebruikersnaam]);
    $admin = $stmt->fetch();

    // password_verify() vergelijkt het ingetypte wachtwoord met de opgeslagen bcrypt-hash.
    // Dit is veel veiliger dan een wachtwoord direct vergelijken.
    if ($admin && password_verify($wachtwoord, $admin['wachtwoord'])) {

        // Succes: geef de sessie een nieuw ID aan.
        // Dit voorkomt "session fixation": een aanvaller kan geen voorbereide sessie-ID injecteren.
        session_regenerate_id(true);

        // Sla de inlogstatus op in de sessie
        $_SESSION['admin']      = true;
        $_SESSION['admin_naam'] = $admin['gebruikersnaam'];

        // Genereer een CSRF-token: een willekeurig getal dat bij elk formulier meegestuurd wordt.
        // Zo weet de server dat het verzoek echt van jouw browservenster komt.
        $_SESSION['csrf'] = bin2hex(random_bytes(32));

        header('Location: admin.php');
        exit;

    } else {
        // Bewust vage foutmelding: geeft geen hint of de gebruikersnaam wél bestaat
        $fout = 'Gebruikersnaam of wachtwoord onjuist.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheerder inloggen — Grappenopslag</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Grappenopslag/h1>
        <a href="index.php" class="knop knop-terug">&#8592; Terug naar site</a>
    </header>

    <main class="formulier-wrapper">
        <h2>&#128274; Beheerder inloggen</h2>

        <?php if ($fout !== ''): ?>
            <p class="foutmelding"><?= htmlspecialchars($fout) ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input
                type="text"
                id="gebruikersnaam"
                name="gebruikersnaam"
                required
                autocomplete="username"
                maxlength="50"
            >

            <label for="wachtwoord">Wachtwoord:</label>
            <input
                type="password"
                id="wachtwoord"
                name="wachtwoord"
                required
                autocomplete="current-password"
            >

            <button type="submit" class="knop">Inloggen</button>
        </form>
    </main>

</body>
</html>
