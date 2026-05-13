<?php
$title = "Anmeldung";
?>

<?php include_once "php/head.php"; ?>

<body>

<header class="nav">
    <div class="logo">🎮 TwinkTavern</div>
    <?php include_once "php/nav.php"; ?>
</header>

<main class="auth-main">
    <section>
        <h1>Anmeldung</h1>

        <form action="anmeldung.php" method="POST">
            <div>
                <label for="email">E-Mail-Adresse:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="passwort">Passwort:</label>
                <input type="password" id="passwort" name="passwort" required>
            </div>

            <button type="submit">Anmelden</button>

            <p>
                Noch kein Konto?
                <a href="registrierung.php">Jetzt registrieren</a>
            </p>
        </form>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/footer.php"; ?>
</footer>

</body>
</html>