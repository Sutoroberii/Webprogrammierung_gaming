<?php
require_once __DIR__ ."/FileAuthentication.php";
class Authentication {

    public static ?AuthenticationDao $instance = null;

    public static function getInstance(): AuthenticationDao {
        if (self::$instance === null) {
            self::$instance = new FileAuthentication(__DIR__ ."/../../data/auth");
        }
        return self::$instance;
    }
}