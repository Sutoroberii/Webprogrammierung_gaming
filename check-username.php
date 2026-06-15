<?php

require_once __DIR__ . "/php/controller/AuthentificationController.php";

header("Content-Type: application/json; charset=UTF-8");

$sessionControl = new SessionControl();
$authControl = new AuthenticationController($sessionControl);

$username = $_GET["username"] ?? "";

$result = $authControl->checkUsernameAvailability($username);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit();