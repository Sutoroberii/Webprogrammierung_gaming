<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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

        <?php if (isset($_SESSION["login_error"])): ?>
            <p class="error-message">
                <?= htmlspecialchars($_SESSION["login_error"], ENT_QUOTES, "UTF-8") ?>
            </p>
            <?php unset($_SESSION["login_error"]); ?>
        <?php endif; ?>

    <form action="anmeldung_verarbeiten.php" method="POST">
        <div>
            <label for="email">E-Mail-Adresse:</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= htmlspecialchars($_SESSION["old_email"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >
        </div>

        <div>
            <label for="passwort">Passwort:</label>
            <input 
                type="password" 
                id="passwort" 
                name="passwort" 
                required
            >
        </div>

        <button type="submit">Anmelden</button>

        <p>
            Noch kein Konto?
            <a href="registrierung.php">Jetzt registrieren</a>
        </p>
    </form>

        <?php unset($_SESSION["old_email"]); ?>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>

</body>
</html>