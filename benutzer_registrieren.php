<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once __DIR__ . "/path.php";
}

require_once $abs_path . "/php/controller/BenutzerController.php";

$benutzerController = new BenutzerController();
$benutzerController->createUser();

header("Location: anmeldung.php");
exit;