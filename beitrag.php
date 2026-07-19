<?php

require_once __DIR__ . "/path.php";
require_once $abs_path . "/php/controller/PostController.php";

$postController = new PostController();
$post = $postController->open($_GET["post"] ?? "");

if (!$post["found"]) {
    http_response_code(404);
}

$author = null;
$title = "Beitrag";
$p = null;
if ($post["found"]) {
    $author = $post["post"]->getPostAuthor();
    $title = $post["post"]->getPostTitle();
    $p = $post["post"];
}

$media = $p->getPostMedia();

if (preg_match('/^\/data\/uploads\/posts\/[a-zA-Z0-9._-]+$/', $media)) {
    echo htmlspecialchars($media);
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
                                    · <?php echo date("d.m.Y H:i:s.", (int) htmlspecialchars($p->getPostDate())); ?>
                                <?php endif; ?>
                            </p>
                        </div>

                    </div>

                    <?php if ($p->getPostMedia() !== null && trim($p->getPostMedia()) !== ""): ?>
                        <p>
                            <img class="post-image" style='max-width:200px' src="<?php echo htmlspecialchars($media); ?>"
                                alt="Beitragsbild">
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

                    <?php if (isset($_SESSION["username"]) && $_SESSION["username"] === $p->getPostAuthor()): ?>
                        <div class="post-edit-actions">
                            <a href="beitrag-neu.php?id=<?php echo $p->getPostId(); ?>" class="button-link">Bearbeiten</a>
                            <form action="post.php" method="POST" class="delete-post-form">
                                <input type="hidden" name="action" value="deletePost">
                                <input type="hidden" name="deletePostId" value="<?php echo $p->getPostId(); ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit">Löschen</button>
                            </form>
                        </div>
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
    <script src="js/beitrag-löschen.js"></script>
</body>

</html>