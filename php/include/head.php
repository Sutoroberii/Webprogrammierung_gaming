<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] =
        bin2hex(random_bytes(32));
}?>
<!DOCTYPE html>
<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NPC Tavern</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>