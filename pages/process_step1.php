<?php
require_once '../includes/config.php';

$session_id = $_POST['session_id'] ?? $_SESSION['session_id'];
$word_id = intval($_POST['word_id']);
$meanings_text = $_POST['meanings'] ?? '';
$action = $_POST['action'] ?? 'next';

// Parse de betekenissen (split op newline)
$meanings_array = array_filter(array_map('trim', explode("\n", $meanings_text)));

// Sla de betekenissen op
foreach ($meanings_array as $meaning) {
    if (!empty($meaning)) {
        $stmt = $conn->prepare("INSERT INTO meanings (session_id, word_id, meaning) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $session_id, $word_id, $meaning);
        $stmt->execute();
    }
}

// Redirect
if ($action === 'finish') {
    // Update huienne stap
    $conn->query("UPDATE sessions SET current_step = 2 WHERE id = '$session_id'");
    header("Location: /grappenschrijver/pages/step2.php");
} else {
    header("Location: /grappenschrijver/pages/step1.php");
}
exit;
?>
