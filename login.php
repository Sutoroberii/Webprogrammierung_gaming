<?php

require_once __DIR__ . "/path.php";

if (!isset($abs_path)) {
    $abs_path = __DIR__;
}

require_once $abs_path . "/php/controller/AuthentificationController.php";

$title = "Anmeldung";

$sessionControl = new SessionControl();

if (isset($_GET["action"]) && $_GET["action"] === "logout") {
    $sessionControl->logoutUser();
    header("Location: index.php");
    exit;
}

if ($sessionControl->isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$authControl = new AuthenticationController($sessionControl);

$loginError = null;
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    try {
        $result = $authControl->login($username, $password);

        if ($result["success"]) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            header("Location: index.php");
            exit;
        }

        $loginError = $result["error"] ?? "Anmeldung fehlgeschlagen.";
    } catch (Exception $e) {
        $loginError = "Bei der Anmeldung ist ein Fehler aufgetreten.";
    }
}

include_once $abs_path . "/php/include/head.php";

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

    <?php include_once $abs_path . "/php/include/nav.php"; ?>

</header>

<main class="auth-main">

    <section>

        <h1>Anmeldung</h1>

        <?php if ($loginError !== null): ?>
            <p class="error-message">
                <?php echo htmlspecialchars($loginError, ENT_QUOTES, "UTF-8"); ?>
            </p>
        <?php endif; ?>

        <form action="login.php" method="post">

            <div>
                <label for="username">Benutzername:</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Name"
                    value="<?php echo htmlspecialchars($username, ENT_QUOTES, "UTF-8"); ?>"
                    required
                >
            </div>

            <div>
                <label for="password">Passwort:</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Passwort"
                    required
                >
            </div>

            <button type="submit">Anmelden</button>

            <p>
                Noch kein Konto?
                <a href="register.php">Jetzt registrieren</a>
            </p>

        </form>

    </section>

</main>

<footer class="footer">
    <?php include_once $abs_path . "/php/include/footer.php"; ?>
</footer>

</body>
</html>