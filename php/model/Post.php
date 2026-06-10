<?php

require_once __DIR__ . "/FilePost.php";
require_once __DIR__ . "/PostPDOSQLite.php";

class Post
{
    private static ?PostDAO $instance = null;

    public static function getInstance(): PostDAO
    {
        if (self::$instance === null) {
            $config = include __DIR__ . '/../../config.php';

            switch ($config['post_storage']) {
                case 'file':
                    self::$instance = new FilePost(__DIR__ . "/../../posts");
                    break;

                case 'db':
                    self::$instance = PostPDOSQLite::getInstance();
                    break;

                default:
                    throw new Exception("Invalid storage type in config");
            }
        }

        return self::$instance;
    }
}