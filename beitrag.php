<?php
$title = "Beitrag";

$beitrag = [
    "autor" => "Tomi",
    "spiel" => "Minecraft",
    "datum" => "21.04.2026",
    "bild" => "bild1.jpg",
    "text" => "Das ist ein einzelner Beitrag mit Bild und Text."
];

$istAutor = true;  
$istAdmin = false;  
?>

<?php include_once "php/head.php"; ?>

<body>

<header class="nav">
    <div class="logo">🎮 NPCTavern</div>
    <?php include_once "php/nav.php"; ?>
</header>

<main class="auth-main">
    <section>
        <h1>Beitrag</h1>

        <div class="single-post-info">
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($beitrag["autor"]); ?></p>
            <p><strong>Spielename:</strong> <?php echo htmlspecialchars($beitrag["spiel"]); ?></p>
            <p><strong>Datum der Erstellung:</strong> <?php echo htmlspecialchars($beitrag["datum"]); ?></p>
        </div>

        <p>
            <img 
                class="single-post-image"
                src="<?php echo htmlspecialchars($beitrag["bild"]); ?>" 
                alt="Beitragsbild"
            >
        </p>

        <p class="single-post-text">
            <?php echo htmlspecialchars($beitrag["text"]); ?>
        </p>

        <?php if ($istAutor): ?>
            <div class="post-edit-actions">
                <a href="beitrag-neu.php">Beitrag ändern</a>

                <form action="beitrag-loeschen.php" method="POST">
                    <button type="submit">Löschen</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($istAdmin): ?>
            <form action="beitrag-loeschen.php" method="POST">
                <button type="submit">Löschen</button>
            </form>
        <?php endif; ?>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/footer.php"; ?>
</footer>

</body>
</html>