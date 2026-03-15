<?php
// ============================================================
// uitloggen.php — Uitloggen als beheerder
//
// Verwijdert alle sessiegegevens en stuurt terug naar de loginpagina.
// ============================================================

session_start();

// Verwijder alle gegevens uit de sessie
session_unset();

// Vernietig de sessie volledig (verwijdert ook het sessiecookie)
session_destroy();

// Stuur terug naar de loginpagina
header('Location: login.php');
exit;
