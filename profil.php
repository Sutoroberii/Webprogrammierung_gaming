<?php
$title = "Profil ansehen";
include_once "php/head.php";
include_once "php/nav.php";
?>

<main>
    <section>
        <h2>Profil</h2>
        <p>Hier sehen Sie Ihre Profildaten.</p>

        <article>
            <h3>Persönliche Informationen</h3>

            <p><strong>Benutzername:</strong> MaxMustermann</p>
            <p><strong>E-Mail-Adresse:</strong> max@example.de</p>
            <p><strong>Registriert seit:</strong> 10.04.2026</p>
        </article>

        <section>
            <h3>Meine Beiträge</h3>

            <ul>
                <li><a href="beitrag.php">Minecraft-Bauprojekt</a></li>
                <li><a href="beitrag.php">Stardew Valley Farm</a></li>
            </ul>
        </section>

        <section>
            <h3>Aktionen</h3>
            <p>
                <a href="profil-bearbeiten.php">Profil bearbeiten</a>
            </p>
        </section>
    </section>
</main>

<?php include_once "php/footer.php"; ?>