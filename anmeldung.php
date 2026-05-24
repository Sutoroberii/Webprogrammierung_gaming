<?php
$title = "Anmeldung";
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
    <?php include_once "php/include/nav.php"; ?>
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
    <?php include_once "php/include/footer.php"; ?>
</footer>

</body>
</html>

<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if(!isset($abs_path)) {
    require_once "path.php";
}

require_once $abs_path . "anmeldung.php";