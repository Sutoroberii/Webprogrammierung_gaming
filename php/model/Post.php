<?php

require_once __DIR__ . "/PostPDOSQLite.php";

class Post
{
    private static ?PostDAO $instance = null;

    public static function getInstance(): PostDAO
    {
        if (self::$instance === null) {
            self::$instance = PostPDOSQLite::getInstance();
        }

        return self::$instance;
    }
}