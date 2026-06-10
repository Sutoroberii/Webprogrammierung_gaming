<?php

require_once __DIR__ . "/path.php";
require_once $abs_path . "/php/model/Post.php";

$title = "Beiträge";

$eingeloggt = isset($_SESSION["username"]) || isset($_SESSION["loggedInUserId"]);

$postDao = Post::getInstance();

try {
    $alleBeitraege = $postDao->findAll();
    $trendingTags = $postDao->getBestTags(10);
} catch (Exception $e) {
    $alleBeitraege = [];
    $trendingTags = [];
    $datenbankFehler = $e->getMessage();
}

$gewaehltesSpiel = trim($_GET["spiel"] ?? "");

$filterOptionen = [];

foreach ($alleBeitraege as $post) {
    if ($post->getPostTitle() !== "") {
        $filterOptionen[] = $post->getPostTitle();
    }

    foreach ($post->getPostTags() as $tag) {
        if (trim($tag) !== "") {
            $filterOptionen[] = trim($tag);
        }
    }
}

$filterOptionen = array_values(array_unique($filterOptionen));
sort($filterOptionen);

$gefilterteBeitraege = [];

foreach ($alleBeitraege as $post) {
    if ($gewaehltesSpiel === "") {
        $gefilterteBeitraege[] = $post;
        continue;
    }

    $titelPasst = strtolower($post->getPostTitle()) === strtolower($gewaehltesSpiel);

    $tagsLower = array_map(
        fn($tag) => strtolower(trim($tag)),
        $post->getPostTags()
    );

    $tagPasst = in_array(strtolower($gewaehltesSpiel), $tagsLower);

    if ($titelPasst || $tagPasst) {
        $gefilterteBeitraege[] = $post;
    }
}

?>

<?php include_once $abs_path . "/php/include/head.php"; ?>

<body>

<header class="nav">

    <div class="nav-left">
        <a href="index.php" class="logo">

            <div class="logo-icon">
                <iconify-icon icon="game-icons:beer-stein"></iconify-icon>
            </div>

            <span class="logo-text">NPC Tavern</span>

        </a>
    </div>

    <div class="nav-center">
        <input type="text" class="search" placeholder="Suche nach Posts, Tavernen...">
    </div>

<div class="nav-right">

    <?php if ($eingeloggt): ?>
        <a href="beitrag-neu.php" class="button-link create-post">
            + Erstelle einen Beitrag
        </a>
    <?php endif; ?>

    <div class="icon">🔔</div>

    <?php if ($eingeloggt): ?>
        <a href="profil.php" class="icon" aria-label="Profil">👤</a>
    <?php else: ?>
        <a href="login.php" class="icon" aria-label="Anmelden">👤</a>
    <?php endif; ?>

</div>

</header>

<div class="layout">

    <aside class="sidebar-left">

        <form class="filter-form" method="GET" action="index.php">

            <a href="index.php" class="button-link">Startseite</a>

            <?php if ($eingeloggt): ?>

                <a href="login.php?action=logout" class="button-link">Abmelden</a>
                <a href="benutzer-deregistrieren.php" class="button-link">Deregistrieren</a>

            <?php else: ?>

                <a href="login.php" class="button-link">Anmeldung</a>
                <a href="register.php" class="button-link">Registrieren</a>

            <?php endif; ?>

            <label for="spiel">Nach Spiel filtern:</label>

            <select name="spiel" id="spiel">
                <option value="" <?php if ($gewaehltesSpiel === "") echo "selected"; ?>>
                    Alle
                </option>

                <?php foreach ($filterOptionen as $option): ?>
                    <option
                        value="<?php echo htmlspecialchars($option); ?>"
                        <?php if ($gewaehltesSpiel === $option) echo "selected"; ?>
                    >
                        <?php echo htmlspecialchars($option); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filtern</button>

        </form>

    </aside>

    <main class="post-feed">

        <h1>Beiträge</h1>

        <?php if (isset($datenbankFehler)): ?>

            <article class="post">
                <p class="post-text">
                    Fehler beim Laden der Beiträge:
                    <?php echo htmlspecialchars($datenbankFehler); ?>
                </p>
            </article>

        <?php elseif (empty($gefilterteBeitraege)): ?>

            <article class="post">
                <p class="post-text">
                    Es wurden noch keine Beiträge gefunden.
                </p>
            </article>

        <?php endif; ?>

        <?php foreach ($gefilterteBeitraege as $beitrag): ?>

            <?php
            $postId = $beitrag->getPostId();
            $postTitle = $beitrag->getPostTitle();
            $postText = $beitrag->getPostText();
            $postMedia = $beitrag->getPostMedia();
            $postAuthor = $beitrag->getPostAuthor();
            $postUrl = $beitrag->getPostUrl();
            $postDate = $beitrag->getPostDate();

            if ($postDate !== null) {
                $datum = date("d.m.Y", (int) $postDate);
            } else {
                $datum = "";
            }

            $detailLink = "beitrag.php";

            if ($postUrl !== null && $postUrl !== "") {
                $detailLink .= "?post=" . urlencode($postUrl);
            } elseif ($postId !== null) {
                $detailLink .= "?id=" . urlencode((string) $postId);
            }
            ?>

            <article class="post">

                <div class="post-header">

                    <div class="avatar"></div>

                    <div>
                        <h2>
                            <a href="<?php echo htmlspecialchars($detailLink); ?>">
                                <?php echo htmlspecialchars($postTitle); ?>
                            </a>
                        </h2>

                        <p class="post-date">
                            <?php if ($postAuthor !== null && $postAuthor !== ""): ?>
                                von <?php echo htmlspecialchars($postAuthor); ?>
                            <?php endif; ?>

                            <?php if ($datum !== ""): ?>
                                · gepostet am <?php echo htmlspecialchars($datum); ?>
                            <?php endif; ?>
                        </p>
                    </div>

                </div>

                <p class="post-text">
                    <?php echo nl2br(htmlspecialchars($postText)); ?>
                </p>

                <?php if ($postMedia !== null && trim($postMedia) !== ""): ?>
                    <p>
                        <img
                            class="post-image"
                            src="<?php echo htmlspecialchars($postMedia); ?>"
                            alt="Beitragsbild"
                        >
                    </p>
                <?php endif; ?>

                <?php if (!empty($beitrag->getPostTags())): ?>
                    <p class="post-tags">
                        <?php foreach ($beitrag->getPostTags() as $tag): ?>
                            <span>#<?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>

                <p class="post-actions">
                    <span>↑ 456 ↓</span>
                    <a href="<?php echo htmlspecialchars($detailLink); ?>">Kommentieren</a>
                    <a href="#">Speichern</a>
                </p>

            </article>

        <?php endforeach; ?>

    </main>

    <aside class="sidebar-right">

        <h2>Trending Tags</h2>

        <ol>
            <?php if (!empty($trendingTags)): ?>

                <?php foreach ($trendingTags as $tag): ?>
                    <li>#<?php echo htmlspecialchars($tag); ?></li>
                <?php endforeach; ?>

            <?php else: ?>

                <li>#achievement</li>
                <li>#gameplay</li>
                <li>#meme</li>
                <li>#minecraft</li>

            <?php endif; ?>
        </ol>

    </aside>

</div>

<footer class="footer">
    <?php include_once $abs_path . "/php/include/footer.php"; ?>
</footer>

</body>
</html>