<?php

require_once __DIR__ . "/path.php";
require_once $abs_path . "/php/model/User.php";

$title = "Profil bearbeiten";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];
$user = null;
$email = "";
$profilFehler = null;

try {
    $userDao = User::getInstance();

    if (method_exists($userDao, "getByUsername")) {
        $user = $userDao->getByUsername($username);
    }

} catch (Throwable $e) {
    $profilFehler = "Die Profildaten konnten nicht geladen werden.";
}

if ($user !== null && method_exists($user, "getEmail")) {
    $email = $user->getEmail();
}

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
    <?php include_once "php/include/nav.php"; ?>
</header>

<main class="auth-main">
    <section>
        <h1>Profil bearbeiten</h1>


        <form action="profil-bearbeiten.php" method="POST">
            <fieldset>
                <legend>Persönliche Daten</legend>

                <div>
                    <label for="benutzername">Benutzername:</label>
                    <input 
                        type="text" 
                        id="benutzername" 
                        name="benutzername" 
                        value="<?php echo htmlspecialchars($username, ENT_QUOTES, "UTF-8"); ?>" 
                        readonly
                    >
                </div>

                <div>
                    <label for="email">E-Mail-Adresse:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?>" 
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
    <?php include_once $abs_path . "/php/include/footer.php"; ?>
</footer>

</body>
</html>