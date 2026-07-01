<?php

require_once __DIR__ . "/FileAuthentication.php";
require_once __DIR__ . "/AuthenticationPDOSQLite.php";

class Authentication {
    public static ?AuthenticationDao $instance = null;

    public static function getInstance(): AuthenticationDao {
        if (self::$instance === null) {
            $config = include __DIR__ . '/../../config.php';
            $storage = $config['auth_storage'] ?? $config['post_storage'] ?? 'file';

            switch ($storage) {
                case 'file':
                    self::$instance = new FileAuthentication(__DIR__ . "/../../data/auth");
                    break;
                case 'db':
                    self::$instance = AuthenticationPDOSQLite::getInstance();
                    break;
                default:
                    throw new Exception('Invalid auth storage type in config');
            }
        }

        return self::$instance;
    }
}