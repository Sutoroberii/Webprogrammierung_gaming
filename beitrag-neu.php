<?php
$title = "Neuer Beitrag";
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
        <h1>Neuen Beitrag erstellen</h1>

        <article class="post">
            <form action="beitrag-neu.php" method="POST" enctype="multipart/form-data" class="post-form">

                <div>
                    <label for="spiel">Spielename:</label>
                    <input 
                        type="text" 
                        id="spiel" 
                        name="spiel" 
                        required
                    >
                </div>

                <div>
                    <label for="text">Text des Beitrags:</label>
                    <textarea 
                        id="text" 
                        name="text" 
                        rows="8" 
                        required
                    ></textarea>
                </div>

                <div>
                    <label for="bild">Bild hochladen:</label>
                    <input 
                        type="file" 
                        id="bild" 
                        name="bild" 
                        accept="image/*"
                    >
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
    <?php include_once "php/footer.php"; ?>
</footer>

</body>
</html>