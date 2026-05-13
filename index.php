<?php
$title = "Beiträge";
$eingeloggt = true; 

$beitraege = [
    [
        "bild" => "bild1.jpg",
        "text" => "Das ist ein Beispielbeitrag zu Minecraft.",
        "datum" => "21.04.2026",
        "spiel" => "Minecraft"
    ],
    [
        "bild" => "bild2.jpg",
        "text" => "Das ist ein Beispielbeitrag zu Stardew Valley.",
        "datum" => "20.04.2026",
        "spiel" => "Stardew Valley"
    ]
];

$gewaehltesSpiel = $_GET["spiel"] ?? "";

$gefilterteBeitraege = [];

foreach ($beitraege as $beitrag) {
    if ($gewaehltesSpiel === "" || $beitrag["spiel"] === $gewaehltesSpiel) {
        $gefilterteBeitraege[] = $beitrag;
    }
}
?>

<?php include_once "php/head.php"; ?>

<body>

<header class="nav">

    <div class="nav-left">
        <div class="logo">🎮 NPC Tavern</div>
    </div>

    <div class="nav-center">
        <input type="text" class="search" placeholder="Search for Posts, Taverns...">
    </div>
    <div class="nav-right">
        <button class="create-post">+ Create Post</button>

        <div class="icon">🔔</div>

        <div class="icon">👤</div>
    </div>

</header>

<div class="layout">

    <aside class="sidebar-left">
        
        
        <form class="filter-form" method="GET" action="beitraege-index.php">
            <button> <a href="index.php" class= "button-link">Home</a></button>
            <label for="spiel">Nach Spiel filtern:</label>

            <select name="spiel" id="spiel">
                <option value="" <?php if ($gewaehltesSpiel === "") echo "selected"; ?>>Alle</option>
                <option value="Minecraft" <?php if ($gewaehltesSpiel === "Minecraft") echo "selected"; ?>>Minecraft</option>
                <option value="Stardew Valley" <?php if ($gewaehltesSpiel === "Stardew Valley") echo "selected"; ?>>Stardew Valley</option>
                <option value="Valorant" <?php if ($gewaehltesSpiel === "Valorant") echo "selected"; ?>>Valorant</option>
            </select>

            <button type="submit">Filtern</button>
        </form>
    </aside>

    <main class="post-feed">
        <h1>Beiträge</h1>

        <?php foreach ($gefilterteBeitraege as $beitrag): ?>
            <article class="post">
                <div class="post-header">
                    <div class="avatar"></div>

                    <div>
                        <h2><?php echo htmlspecialchars($beitrag["spiel"]); ?></h2>
                        <p class="post-date">
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

                <p class="post-actions">
                    <span>↑ 456 ↓</span>
                    <a href="beitrag.php">Kommentieren</a>
                    <a href="#">Speichern</a>
                </p>
            </article>
        <?php endforeach; ?>

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
    <?php include_once "php/footer.php"; ?>
</footer>

</body>
</html>