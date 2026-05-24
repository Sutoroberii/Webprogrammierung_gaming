<?php
$title = "Registrierung";
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
        <h1>Registrierung</h1>

        <form action="registrierung.php" method="POST">
            <div>
                <label for="email">E-Mail-Adresse:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="passwort">Passwort:</label>
                <input type="password" id="passwort" name="passwort" required>
            </div>

            <div>
                <label for="passwort_wiederholen">Passwort wiederholen:</label>
                <input type="password" id="passwort_wiederholen" name="passwort_wiederholen" required>
            </div>

            <button type="submit">Registrieren</button>

            <p>
                Du hast schon ein Konto?
                <a href="anmeldung.php">Zur Anmeldung</a>
            </p>
        </form>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>

</body>
</html>