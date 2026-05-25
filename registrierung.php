<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($abs_path)) {
    require_once __DIR__ . "/path.php";
}

require_once $abs_path . "/php/view/registrierung.php";
?>