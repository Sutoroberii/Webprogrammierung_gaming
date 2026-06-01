<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once "path.php";
}

require_once $abs_path . "/controller/BeitragController.php";

$beitragController = new BeitragController();
$beitragController->createNewEntry();

header("Location: index.php");
exit;