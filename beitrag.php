<?php

require_once __DIR__ . "/path.php";
require_once $abs_path . "/php/controller/PostController.php";

$postController = new PostController();
$post= $postController->open($_GET["post"] ?? "");

if (!$post["found"]) {
    http_response_code(404);
} 

$author = null;
$title = "Beitrag";
if ($post["found"]) {
    $author = $post["post"]->getPostAuthor();
    $title = $post["post"]->getPostTitle();
    $p = $post["post"];
}

?>

<?php include_once "php/include/head.php"; ?>

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


        <?php if (!$post["found"] || $p === null): ?>

            <article class="post">
                <h1>Beitrag nicht gefunden</h1>
                <p>Der Beitrag existiert nicht oder wurde gelöscht.</p>
            </article>

        <?php else: ?>

            <article class="post">

                <div class="post-header">

                    <div class="avatar"></div>

                    <div>
                        <h1><?php echo htmlspecialchars($p->getPostTitle()); ?></h1>

                        <p class="post-date">
                            von <?php echo htmlspecialchars($p->getPostAuthor()); ?>

                            <?php if ($p->getPostDate() !== null): ?>
                                · <?php echo date ("d.m.Y H:i:s.", (int) htmlspecialchars($p->getPostDate())); ?>
                            <?php endif; ?>
                        </p>
                    </div>

                </div>

                <?php if ($p->getPostMedia() !== null && trim($p->getPostMedia()) !== ""): ?>
                    <p>
                        <img
                            class="post-image"
                            src="<?php echo htmlspecialchars($p->getPostMedia()); ?>"
                            alt="Beitragsbild"
                        >
                    </p>
                <?php endif; ?>

                <div class="post-text">
                    <?php echo htmlspecialchars($p->getPostText()); ?>
                </div>

                <?php if (!empty($p->getPostTags())): ?>
                    <p class="post-tags">
                        <?php foreach ($p->getPostTags() as $tag): ?>
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
