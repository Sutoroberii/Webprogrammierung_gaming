<?php

require_once __DIR__ . "/AuthenticationPDOSQLite.php";

class Authentication {
    public static ?AuthenticationDao $instance = null;

    public static function getInstance(): AuthenticationDao {
        if (self::$instance === null) {
            self::$instance = AuthenticationPDOSQLite::getInstance();
        }

        return self::$instance;
    }
}
