<?php
$abs_path = __DIR__;

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
}

?>