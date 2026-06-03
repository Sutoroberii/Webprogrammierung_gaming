<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once "path.php";
}

require_once $abs_path . "/php/controller/PostController.php";

$beitragController = new BeitragController();
$entry = $beitragController->readEntry();

require_once $abs_path . "/view/beitrag-show.php";
?>