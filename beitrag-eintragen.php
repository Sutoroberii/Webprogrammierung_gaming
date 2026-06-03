<?php
require_once __DIR__ . "/path.php";

if (!isset($_SESSION["loggedInUserId"])) {
    header("Location: anmeldung.php");
    exit;
}

require_once $abs_path . "/php/controller/PostController.php";

$beitragController = new PostController();
$beitragController->createPost();

header("Location: index.php");
exit;