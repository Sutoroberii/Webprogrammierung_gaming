<?php
require_once "php/controller/PostController.php";
require_once "php/model/SessionControl.php";
require_once "php/model/UserDao.php";
require_once "php/model/User.php";

$postController = new PostController();
$sessionControl = new SessionControl();
$userDao = User::getInstance();
$user = $sessionControl->getLoggedInUsername();

if (isset($_GET['deletePost'])) {
    if ($user === null) {
        header('Location: index.php');
        exit;
    }
    $deletePostId = (int) $_GET['deletePost'];
    $deleteResult = $postController->delete($deletePostId, $user);
    if ($deleteResult['success']) {
        header('Location: index.php');
        exit;
    }
    $deleteError = $deleteResult['errors'][0] ?? 'Unbekannter Fehler';
}

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'createPost') {
        $formResult = $postController->createNewPost(['postTitle' => $_POST['title'] ?? '', 'postTags' => $_POST['tags'] ?? '', 'postMedia' => '', 'postMediaFile' => $_FILES['media'] ?? [], 'postText' => $_POST['text'] ?? '', 'postAuthor' => $user]);

        if ($formResult['success']) {
            header('Location: post.php?post=' . urlencode($formResult['post']->getPostUrl()));
            exit;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'editPost') {
        $formResult = $controller->update(['postId' => (int) ($_POST['id'] ?? 0), 'postTitle' => $_POST['title'] ?? '', 'postTags' => $_POST['tags'] ?? '', 'postMedia' => '', 'postMediaFile' => $_FILES['media'] ?? [], 'postText' => $_POST['text'] ?? '', 'postAuthor' => $user]);

        if ($formResult['success']) {
            header('Location: post.php?post=' . urlencode($formResult['post']->getPostUrl()));
            exit;
        }
    }
}
include_once "php/include/head.php";


//Neuer Post
if (isset($_GET['create'])) {
    if ($user === null) {
        header('Location: index.php');
        exit;
    }
    ?>

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
                <button> <a href="beitrag-neu.php" class="button-link">Erstelle einen Beitrag</a></button>
                <div class="icon">🔔</div>
                <a href="profil.php" class="icon" aria-label="Profil">👤</a>
            </div>
        </header>
        <div class="layout">
            <aside class="sidebar-left">
                <a href="index.php" class="button-link">Startseite</a>
            </aside>
            <main class="post-feed">

                <h1>Neuen Beitrag erstellen</h1>
                <article class="post">
                    <form action="beitrag-eintragen.php" method="POST" enctype="multipart/form-data" class="post-form">
                        <div>
                            <label for="spiel">Spielename:</label>
                            <input type="text" id="game" name="spiel" required>
                        </div>
                        <div>
                            <label for="text">Text des Beitrags:</label>
                            <textarea id="text" name="text" rows="8" required></textarea>
                        </div>
                        <div>
                            <label for="bild">Bild hochladen:</label>
                            <input type="file" id="media" name="bild" accept="image/png, image/jpeg">
                        </div>
                        <button type="submit">Beitrag speichern</button>
                    </form>
                </article>
            </main>
            <aside class="sidebar-right">
                <h2>Hinweise</h2>
                <ol>
                    <li>Wähle einen passenden Spielnamen.</li>
                    <li>Beschreibe deinen Beitrag verständlich.</li>
                    <li>Optional kannst du ein Bild hochladen.</li>
                </ol>
            </aside>
        </div>
        <footer class="footer">
            <?php include_once "php/include/footer.php"; ?>
        </footer>
    </body>
    </html>
    <?php
    exit;
}

//Bestehender Post
//to do: 
//bestehende Seite umbauen