<?php

require_once __DIR__ . "/UserPDOSQLite.php";

class User {
    private static ?UserDAO $instance = null;

    public static function getInstance(): UserDAO {
        if (self::$instance === null) {
            self::$instance = UserPDOSQLite::getInstance();
        }

        return self::$instance;
    }
}
