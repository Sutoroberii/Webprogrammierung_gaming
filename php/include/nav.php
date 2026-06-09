<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$eingeloggt = isset($_SESSION["username"]);

?>

<nav class="nav-center-links">

    <a href="index.php">Startseite</a>

    <?php if ($eingeloggt): ?>

        <a href="beitrag-neu.php">Neuer Beitrag</a>
        <a href="profil.php">Profil</a>
        <a href="profil-bearbeiten.php">Profil bearbeiten</a>
        <a href="nutzerliste.php">Nutzerliste</a>
        <a href="login.php?action=logout">Abmelden</a>

    <?php else: ?>

        <a href="register.php">Registrierung</a>
        <a href="login.php">Anmeldung</a>
        <a href="nutzerliste.php">Nutzerliste</a>

    <?php endif; ?>

</nav>