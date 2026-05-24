<?php
$title = "Nutzerliste";

$nutzer = [
    [
        "benutzername" => "Max",
        "email" => "max@test.de",
        "registriert_am" => "10.04.2026"
    ],
    [
        "benutzername" => "Lisa",
        "email" => "lisa@test.de",
        "registriert_am" => "12.04.2026"
    ]
];
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
    <?php include_once "php/nav.php"; ?>
</header>

    <section>
        <h1>Nutzerliste</h1>

        <table border="1">
            <tr>
                <th>Benutzername</th>
                <th>E-Mail-Adresse</th>
                <th>Datum der Registrierung</th>
                <th>Aktion</th>
            </tr>

            <?php foreach ($nutzer as $eintrag): ?>
                <tr>
                    <td><?php echo $eintrag["benutzername"]; ?></td>
                    <td><?php echo $eintrag["email"]; ?></td>
                    <td><?php echo $eintrag["registriert_am"]; ?></td>
                    <td>
                        <form action="nutzer-loeschen.php" method="POST">
                            <button type="submit">Löschen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>


<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>
</body>