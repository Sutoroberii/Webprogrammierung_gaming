<?php

require_once __DIR__ . "/FileAuthentication.php";
require_once __DIR__ . "/AuthenticationPDOSQLite.php";

class Authentication
{
    public static ?AuthenticationDao $instance = null;

    public static function getInstance(): AuthenticationDao
    {
        if (self::$instance === null) {
            $config = include __DIR__ . '/../../config.php';

            switch ($config['post_storage']) {
                case 'file':
                    self::$instance = new FileAuthentication(__DIR__ . "/../../data/auth");
                    break;

                case 'db':
                    self::$instance = AuthenticationPDOSQLite::getInstance();
                    break;

                default:
                    throw new Exception("Invalid storage type in config");
            }
        }

        return self::$instance;
    }
}
