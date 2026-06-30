<?php

require_once __DIR__ . "/path.php";

$title = "Neuer Beitrag";

$loginPage = file_exists(__DIR__ . "/login.php") ? "login.php" : "anmeldung.php";

if (!isset($_SESSION["username"])) {
    header("Location: " . $loginPage);
    exit;
}

$errors = $_SESSION["post_errors"] ?? [];
$oldData = $_SESSION["old_post_data"] ?? [];

unset($_SESSION["post_errors"]);
unset($_SESSION["old_post_data"]);

$isEdit = false;
$editPostId = 0;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    require_once $abs_path . "/php/controller/PostController.php";
    $postController = new PostController();
    $existingPost = $postController->findById((int) $_GET['id']);


    if ($existingPost !== null && $existingPost->getPostAuthor() === $_SESSION["username"]) {
        $isEdit = true;
        $editPostId = $existingPost->getPostId();
        $title = "Beitrag bearbeiten";

        if (empty($oldData)) {
            $oldData["postTitle"] = $existingPost->getPostTitle();
            $oldData["postTags"] = implode(", ", $existingPost->getPostTags());
            $oldData["postText"] = $existingPost->getPostText();
        }
    }
}

$postTitle = htmlspecialchars($oldData["postTitle"] ?? "");
$postTags = htmlspecialchars($oldData["postTags"] ?? "");
$postText = htmlspecialchars($oldData["postText"] ?? "");

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
            <a href="beitrag-neu.php" class="button-link create-post">
                Erstelle einen Beitrag
            </a>

            <div class="icon">🔔</div>

            <a href="profil.php" class="icon" aria-label="Profil">👤</a>
        </div>

    </header>

    <div class="layout">

        <aside class="sidebar-left">
            <a href="index.php" class="button-link">Startseite</a>
        </aside>

        <main class="post-feed">

            <h1><?php echo $isEdit ? "Beitrag bearbeiten" : "Neuen Beitrag erstellen"; ?></h1>

            <?php if (!empty($errors)): ?>
                <article class="post">
                    <h2>Fehler</h2>

                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endif; ?>

            <article class="post">

                <form action="post.php" method="POST" enctype="multipart/form-data" class="post-form">

                    <div>
                        <label for="postTitle">Spielname / Titel:</label>
                        <input type="text" id="postTitle" name="postTitle" value="<?php echo $postTitle; ?>" required>
                        <input type="hidden" name="action" value="<?php echo $isEdit ? 'editPost' : 'createPost'; ?>">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?php echo $editPostId; ?>">
                        <?php endif; ?>

                    </div>

                    <div>
                        <label for="postTags">Tags:</label>
                        <input type="text" id="postTags" name="postTags" value="<?php echo $postTags; ?>"
                            placeholder="z.B. Minecraft, Survival, Build">
                    </div>

                    <div>
                        <label for="postText">Text des Beitrags:</label>
                        <textarea id="postText" name="postText" rows="8" required><?php echo $postText; ?></textarea>
                    </div>

                    <div>
                        <label for="postMediaFile">Bild hochladen:</label>

                        <div id="dropZone" class="drop-zone">
                            <p>Datei hierher ziehen oder klicken zum Auswählen</p>
                            <input type="file" id="postMediaFile" name="postMediaFile" accept=".jpeg,.png" hidden>
                        </div>

                        <div id="preview" style='max-width:200px'></div>
                    </div>

                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <button type="submit">Beitrag speichern</button>

                </form>

            </article>

        </main>

        <aside class="sidebar-right">

            <h2>Hinweise</h2>

            <ol>
                <li>Wähle einen passenden Spielnamen oder Titel.</li>
                <li>Beschreibe deinen Beitrag verständlich.</li>
                <li>Tags kannst du mit Kommas trennen.</li>
                <li>Optional kannst du ein Bild hochladen.</li>
            </ol>

        </aside>

    </div>

    <footer class="footer">
        <?php include_once $abs_path . "/php/include/footer.php"; ?>
    </footer>

</body>

</html>

<script>
    const dropZone = document.getElementById("dropZone");
    const fileInput = document.getElementById("postMediaFile");
    const preview = document.getElementById("preview");

    // Klick öffnet Dateiauswahl
    dropZone.addEventListener("click", () => fileInput.click());

    // Drag over Styling
    dropZone.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZone.classList.add("dragover");
    });

    // Drag leave
    dropZone.addEventListener("dragleave", () => {
        dropZone.classList.remove("dragover");
    });

    // Datei droppen
    dropZone.addEventListener("drop", (e) => {
        e.preventDefault();
        dropZone.classList.remove("dragover");

        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files; // wichtig für PHP Upload
            showPreview(files[0]);
        }
    });

    // Wenn Datei normal ausgewählt wird
    fileInput.addEventListener("change", () => {
        if (fileInput.files.length) {
            showPreview(fileInput.files[0]);
        }
    });

    // Preview
    function showPreview(file) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };

        reader.readAsDataURL(file);
    }
</script>