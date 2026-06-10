<?php


require_once __DIR__ . "/path.php";

if (!isset($_SESSION["loggedInUserId"])) {
    header("Location: login.php");
    exit;
}

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

<?php include_once "php/include/head.php"; ?>

<body>

<header class="nav">

    <div class="nav-left">
        <div class="logo">🎮 NPC Tavern</div>
    </div>

    <div class="nav-center">
        <input type="text" class="search" placeholder="Search for Posts, Taverns...">
    </div>

    <div class="nav-right">
        <a href="beitrag-neu.php" class="button-link create-post">+ Create Post</a>

        <div class="icon">🔔</div>
        <div class="icon">👤</div>
    </div>

</header>

<div class="layout">

    <aside class="sidebar-left">
        <a href="index.php" class="button-link">Home</a>
    </aside>

    <main class="post-feed">
        <h1>Beitrag</h1>

        <article class="post">

            <div class="post-header">
                <div class="avatar"></div>

                <div>
                    <h2><?php echo htmlspecialchars($beitrag["spiel"]); ?></h2>
                    <p class="post-date">
                        von <?php echo htmlspecialchars($beitrag["autor"]); ?> · 
                        gepostet am <?php echo htmlspecialchars($beitrag["datum"]); ?>
                    </p>
                </div>
            </div>

            <p class="post-text">
                <?php echo htmlspecialchars($beitrag["text"]); ?>
            </p>

            <p>
                <img 
                    class="post-image"
                    src="<?php echo htmlspecialchars($beitrag["bild"]); ?>" 
                    alt="Beitragsbild"
                >
            </p>

            <div class="post-actions">
                <span>↑ 456 ↓</span>
                <a href="#">Kommentieren</a>
                <a href="#">Speichern</a>
            </div>

            <?php if ($istAutor): ?>
                <div class="post-edit-actions">
                    <a href="beitrag-neu.php" class="button-link">Beitrag ändern</a>

                    <form action="beitrag-loeschen.php" method="POST">
                        <button type="submit">Löschen</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($istAdmin): ?>
                <div class="post-edit-actions">
                    <form action="beitrag-loeschen.php" method="POST">
                        <button type="submit">Löschen</button>
                    </form>
                </div>
            <?php endif; ?>

        </article>
    </main>

    <aside class="sidebar-right">
        <h2>Trending Tags</h2>

        <ol>
            <li>#achievement</li>
            <li>#gameplay</li>
            <li>#meme</li>
            <li>#minecraft</li>
        </ol>
    </aside>

</div>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>

</body>
</html>