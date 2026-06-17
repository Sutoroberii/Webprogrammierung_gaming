<?php

require_once __DIR__ . "/path.php";

require_once $abs_path . "/php/model/User.php";
require_once $abs_path . "/php/model/Post.php";

$title = "Profil";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];

$user = null;
$meineBeitraege = [];
$profilFehler = null;

try {
    $userDao = User::getInstance();

    if (method_exists($userDao, "getByUsername")) {
        $user = $userDao->getByUsername($username);
    }

} catch (Throwable $e) {
    $profilFehler = "Die Profildaten konnten nicht geladen werden.";
}

try {
    $postDao = Post::getInstance();
    $alleBeitraege = $postDao->findAll();

    foreach ($alleBeitraege as $beitrag) {
        if ($beitrag->getPostAuthor() === $username) {
            $meineBeitraege[] = $beitrag;
        }
    }

} catch (Throwable $e) {
    $profilFehler = "Die Beiträge konnten nicht geladen werden.";
}

$email = "";

if ($user !== null && method_exists($user, "getEmail")) {
    $email = $user->getEmail();
}

$creationDate = "";

if ($user !== null && method_exists($user, "getCreationDate")) {
    $creationDate = $user->getCreationDate();
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

        <?php if (file_exists($abs_path . "/profil-bearbeiten.php")): ?>
            <a href="profil-bearbeiten.php" class="button-link">Profil bearbeiten</a>
        <?php endif; ?>

        <a href="login.php?action=logout" class="button-link">Abmelden</a>

    </aside>

    <main class="post-feed">

        <h1>Profil</h1>

        <?php if ($profilFehler !== null): ?>
            <article class="post">
                <p class="error-message">
                    <?php echo htmlspecialchars($profilFehler, ENT_QUOTES, "UTF-8"); ?>
                </p>
            </article>
        <?php endif; ?>

        <article class="post">

            <h2>Persönliche Informationen</h2>

            <p>
                <strong>Benutzername:</strong>
                <?php echo htmlspecialchars($username, ENT_QUOTES, "UTF-8"); ?>
            </p>

            <p>
                <strong>E-Mail-Adresse:</strong>
                <?php if ($email !== ""): ?>
                    <?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?>
                <?php else: ?>
                    Nicht gefunden
                <?php endif; ?>
            </p>

            <p>
                <strong>Registriert seit:</strong>
                <?php if ($creationDate !== ""): ?>
                    <?php echo htmlspecialchars($creationDate, ENT_QUOTES, "UTF-8"); ?>
                <?php else: ?>
                    Nicht gefunden
                <?php endif; ?>
            </p>

        </article>

        <h2>Meine Beiträge</h2>

        <?php if (empty($meineBeitraege)): ?>

            <article class="post">
                <p>Du hast noch keine Beiträge erstellt.</p>

                <p>
                    <a href="beitrag-neu.php" class="button-link">
                        Ersten Beitrag erstellen
                    </a>
                </p>
            </article>

        <?php else: ?>

            <?php foreach ($meineBeitraege as $beitrag): ?>

                <?php
                $postId = $beitrag->getPostId();
                $postTitle = $beitrag->getPostTitle();
                $postText = $beitrag->getPostText();
                $postMedia = $beitrag->getPostMedia();
                $postUrl = $beitrag->getPostUrl();
                $postDate = $beitrag->getPostDate();

                $datum = "";

                if ($postDate !== null && $postDate !== "") {
                    if (is_numeric($postDate)) {
                        $datum = date("d.m.Y", (int) $postDate);
                    } else {
                        $datum = $postDate;
                    }
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
                            <h3>
                                <a href="<?php echo htmlspecialchars($detailLink, ENT_QUOTES, "UTF-8"); ?>">
                                    <?php echo htmlspecialchars($postTitle, ENT_QUOTES, "UTF-8"); ?>
                                </a>
                            </h3>

                            <?php if ($datum !== ""): ?>
                                <p class="post-date">
                                    Gepostet am <?php echo htmlspecialchars($datum, ENT_QUOTES, "UTF-8"); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                    </div>

                    <?php if ($postMedia !== null && trim($postMedia) !== ""): ?>
                        <p>
                            <img
                                class="post-image"
                                src="<?php echo htmlspecialchars($postMedia, ENT_QUOTES, "UTF-8"); ?>"
                                alt="Beitragsbild"
                            >
                        </p>
                    <?php endif; ?>

                    <p class="post-text">
                        <?php echo nl2br(htmlspecialchars($postText ?? "", ENT_QUOTES, "UTF-8")); ?>
                    </p>

                    <?php if (!empty($beitrag->getPostTags())): ?>
                        <p class="post-tags">
                            <?php foreach ($beitrag->getPostTags() as $tag): ?>
                                <span>#<?php echo htmlspecialchars($tag, ENT_QUOTES, "UTF-8"); ?></span>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>

                    <p class="post-actions">
                        <a href="<?php echo htmlspecialchars($detailLink, ENT_QUOTES, "UTF-8"); ?>">
                            Beitrag öffnen
                        </a>
                    </p>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </main>

    <aside class="sidebar-right">

        <h2>Profilaktionen</h2>

        <ol>
            <li>
                <a href="beitrag-neu.php">Neuen Beitrag erstellen</a>
            </li>

            <?php if (file_exists($abs_path . "/profil-bearbeiten.php")): ?>
                <li>
                    <a href="profil-bearbeiten.php">Profil bearbeiten</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="login.php?action=logout">Abmelden</a>
            </li>
        </ol>

    </aside>

</div>

<footer class="footer">
    <?php include_once $abs_path . "/php/include/footer.php"; ?>
</footer>

</body>
</html>