<?php

require_once __DIR__ . "/php/controller/AuthentificationController.php";

$sessionControl = new SessionControl();
$authControl = new AuthenticationController($sessionControl);
$token = $_POST['token'] ?? $_GET['token'] ?? '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $result = $authControl->changePassword(
            $token,
            $_POST['password'] ?? '',
            $_POST['confirmpassword'] ?? ''
        );
    } catch (RuntimeException $e) {
        $result = ['success' => false, 'error' => $e->getMessage()];
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
        <h1>Passwort ändern</h1>

        <?php if ($result !== null && $result['success']): ?>
            <p>Das Passwort wurde erfolgreich geändert.</p>
            <p><a href="login.php">Jetzt anmelden</a></p>
        <?php else: ?>
            <?php if ($result !== null): ?>
                <p class="error-message">
                    <?= nl2br(htmlspecialchars($result['error'], ENT_QUOTES, 'UTF-8')) ?>
                </p>
            <?php endif; ?>

            <form action="passwort-aendern.php" method="post">
                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"
                >

                <div>
                    <label for="password">Neues Passwort:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div>
                    <label for="confirmpassword">Passwort wiederholen:</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" required>
                </div>

                <button type="submit">Passwort speichern</button>
            </form>
        <?php endif; ?>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>
</body>
</html>
