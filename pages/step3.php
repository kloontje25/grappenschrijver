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

// Haal alle significa's en associaties op
$data = $conn->query(
    "SELECT m.*, w.word, w.id as word_id, a.id as assoc_id, a.association
     FROM meanings m
     JOIN words w ON m.word_id = w.id
     LEFT JOIN associations a ON m.id = a.meaning_id
     WHERE m.session_id = '$session_id'
     ORDER BY w.word ASC, m.created_at ASC, a.created_at ASC"
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
        <div class="step completed">
            <div class="step-number">3</div>
            <div class="step-label">Associaties</div>
        </div>
        <div class="step active">
            <div class="step-number">4</div>
            <div class="step-label">Grappen</div>
        </div>
        <div class="step">
            <div class="step-number">5</div>
            <div class="step-label">Review</div>
        </div>
    </div>

    <div class="card">
        <h2>😂 Stap 3: Grappen schrijven</h2>
        
        <div class="alert alert-info">
            <strong>Wat ga je doen?</strong>
            <p style="margin-top: 10px;">
                Nu is het moment aangebroken! Met al je betekenissen en associaties ga je daadwerkelijke grappen schrijven.
                Dit zijn korte, grappige teksten die gebruik maken van de associaties die je hebt opgeschreven.
                Denk aan wordplay, onverwachte wendingen, of absurdistische humor!
            </p>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">📌 Voorbeeld:</h3>
        <div class="meaning-box">
            <h4>Woord: MELK | Betekenis: Een wit drank van koeien</h4>
            <p><strong>Associaties:</strong> Koe, witte vloeistof, glas, gezond, boerderij</p>
            <p><strong>Mogelijke grappen:</strong></p>
            <ul style="margin-left: 20px;">
                <li>"Waarom melkt een koe nooit op de maandag? Omdat ze zondag nog volmeleld zijn!"</li>
                <li>"Wat zegt melk tegen de boer? 'Je bent mijn beste leverancier!'"</li>
            </ul>
        </div>

        <div class="button-group" style="margin-top: 40px;">
            <form method="POST" style="width: 100%;">
                <input type="hidden" name="start_step" value="3">
                <button type="submit" class="btn btn-primary btn-block">Ik ben klaar, laten gaan!</button>
            </form>
        </div>
    </div>
    <?php
else:
    // Normale stap weergave
    $meanings_by_word = [];
    foreach ($data as $row) {
        if (!isset($meanings_by_word[$row['word']])) {
            $meanings_by_word[$row['word']] = [];
        }
        
        $meaning_key = $row['id'];
        if (!isset($meanings_by_word[$row['word']][$meaning_key])) {
            $meanings_by_word[$row['word']][$meaning_key] = array(
                'meaning' => $row['meaning'],
                'associations' => [],
                'word_id' => $row['word_id']
            );
        }
        
        if ($row['assoc_id']) {
            $meanings_by_word[$row['word']][$meaning_key]['associations'][] = $row['association'];
        }
    }

    $words_list = array_keys($meanings_by_word);
    $current_index = 0;
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
        <div class="step completed">
            <div class="step-number">3</div>
            <div class="step-label">Associaties</div>
        </div>
        <div class="step active">
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
            <h2>Stap 3: Schrijf grappen</h2>
            <span style="background: var(--primary-color); color: white; padding: 8px 16px; border-radius: 20px; font-weight: bold;">
                <?php echo ($current_index + 1) . " / " . count($words_list); ?>
            </span>
        </div>

        <?php if ($current_word): ?>
            <div class="word-display" style="background: linear-gradient(135deg, #10b981, #059669);">
                <div class="word"><?php echo htmlspecialchars($current_word); ?></div>
                <div class="subtitle">Schrijf je grappen!</div>
            </div>

            <form method="POST" action="process_step3.php">
                <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
                <input type="hidden" name="word_index" value="<?php echo $current_index; ?>">

                <?php $meaning_index = 0; foreach ($meanings_by_word[$current_word] as $meaning_id => $meaning_data): $meaning_index++; ?>
                    <div class="meaning-box">
                        <h4>Betekenis <?php echo $meaning_index; ?>: <?php echo htmlspecialchars($meaning_data['meaning']); ?></h4>
                        
                        <?php if (!empty($meaning_data['associations'])): ?>
                            <p style="margin-bottom: 10px;"><strong>Associaties:</strong></p>
                            <div style="background: white; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
                                <?php echo implode(', ', array_map('htmlspecialchars', $meaning_data['associations'])); ?>
                            </div>
                        <?php endif; ?>

                        <label for="jokes_<?php echo $meaning_id; ?>">Schrijf je grappen op (je mag er meerdere schrijven):</label>
                        <textarea 
                            id="jokes_<?php echo $meaning_id; ?>" 
                            name="jokes[<?php echo $meaning_id; ?>]" 
                            placeholder="Schrijf hier je grappen..."
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
