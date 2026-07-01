<?php
require_once __DIR__ . "/php/controller/AuthentificationController.php";

$sessionControl = new SessionControl();
if ($sessionControl->isLoggedIn()) {
    header("Location:./");
    exit();
}

$authControl = new AuthenticationController($sessionControl);
$registerError = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $result = $authControl->register(
            $_POST["username"] ?? "",
            $_POST["email"] ?? "",
            $_POST["password"] ?? "",
            $_POST["confirmpassword"] ?? ""
        );

        if ($result["success"]) {
            header("Location:./");
            exit();
        }

        $registerError = $result["error"];
    } catch (RuntimeException $e) {
        $registerError = $e->getMessage();
    }
}

include_once "php/include/head.php"; 
?>


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

        <?php if ($registerError !== null): ?>
            <p class="error-message">
                <?= htmlspecialchars($registerError, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form action="register.php" method="post">
            <div>
                <label for="username">Benutzername:</label>

                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Name"
                    value="<?php echo htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8") ?>">

                <small id="username-check-message"></small>
            </div>

            <div>
                <label for="email">E-Mail-Adresse:</label>
                <input type="text" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8") ?>">
            </div>

            <div>
                <label for="passwort">Passwort:</label>
                <input type="password" id="password" name="password" placeholder="Passwort">
            </div>

            <div>
                <label for="confirmpassword">Passwort wiederholen:</label>
                <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Passwort">
            </div>

            <div class="form-check">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">
                    Ich habe die <a href="datenschutz.php" target="_blank">Datenschutzerklärung</a> gelesen und
                    akzeptiere die <a href="nutzungsbedingungen.php" target="_blank">Nutzungsbedingungen</a>.
                </label>
            </div>


            <button type="submit", value="Registrieren">Registrieren</button>

            <p>
                Schon ein Konto?
                <a href="login.php">Jetzt anmelden</a>
            </p>
        </form>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>

<script src="js/benutzername_Kontrolle.js"></script>
</body>
</html>