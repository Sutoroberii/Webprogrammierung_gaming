<?php
$title = "Anmeldung";
?>

<?php include_once "php/head.php"; ?>
<body>

<?php include_once "php/nav.php"; ?>

<main>
    <section>
        <h1>Anmeldung</h1>

        <form action="anmeldung.php" method="POST">
            <div>
                <label for="email">E-Mail-Adresse:</label><br>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="passwort">Passwort:</label><br>
                <input type="password" id="passwort" name="passwort" required>
            </div>

            <br>
            <button type="submit">Anmelden</button>
        </form>
    </section>
</main>

<?php include_once "php/footer.php"; ?>
</body>