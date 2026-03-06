<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

$session_id = $_SESSION['session_id'];

// Controlleer of sessie al bestaat
$sessionCheck = $conn->query("SELECT * FROM sessions WHERE id = '$session_id'");
if ($sessionCheck->num_rows > 0) {
    // Sessie bestaat al, ga naar de huienne stap
    $existingSession = $sessionCheck->fetch_assoc();
    header("Location: /grappenschrijver/pages/step" . $existingSession['current_step'] . ".php");
    exit;
}

// Verwerk formulier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $with_explanation = isset($_POST['with_explanation']) ? 1 : 0;
    
    // Creëer nieuwe sessie
    $stmt = $conn->prepare("INSERT INTO sessions (id, current_step, with_explanation) VALUES (?, 1, ?)");
    $stmt->bind_param("si", $session_id, $with_explanation);
    
    if ($stmt->execute()) {
        header("Location: /grappenschrijver/pages/step1.php");
        exit;
    }
}
?>

<div class="step-indicator">
    <div class="step active">
        <div class="step-number">1</div>
        <div class="step-label">Intro</div>
    </div>
    <div class="step">
        <div class="step-number">2</div>
        <div class="step-label">Betekenissen</div>
    </div>
    <div class="step">
        <div class="step-number">3</div>
        <div class="step-label">Associaties</div>
    </div>
    <div class="step">
        <div class="step-number">4</div>
        <div class="step-label">Grappen</div>
    </div>
    <div class="step">
        <div class="step-number">5</div>
        <div class="step-label">Review</div>
    </div>
</div>

<div class="card">
    <h2>📖 Het Grappenmakerproces</h2>
    
    <div class="alert alert-info mt-20">
        <strong>Hoe werkt het?</strong>
        <p style="margin-top: 10px;">
            Dit is een geleide workshop waar je stap voor stap leert hoe je grappen kunt creëren. 
            Je begint met woorden, voegt betekenissen en associaties toe, en eindigt met het schrijven van daadwerkelijke grappen.
        </p>
    </div>

    <h3 style="margin-top: 30px; margin-bottom: 15px;">📝 Stappenplan:</h3>
    <ol style="margin-left: 20px; line-height: 2;">
        <li><strong>Betekenissen:</strong> Voor elk woord schrijf je mogelijke betekenissen op.</li>
        <li><strong>Associaties:</strong> Per betekenis schrijf je dingen op die je ermee associeert.</li>
        <li><strong>Grappen:</strong> Met behulp van je associaties schrijf je de daadwerkelijke grappen.</li>
        <li><strong>Review:</strong> Bekijk je grappen en kies welke je publiceert.</li>
    </ol>

    <h3 style="margin-top: 30px; margin-bottom: 20px;">⚙️ Instellingen:</h3>
    
    <form method="POST" style="max-width: 500px;">
        <div class="checkbox-group">
            <input type="checkbox" id="with_explanation" name="with_explanation" value="1">
            <label for="with_explanation">
                Ik wil uitleg krijgen bij elke stap (aanbevolen voor beginners)
            </label>
        </div>

        <div class="button-group" style="margin-top: 40px;">
            <a href="/grappenschrijver/index.php" class="btn btn-secondary">← Terug naar Home</a>
            <button type="submit" class="btn btn-primary">Starten →</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
