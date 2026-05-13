<?php
$title = "Profil bearbeiten";
?>

<?php include_once "php/head.php"; ?>

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

<main class="auth-main">
    <section>
        <h1>Profil bearbeiten</h1>

        <p class="auth-info">Hier können Sie Ihre Profildaten ändern.</p>

        <form action="profil-bearbeiten.php" method="POST">
            <fieldset>
                <legend>Persönliche Daten</legend>

                <div>
                    <label for="benutzername">Benutzername:</label>
                    <input 
                        type="text" 
                        id="benutzername" 
                        name="benutzername" 
                        value="MaxMustermann" 
                        required
                    >
                </div>

                <div>
                    <label for="email">E-Mail-Adresse:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="max@example.de" 
                        required
                    >
                </div>
            </fieldset>

            <fieldset>
                <legend>Passwort ändern</legend>

                <div>
                    <label for="altes-passwort">Altes Passwort:</label>
                    <input 
                        type="password" 
                        id="altes-passwort" 
                        name="altes_passwort"
                    >
                </div>

                <div>
                    <label for="neues-passwort">Neues Passwort:</label>
                    <input 
                        type="password" 
                        id="neues-passwort" 
                        name="neues_passwort"
                    >
                </div>

                <div>
                    <label for="passwort-wiederholen">Neues Passwort wiederholen:</label>
                    <input 
                        type="password" 
                        id="passwort-wiederholen" 
                        name="passwort_wiederholen"
                    >
                </div>
            </fieldset>

            <button type="submit">Änderungen speichern</button>
        </form>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/footer.php"; ?>
</footer>

</body>
</html>