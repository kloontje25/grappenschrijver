<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

$session_id = $_SESSION['session_id'];

// Controleer sessie
$sessionCheck = $conn->query("SELECT * FROM sessions WHERE id = '$session_id' AND completed = FALSE");
if ($sessionCheck->num_rows === 0) {
    header("Location: /grappenschrijver/pages/intro.php");
    exit;
}

$session = $sessionCheck->fetch_assoc();

// Haal alle grappen op
$jokes = $conn->query(
    "SELECT j.*, w.word FROM session_jokes j
     JOIN words w ON j.word_id = w.id
     WHERE j.session_id = '$session_id'
     ORDER BY w.word ASC, j.created_at ASC"
)->fetch_all(MYSQLI_ASSOC);

// Verwerk formulier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hidden_jokes = $_POST['hidden_jokes'] ?? [];
    
    // Update hidden status
    foreach ($jokes as $joke) {
        $is_hidden = in_array($joke['id'], array_keys($hidden_jokes)) ? 1 : 0;
        $conn->query("UPDATE session_jokes SET is_hidden = $is_hidden WHERE id = " . $joke['id']);
    }
    
    // Sla zichtbare grappen op in jokes tabel
    foreach ($jokes as $joke) {
        if (!in_array($joke['id'], array_keys($hidden_jokes))) {
            $stmt = $conn->prepare("INSERT INTO jokes (joke_text, session_id) VALUES (?, ?)");
            $stmt->bind_param("ss", $joke['joke_text'], $session_id);
            $stmt->execute();
        }
    }
    
    // Mark sessie als voltooid
    $conn->query("UPDATE sessions SET completed = TRUE, completed_at = NOW() WHERE id = '$session_id'");
    
    // Redirect naar home
    header("Location: /grappenschrijver/index.php");
    exit;
}
?>

<div class="step-indicator">
    <div class="step completed">
        <div class="step-number">1</div>
        <div class="step-label">Intro</div>
    </div>
    <div class="step completed">
        <div class="step-number">2</div>
        <div class="step-label">Betekenissen</div>
    </div>
    <div class="step completed">
        <div class="step-number">3</div>
        <div class="step-label">Associaties</div>
    </div>
    <div class="step completed">
        <div class="step-number">4</div>
        <div class="step-label">Grappen</div>
    </div>
    <div class="step active">
        <div class="step-number">5</div>
        <div class="step-label">Review</div>
    </div>
</div>

<div class="card">
    <h2>🎉 Laatste stap: Je grappen controleren</h2>
    
    <div class="alert alert-success">
        <strong>Geweldig!</strong> Je hebt alle stappen voltooid! 
        Nu mag je je grappen controleren en kiezen welke je wil publiceren.
    </div>

    <?php if (count($jokes) > 0): ?>
        <p style="margin: 20px 0; color: var(--text-light);">
            Vink hieronder de grappen aan die je <strong>NIET</strong> op de homepagina wilt zien.
        </p>

        <form method="POST">
            <div class="grid">
                <?php foreach ($jokes as $joke): ?>
                    <div class="joke-card">
                        <p><?php echo htmlspecialchars($joke['joke_text']); ?></p>
                        <small style="display: block; margin-top: 10px; color: var(--text-light);">
                            <?php echo htmlspecialchars($joke['word']); ?>
                        </small>
                        
                        <div class="checkbox-group" style="margin-top: 15px;">
                            <input type="checkbox" id="hidden_<?php echo $joke['id']; ?>" 
                                   name="hidden_jokes[<?php echo $joke['id']; ?>]" value="1">
                            <label for="hidden_<?php echo $joke['id']; ?>" style="font-size: 14px;">
                                Verberg deze grap
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="button-group" style="margin-top: 40px;">
                <a href="/grappenschrijver/index.php" class="btn btn-secondary">← Annuleren</a>
                <button type="submit" class="btn btn-success">
                    Publiceren en afronden ✓
                </button>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">
            <strong>Hmm...</strong> Je hebt geen grappen geschreven. 
            <a href="/grappenschrijver/pages/step3.php">Ga terug naar stap 3</a> om grappen toe te voegen.
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
