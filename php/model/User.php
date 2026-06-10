<?php

require_once __DIR__ . "/FileUser.php";
require_once __DIR__ . "/UserPDOSQLite.php";

class User
{
    private static ?UserDAO $instance = null;

    public static function getInstance(): UserDAO
    {
        if (self::$instance === null) {
            $config = include __DIR__ . '/../../config.php';

            switch ($config['post_storage']) {
                case 'file':
                    self::$instance = new FileUser(__DIR__ . "/../../data/users");
                    break;

                case 'db':
                    self::$instance = UserPDOSQLite::getInstance();
                    break;

                default:
                    throw new Exception("Invalid storage type in config");
            }
        }

        return self::$instance;
    }
}
