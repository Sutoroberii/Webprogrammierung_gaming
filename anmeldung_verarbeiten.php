<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once __DIR__ . "/path.php";
}

require_once $abs_path . "/php/model/Benutzer.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: anmeldung.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$passwort = $_POST["passwort"] ?? "";

if ($email === "" || $passwort === "") {
    $_SESSION["login_error"] = "Bitte E-Mail-Adresse und Passwort eingeben.";
    $_SESSION["old_email"] = $email;

    header("Location: anmeldung.php");
    exit;
}

try {
    $benutzerDAO = Benutzer::getInstance();

    $user = $benutzerDAO->loginUser($email, $passwort);

    $_SESSION["loggedInUserId"] = $user->getId();
    $_SESSION["benutzername"] = $user->getBenutzername();
    $_SESSION["email"] = $user->getEmail();
    $_SESSION["message"] = "login_user";

    unset($_SESSION["login_error"]);
    unset($_SESSION["old_email"]);

    header("Location: index.php");
    exit;

} catch (InvalidInputException $e) {
    $_SESSION["login_error"] = "E-Mail-Adresse oder Passwort ist falsch.";
    $_SESSION["old_email"] = $email;

    header("Location: anmeldung.php");
    exit;

} catch (Exception $e) {
    $_SESSION["login_error"] = "Beim Login ist ein Fehler aufgetreten.";

    header("Location: anmeldung.php");
    exit;
}