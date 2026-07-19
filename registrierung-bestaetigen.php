<?php

require_once __DIR__ . "/php/controller/AuthentificationController.php";

$sessionControl = new SessionControl();
$authControl = new AuthenticationController($sessionControl);
$token = $_GET['token'] ?? '';

try {
    $result = $authControl->confirmRegistration($token);
} catch (RuntimeException $e) {
    $result = ['success' => false, 'error' => $e->getMessage()];
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
        <h1>Registrierung bestätigen</h1>

        <?php if ($result['success']): ?>
            <p>Die Registrierung wurde erfolgreich abgeschlossen.</p>
            <p><a href="login.php">Jetzt anmelden</a></p>
        <?php else: ?>
            <p class="error-message">
                <?= htmlspecialchars($result['error'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <p><a href="register.php">Neue Registrierung starten</a></p>
        <?php endif; ?>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>
</body>
</html>
