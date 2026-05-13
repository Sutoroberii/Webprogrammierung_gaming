<?php
$title = "Profil ansehen";
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
        <h1>Profil</h1>

        <article class="profile-box">
            <h2>Persönliche Informationen</h2>

            <p><strong>Benutzername:</strong> MaxMustermann</p>
            <p><strong>E-Mail-Adresse:</strong> max@example.de</p>
            <p><strong>Registriert seit:</strong> 10.04.2026</p>
        </article>

        <div class="profile-box">
            <h2>Meine Beiträge</h2>

            <ul class="profile-list">
                <li><a href="beitrag.php">Minecraft-Bauprojekt</a></li>
                <li><a href="beitrag.php">Stardew Valley Farm</a></li>
            </ul>
        </div>

        <div class="profile-actions">
            <a href="profil-bearbeiten.php">Profil bearbeiten</a>
        </div>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/footer.php"; ?>
</footer>

</body>
</html>