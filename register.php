<?php

require_once __DIR__ . "/php/controller/AuthentificationController.php";

$sessionControl = new SessionControl();

if ($sessionControl->isLoggedIn()) {
    header("Location:./");
    exit();
}

$authControl = new AuthenticationController($sessionControl);
$registerError = null;
$mailFile = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $result = $authControl->register(
            $_POST["username"] ?? "",
            $_POST["email"] ?? "",
            $_POST["password"] ?? "",
            $_POST["confirmpassword"] ?? ""
        );

        if ($result["success"]) {
            $mailFile = $result["mail_file"];
        } else {
            $registerError = $result["error"];
        }
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
                <?= nl2br(htmlspecialchars($registerError, ENT_QUOTES, "UTF-8")) ?>
            </p>
        <?php endif; ?>

        <?php if ($mailFile !== null): ?>
            <p>Es wurde eine E-Mail an die angegebene Adresse mit weiteren Informationen verschickt.</p>
            <p>
                Weitere Infos finden Sie in der Datei
                <a
                    href="<?= htmlspecialchars($mailFile, ENT_QUOTES, 'UTF-8') ?>"
                    target="_blank"
                    rel="noopener"
                ><?= htmlspecialchars(basename($mailFile), ENT_QUOTES, 'UTF-8') ?></a>.
            </p>
        <?php else: ?>
            <form action="register.php" method="post">
                <div>
                    <label for="username">Benutzername:</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Name"
                        maxlength="10"
                        required
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <small id="username-check-message"></small>
                </div>

                <div>
                    <label for="email">E-Mail-Adresse:</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="E-Mail"
                        required
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <div>
                    <label for="password">Passwort:</label>
                    <input type="password" id="password" name="password" placeholder="Passwort" required>
                </div>

                <div>
                    <label for="confirmpassword">Passwort wiederholen:</label>
                    <input
                        type="password"
                        id="confirmpassword"
                        name="confirmpassword"
                        placeholder="Passwort"
                        required
                    >
                </div>

                <div class="form-check">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Ich habe die
                        <a href="datenschutz.php" target="_blank" rel="noopener">Datenschutzerklärung</a>
                        gelesen und akzeptiere die
                        <a href="nutzungsbedingungen.php" target="_blank" rel="noopener">Nutzungsbedingungen</a>.
                    </label>
                </div>

                <button type="submit" value="Registrieren">Registrieren</button>

                <p>
                    Schon ein Konto?
                    <a href="login.php">Jetzt anmelden</a>
                </p>
            </form>
        <?php endif; ?>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>

</body>
</html>
