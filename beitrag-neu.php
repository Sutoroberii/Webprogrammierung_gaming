<?php
$title = "Neuer Beitrag";
?>

<?php include_once "php/head.php"; ?>
<body>

<?php include_once "php/nav.php"; ?>

<main>
    <section>
        <h1>Neuen Beitrag erstellen</h1>

        <form action="beitrag-neu.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="spiel">Spielename:</label><br>
                <input type="text" id="spiel" name="spiel" required>
            </div>

            <div>
                <label for="text">Text des Beitrags:</label><br>
                <textarea id="text" name="text" rows="8" cols="50" required></textarea>
            </div>

            <div>
                <label for="bild">Bild hochladen:</label><br>
                <input type="file" id="bild" name="bild" accept="image/*">
            </div>

            <br>
            <button type="submit">Beitrag speichern</button>
        </form>
    </section>
</main>

<?php include_once "php/footer.php"; ?>
</body>