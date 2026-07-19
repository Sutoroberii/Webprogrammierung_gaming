<?php

require_once __DIR__ . "/path.php";
require_once $abs_path . "/php/controller/PostController.php";

$title = "Beitrag lesen";

$beitragController = new PostController();

$found = false;
$entry = null;
$post = null;
$html = "";
$error = null;

try {
    if (isset($_GET["url"]) && trim($_GET["url"]) !== "") {
        $url = trim($_GET["url"]);

        $result = $beitragController->open($url);

        $found = $result["found"];
        $entry = htmlspecialchars($result["post"]);
        $post = htmlspecialchars($result["post"]);
        $html = htmlspecialchars($result["html"]);

        if (!$found) {
            $error = "Der Beitrag wurde nicht gefunden.";
        }

    } elseif (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $id = (int) $_GET["id"];

        $entry = $beitragController->findById($id);
        $post = $entry;

        if ($entry === null) {
            $error = "Der Beitrag wurde nicht gefunden.";
        } else {
            $found = true;
            $html = nl2br(htmlspecialchars($entry->getPostText()));
        }

    } else {
        $error = "Kein Beitrag angegeben.";
    }

} catch (Exception $e) {
    $error = "Der Beitrag konnte nicht geladen werden.";
}

require_once $abs_path . "/view/beitrag-show.php";