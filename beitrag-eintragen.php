<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once "path.php";
}

require_once $abs_path . "/controller/PostController.php";

$beitragController = new PostController();
$beitragController->createPost();

header("Location: index.php");
exit;