<?php
require_once '../includes/config.php';

$session_id = $_POST['session_id'] ?? $_SESSION['session_id'];
$action = $_POST['action'] ?? 'next';
$jokes = $_POST['jokes'] ?? [];

// Sla de grappen op
foreach ($jokes as $meaning_id => $jokes_text) {
    $jokes_array = array_filter(array_map('trim', explode("\n", $jokes_text)));
    
    // Haal word_id op
    $meaning = $conn->query("SELECT word_id FROM meanings WHERE id = " . intval($meaning_id))->fetch_assoc();
    $word_id = $meaning['word_id'];
    
    foreach ($jokes_array as $joke) {
        if (!empty($joke)) {
            $stmt = $conn->prepare("INSERT INTO session_jokes (session_id, word_id, joke_text) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $session_id, intval($word_id), $joke);
            $stmt->execute();
        }
    }
}

// Redirect
if ($action === 'finish') {
    $conn->query("UPDATE sessions SET current_step = 4 WHERE id = '$session_id'");
    header("Location: /grappenschrijver/pages/review.php");
} else {
    header("Location: /grappenschrijver/pages/step3.php");
}
exit;
?>
