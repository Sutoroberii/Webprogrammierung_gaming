<?php
require_once __DIR__ . "/php/controller/AuthentificationController.php";

$sessionControl = new SessionControl();
if ($sessionControl->isLoggedIn()) {
    header("Location:./");
    exit();
}

$authControl = new AuthentificationController($sessionControl);
$loginError = null;

if (isset($_GET["action"]) && $_GET["action"] === "logout") {
    $sessionControl->logoutUser();
    header("Location:./");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $result = $authControl->login($_POST["username"] ?? "", $_POST["password"] ?? "");
    if ($result["success"]) {
        header("Location:./");
        exit();
    } else {
        $loginError = $result["error"];
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
        <h1>Anmeldung</h1>

        <?php if ($loginError !== null): ?>
            <p class="error-message">
                <?= htmlspecialchars($loginError, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div>
                <label for="username">Benutzername:</label>
                <input type="text" id="username" name="username" placeholder="Name" value="<?php echo htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8") ?>" required><br>
            </div>

            <div>
                <label for="passwort">Passwort:</label>
                <input type="password" id="password" name="password" placeholder="Passwort" required><br>
            </div>

            <button type="submit" value="Anmelden">Anmelden</button>

            <p>
                Noch kein Konto?
                <a href="register.php">Jetzt registrieren</a>
            </p>
        </form>

    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>

</body>
</html>