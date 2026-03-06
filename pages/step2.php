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
$with_explanation = $session['with_explanation'];

// Haal alle betekenissen op
$meanings = $conn->query(
    "SELECT m.*, w.word FROM meanings m 
     JOIN words w ON m.word_id = w.id 
     WHERE m.session_id = '$session_id'
     ORDER BY w.word ASC, m.created_at ASC"
)->fetch_all(MYSQLI_ASSOC);

// Toon uitleg als nodig
if ($with_explanation && empty($_POST)):
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
        <div class="step active">
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
        <h2>🔗 Stap 2: Associaties schrijven</h2>
        
        <div class="alert alert-info">
            <strong>Wat ga je doen?</strong>
            <p style="margin-top: 10px;">
                Nu ga je voor elke betekenis associaties schrijven. Dit zijn woorden, dingen of gedachten 
                die je automatisch bij die betekenis hoort. Dit helpt je grappen creëren die echt grappig zijn!
            </p>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">📌 Voorbeeld:</h3>
        <div class="meaning-box">
            <h4>Woord: MELK</h4>
            <p><strong>Betekenis:</strong> Een wit drank van koeien</p>
            <p><strong>Associaties:</strong></p>
            <ul style="margin-left: 20px;">
                <li>Koe</li>
                <li>Witte vloeistof</li>
                <li>Glas</li>
                <li>Gezond</li>
                <li>Boerderij</li>
            </ul>
        </div>

        <div class="button-group" style="margin-top: 40px;">
            <form method="POST" style="width: 100%;">
                <input type="hidden" name="start_step" value="2">
                <button type="submit" class="btn btn-primary btn-block">Ik ben klaar, laten gaan!</button>
            </form>
        </div>
    </div>
    <?php
else:
    // Normale stap weergave
    $current_index = 0;
    $meanings_by_word = [];
    foreach ($meanings as $meaning) {
        if (!isset($meanings_by_word[$meaning['word']])) {
            $meanings_by_word[$meaning['word']] = [];
        }
        $meanings_by_word[$meaning['word']][] = $meaning;
    }
    
    $words_list = array_keys($meanings_by_word);
    $current_word = $words_list[$current_index] ?? null;
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
        <div class="step active">
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
            <h2>Stap 2: Associaties schrijven</h2>
            <span style="background: var(--primary-color); color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold;">
                <?php echo ($current_index + 1) . " / " . count($words_list); ?>
            </span>
        </div>

        <?php if ($current_word): ?>
            <div class="word-display" style="background: linear-gradient(135deg, #ec4899, #f97316);">
                <div class="word"><?php echo htmlspecialchars($current_word); ?></div>
                <div class="subtitle">Schrijf associaties voor elk betekenis</div>
            </div>

            <form method="POST" action="process_step2.php">
                <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
                <input type="hidden" name="word_index" value="<?php echo $current_index; ?>">

                <?php foreach ($meanings_by_word[$current_word] as $meaning): ?>
                    <div class="meaning-box">
                        <h4>Betekenis: <?php echo htmlspecialchars($meaning['meaning']); ?></h4>
                        <label for="associations_<?php echo $meaning['id']; ?>">Schrijf associaties op (één per regel):</label>
                        <textarea 
                            id="associations_<?php echo $meaning['id']; ?>" 
                            name="associations[<?php echo $meaning['id']; ?>]" 
                            placeholder="1. Eerste associatie&#10;2. Tweede associatie&#10;3. Derde associatie..."
                            required
                        ></textarea>
                    </div>
                <?php endforeach; ?>

                <div class="button-group justify-between">
                    <button type="submit" name="action" value="next" class="btn btn-secondary">
                        Volgende woord →
                    </button>
                    <?php if ($current_index === count($words_list) - 1): ?>
                        <button type="submit" name="action" value="finish" class="btn btn-success">
                            Volgende stap →
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
