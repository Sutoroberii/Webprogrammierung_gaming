<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once __DIR__ . "/../../path.php";
}

$title = "Registrierung";
?>

<?php include_once $abs_path . "/php/include/head.php"; ?>

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

    <?php include_once $abs_path . "/php/include/nav.php"; ?>
</header>

<main class="auth-main">
    <section>
        <h1>Registrierung</h1>

        <?php if (isset($_SESSION["register_error"])): ?>
            <p class="error-message">
                <?= htmlspecialchars($_SESSION["register_error"], ENT_QUOTES, "UTF-8") ?>
            </p>
            <?php unset($_SESSION["register_error"]); ?>
        <?php endif; ?>

        <form action="benutzer_registrieren.php" method="POST">
            <div>
                <label for="benutzername">Benutzername:</label>
                <input 
                    type="text" 
                    id="benutzername" 
                    name="benutzername" 
                    value="<?= htmlspecialchars($_SESSION["old_benutzername"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                    required
                >
            </div>

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

            <div>
                <label for="passwortwiederholung">Passwort wiederholen:</label>
                <input 
                    type="password" 
                    id="passwortwiederholung" 
                    name="passwortwiederholung" 
                    required
                >
            </div>

            <button type="submit">Registrieren</button>

            <p>
                Schon ein Konto?
                <a href="anmeldung.php">Jetzt anmelden</a>
            </p>
        </form>

        <?php
        unset($_SESSION["old_benutzername"]);
        unset($_SESSION["old_email"]);
        ?>
    </section>
</main>

<footer class="footer">
    <?php include_once $abs_path . "/php/include/footer.php"; ?>
</footer>

</body>
</html>