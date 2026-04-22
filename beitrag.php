<?php
$title = "Beitrag";

$beitrag = [
    "autor" => "Tomi",
    "spiel" => "Minecraft",
    "datum" => "21.04.2026",
    "bild" => "bild1.jpg",
    "text" => "Das ist ein einzelner Beitrag mit Bild und Text."
];

$istAutor = true;  
$istAdmin = false;  
?>

<?php include_once "php/head.php"; ?>
<body>

<?php include_once "php/nav.php"; ?>

<main>
    <section>
        <h1>Beitrag</h1>

        <p>Autor: <?php echo $beitrag["autor"]; ?></p>
        <p>Spielename: <?php echo $beitrag["spiel"]; ?></p>
        <p>Datum der Erstellung: <?php echo $beitrag["datum"]; ?></p>

        <p>
            <img src="<?php echo $beitrag["bild"]; ?>" alt="Beitragsbild" width="300">
        </p>

        <p><?php echo $beitrag["text"]; ?></p>

        <?php if ($istAutor): ?>
            <p>
                <a href="beitrag-bearbeiten.php">Ändern</a>
            </p>
            <form action="beitrag-loeschen.php" method="POST">
                <button type="submit">Löschen</button>
            </form>
        <?php endif; ?>

        <?php if ($istAdmin): ?>
            <form action="beitrag-loeschen.php" method="POST">
                <button type="submit">Löschen</button>
            </form>
        <?php endif; ?>
    </section>
</main>

<?php include_once "php/footer.php"; ?>
</body>