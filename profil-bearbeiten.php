<?php
$title = "Profil bearbeiten";
include_once "php/head.php";
include_once "php/nav.php";
?>

<main>
    <section>
        <h2>Profil bearbeiten</h2>
        <p>Hier können Sie Ihre Profildaten ändern.</p>

        <form action="profil-bearbeiten.php" method="post">
            <fieldset>
                <legend>Persönliche Daten</legend>

                <div>
                    <label for="benutzername">Benutzername:</label><br>
                    <input type="text" id="benutzername" name="benutzername" value="MaxMustermann" required>
                </div>

                <div>
                    <label for="email">E-Mail-Adresse:</label><br>
                    <input type="email" id="email" name="email" value="max@example.de" required>
                </div>
            </fieldset>

            <fieldset>
                <legend>Passwort ändern</legend>

                <div>
                    <label for="altes-passwort">Altes Passwort:</label><br>
                    <input type="password" id="altes-passwort" name="altes_passwort">
                </div>

                <div>
                    <label for="neues-passwort">Neues Passwort:</label><br>
                    <input type="password" id="neues-passwort" name="neues_passwort">
                </div>

                <div>
                    <label for="passwort-wiederholen">Neues Passwort wiederholen:</label><br>
                    <input type="password" id="passwort-wiederholen" name="passwort_wiederholen">
                </div>
            </fieldset>

            <div>
                <button type="submit">Änderungen speichern</button>
            </div>
        </form>
    </section>
</main>

<?php include_once "php/footer.php"; ?>