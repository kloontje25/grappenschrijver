<?php
require_once '../includes/config.php';

$session_id = $_POST['session_id'] ?? $_SESSION['session_id'];
$action = $_POST['action'] ?? 'next';
$associations = $_POST['associations'] ?? [];

// Sla de associaties op
foreach ($associations as $meaning_id => $associations_text) {
    $associations_array = array_filter(array_map('trim', explode("\n", $associations_text)));
    
    foreach ($associations_array as $association) {
        if (!empty($association)) {
            $stmt = $conn->prepare("INSERT INTO associations (meaning_id, association) VALUES (?, ?)");
            $stmt->bind_param("is", intval($meaning_id), $association);
            $stmt->execute();
        }
    }
}

// Redirect
if ($action === 'finish') {
    $conn->query("UPDATE sessions SET current_step = 3 WHERE id = '$session_id'");
    header("Location: /grappenschrijver/pages/step3.php");
} else {
    header("Location: /grappenschrijver/pages/step2.php");
}
exit;
?>
