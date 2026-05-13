<?php
$title = "Neuer Beitrag";
?>

<?php include_once "php/head.php"; ?>

<body>

<header class="nav">
    <div class="logo">🎮 TwinkTavern</div>
    <?php include_once "php/nav.php"; ?>
</header>

<main class="auth-main">
    <section>
        <h1>Neuen Beitrag erstellen</h1>

        <form action="beitrag-neu.php" method="POST" enctype="multipart/form-data">
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
    </section>
</main>

<footer class="footer">
    <?php include_once "php/footer.php"; ?>
</footer>

</body>
</html>