<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

$session_id = $_SESSION['session_id'];

// Controleer of sessie bestaat
$sessionCheck = $conn->query("SELECT * FROM sessions WHERE id = '$session_id' AND completed = FALSE");
if ($sessionCheck->num_rows === 0) {
    header("Location: /grappenschrijver/pages/intro.php");
    exit;
}

$session = $sessionCheck->fetch_assoc();
$with_explanation = $session['with_explanation'];

// Haal alle woorden op
$words = $conn->query("SELECT * FROM words ORDER BY word ASC")->fetch_all(MYSQLI_ASSOC);

// Bepaal huienne word
$current_word_index = 0;
$meanings = $conn->query("SELECT DISTINCT word_id FROM meanings WHERE session_id = '$session_id'");
$used_word_ids = array_map(function($row) { return $row['word_id']; }, $meanings->fetch_all(MYSQLI_ASSOC));

foreach ($words as $key => $word) {
    if (!in_array($word['id'], $used_word_ids)) {
        $current_word_index = $key;
        break;
    }
}

$current_word = $words[$current_word_index] ?? null;
$total_words = count($words);
$processed_words = count($used_word_ids);

// Toon uitleg als nodig
if ($with_explanation && empty($_POST)):
    ?>
    <div class="step-indicator">
        <div class="step completed">
            <div class="step-number">1</div>
            <div class="step-label">Intro</div>
        </div>
        <div class="step active">
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
        <h2>📚 Stap 1: Betekenissen van woorden</h2>
        
        <div class="alert alert-info">
            <strong>Wat ga je doen?</strong>
            <p style="margin-top: 10px;">
                In deze stap krijg je willekeurige woorden. Voor elk woord schrijf je verschillende betekenissen of 
                definities op die in je hoofd komen. Dit helps je verschillende perspectieven op hetzelfde woord te zien.
            </p>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">📌 Voorbeeld:</h3>
        <div class="meaning-box">
            <h4>Woord: MELK</h4>
            <p><strong>Betekenissen:</strong></p>
            <ul style="margin-left: 20px;">
                <li>Een wit drank van koeien</li>
                <li>Een blanc vloeistof</li>
                <li>Het voedsel voor baby's</li>
                <li>Een ingrediënt in koffie</li>
            </ul>
        </div>

        <div class="button-group" style="margin-top: 40px;">
            <form method="POST" style="width: 100%;">
                <input type="hidden" name="start_step" value="1">
                <button type="submit" class="btn btn-primary btn-block">Ik ben klaar, laten gaan!</button>
            </form>
        </div>
    </div>
    <?php
else:
    // Normale stap weergave
    ?>
    <div class="step-indicator">
        <div class="step completed">
            <div class="step-number">1</div>
            <div class="step-label">Intro</div>
        </div>
        <div class="step active">
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Stap 1: Schrijf betekenissen op</h2>
            <span style="background: var(--primary-color); color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold;">
                <?php echo ($processed_words + 1) . " / " . $total_words; ?>
            </span>
        </div>

        <?php if ($current_word): ?>
            <div class="word-display">
                <div class="word"><?php echo htmlspecialchars($current_word['word']); ?></div>
                <div class="subtitle">Wat zijn de betekenissen van dit woord?</div>
            </div>

            <form method="POST" action="process_step1.php">
                <input type="hidden" name="word_id" value="<?php echo $current_word['id']; ?>">
                <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">

                <div class="form-group">
                    <label for="meanings">Schrijf de betekenissen op (één per regel):</label>
                    <textarea 
                        id="meanings" 
                        name="meanings" 
                        placeholder="1. Eerste betekenis&#10;2. Tweede betekenis&#10;3. Derde betekenis..." 
                        required
                    ></textarea>
                    <small style="color: var(--text-light); display: block; margin-top: 8px;">
                        💡 Tip: Schrijf minimaal 2-3 verschillende betekenissen op voor betere grappen later.
                    </small>
                </div>

                <div class="button-group justify-between">
                    <button type="submit" name="action" value="next" class="btn btn-secondary">
                        Volgende woord →
                    </button>
                    <?php if ($processed_words === $total_words - 1): ?>
                        <button type="submit" name="action" value="finish" class="btn btn-success">
                            Volgende stap →
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-success">
                <strong>Prima!</strong> Je hebt alle woorden verwerkt. 
                <a href="/grappenschrijver/pages/step2.php" class="btn btn-primary" style="margin-top: 10px;">Ga naar stap 2 →</a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
