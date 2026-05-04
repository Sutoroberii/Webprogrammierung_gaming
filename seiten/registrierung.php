<?php
$title = "Registrierung";
?>

<?php include_once "php/head.php"; ?>
<body>

<?php include_once "php/nav.php"; ?>

<main>
    <section>
        <h1>Registrierung</h1>

        <form action="registrierung.php" method="POST">
            <div>
                <label for="email">E-Mail-Adresse:</label><br>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="passwort">Passwort:</label><br>
                <input type="password" id="passwort" name="passwort" required>
            </div>

            <div>
                <label for="passwort_wiederholen">Passwort wiederholen:</label><br>
                <input type="password" id="passwort_wiederholen" name="passwort_wiederholen" required>
            </div>

            <br>
            <button type="submit">Registrieren</button>
        </form>
    </section>
</main>

<?php include_once "php/footer.php"; ?>
</body>