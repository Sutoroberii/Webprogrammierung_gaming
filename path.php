<?php
$abs_path = __DIR__;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>