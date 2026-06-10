<?php

if (!isset($abs_path)) {
    require_once __DIR__ . "/../path.php";
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

    <?php include_once $abs_path . "/php/include/nav.php"; ?>

</header>

<div class="layout">

    <aside class="sidebar-left">
        <a href="index.php" class="button-link">Startseite</a>
        <a href="beitrag-neu.php" class="button-link">Neuer Beitrag</a>
    </aside>

    <main class="post-feed">

        <?php if ($error !== null): ?>

            <article class="post">
                <h1>Fehler</h1>
                <p><?php echo htmlspecialchars($error); ?></p>
            </article>

        <?php elseif (!$found || $entry === null): ?>

            <article class="post">
                <h1>Beitrag nicht gefunden</h1>
                <p>Der Beitrag existiert nicht oder wurde gelöscht.</p>
            </article>

        <?php else: ?>

            <article class="post">

                <div class="post-header">

                    <div class="avatar"></div>

                    <div>
                        <h1><?php echo htmlspecialchars($entry->getPostTitle()); ?></h1>

                        <p class="post-date">
                            von <?php echo htmlspecialchars($entry->getPostAuthor()); ?>

                            <?php if ($entry->getPostDate() !== null): ?>
                                · <?php echo date("d.m.Y", (int) $entry->getPostDate()); ?>
                            <?php endif; ?>
                        </p>
                    </div>

                </div>

                <?php if ($entry->getPostMedia() !== null && trim($entry->getPostMedia()) !== ""): ?>
                    <p>
                        <img
                            class="post-image"
                            src="<?php echo htmlspecialchars($entry->getPostMedia()); ?>"
                            alt="Beitragsbild"
                        >
                    </p>
                <?php endif; ?>

                <div class="post-text">
                    <?php echo $html; ?>
                </div>

                <?php if (!empty($entry->getPostTags())): ?>
                    <p class="post-tags">
                        <?php foreach ($entry->getPostTags() as $tag): ?>
                            <span>#<?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>

            </article>

        <?php endif; ?>

    </main>

    <aside class="sidebar-right">
        <h2>Aktionen</h2>

        <ol>
            <li><a href="index.php">Zurück zur Startseite</a></li>
            <li><a href="beitrag-neu.php">Weiteren Beitrag erstellen</a></li>
        </ol>
    </aside>

</div>

<footer class="footer">
    <?php include_once $abs_path . "/php/include/footer.php"; ?>
</footer>

</body>
</html>