<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once __DIR__ . "/path.php";
}

require_once $abs_path . "/php/controller/BenutzerController.php";

$benutzerController = new BenutzerController();

$loginErfolgreich = $benutzerController->loginUser();

if ($loginErfolgreich) {
    header("Location: index.php");
    exit;
}

header("Location: anmeldung.php");
exit;