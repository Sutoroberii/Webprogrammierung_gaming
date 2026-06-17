<?php

require_once __DIR__ . "/path.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once $abs_path . "/php/controller/PostController.php";

$postController = new PostController();

$data = [
    "postTitle" => $_POST["postTitle"] ?? $_POST["spiel"] ?? "",
    "postText" => $_POST["postText"] ?? $_POST["text"] ?? "",
    "postTags" => $_POST["postTags"] ?? $_POST["tags"] ?? "",
    "postMedia" => $_POST["postMedia"] ?? "",
    "postAuthor" => $_SESSION["username"]
];

if (isset($_FILES["postMediaFile"]['name'])) {
    $data["postMediaFile"] = $_FILES["postMediaFile"];
} elseif (isset($_FILES["bild"])) {
    $data["postMediaFile"] = $_FILES["bild"];
}

$result = $postController->createNewPost($data);

if (!$result["success"]) {
    $_SESSION["post_errors"] = $result["errors"];
    $_SESSION["old_post_data"] = $_POST;

    header("Location: beitrag-neu.php");
    exit;
}

$post = $result["post"];

if ($post !== null && $post->getPostUrl() !== null && $post->getPostUrl() !== "") {
    header("Location: beitrag-lesen.php?url=" . urlencode($post->getPostUrl()));
    exit;
}

header("Location: index.php");
exit;