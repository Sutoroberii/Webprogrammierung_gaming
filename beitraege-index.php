<?php
$title = "Beiträge";
$eingeloggt = true; 
$beitraege = [
    [
        "bild" => "bild1.jpg",
        "text" => "Das ist ein Beispielbeitrag zu Minecraft.",
        "datum" => "21.04.2026",
        "spiel" => "Minecraft"
    ],
    [
        "bild" => "bild2.jpg",
        "text" => "Das ist ein Beispielbeitrag zu Stardew Valley.",
        "datum" => "20.04.2026",
        "spiel" => "Stardew Valley"
    ]
];
?>

<?php include_once "php/head.php"; ?>
<body>

<?php include_once "php/nav.php"; ?>

<main>
    <section>
        <h1>Beiträge</h1>

        <form method="GET" action="beitraege-index.php">
            <label for="spiel">Nach Spiel filtern:</label>
            <select name="spiel" id="spiel">
                <option value="">Alle</option>
                <option value="Minecraft">Minecraft</option>
                <option value="Stardew Valley">Stardew Valley</option>
                <option value="Valorant">Valorant</option>
            </select>
            <button type="submit">Filtern</button>
        </form>

        <?php if ($eingeloggt): ?>
            <p>
                <a href="beitrag-neu.php">Neuer Eintrag</a>
            </p>
        <?php endif; ?>

        <?php foreach ($beitraege as $beitrag): ?>
            <article>
                <h2><?php echo $beitrag["spiel"]; ?></h2>
                <p><img src="<?php echo $beitrag["bild"]; ?>" alt="Beitragsbild" width="200"></p>
                <p><?php echo $beitrag["text"]; ?></p>
                <p>Datum: <?php echo $beitrag["datum"]; ?></p>
                <p><a href="beitrag.php">Zum Beitrag</a></p>
                <hr>
            </article>
        <?php endforeach; ?>
    </section>
</main>

<?php include_once "php/footer.php"; ?>
</body>