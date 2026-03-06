<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Haal alle zichtbare grappen op
$result = $conn->query("SELECT * FROM jokes WHERE is_hidden = FALSE ORDER BY created_at DESC");
$jokes = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="card text-center py-40">
    <h2>Welkom bij de Grappenschrijver! 👋</h2>
    <p style="margin: 20px 0; font-size: 16px; color: var(--text-light);">
        Ontdek de kunst van grappenmakers. Volg het proces om je eigen grappen te creëren via een geleide workshop.
    </p>
    <a href="/grappenschrijver/pages/intro.php" class="btn btn-primary btn-large" style="padding: 15px 40px; font-size: 18px;">
        Begin het Proces →
    </a>
</div>

<h2 style="margin: 40px 0 20px;">📚 Alle Grappen</h2>

<?php if (count($jokes) > 0): ?>
    <div class="grid">
        <?php foreach ($jokes as $joke): ?>
            <div class="joke-card">
                <p><?php echo htmlspecialchars($joke['joke_text']); ?></p>
                <small style="color: var(--text-light);">
                    <?php echo date('d-m-Y', strtotime($joke['created_at'])); ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <strong>Geen grappen gevonden.</strong> Begin het proces om je eerste grap te schrijven!
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
