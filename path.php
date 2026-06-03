<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$abs_path = __DIR__;