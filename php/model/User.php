<?php
require_once __DIR__ ."/FileUser.php";

class User {

    private static ?UserDao $instance = null;

    public static function getInstance(): UserDao {
        if (self::$instance === null) {
            self::$instance = new FileUser(__DIR__ ."/../../data/users");
        }
        return self::$instance;
    }
}