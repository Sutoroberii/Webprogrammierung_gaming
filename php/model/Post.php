<?php
require_once __DIR__ ."/FilePost.php";

class Post {
    private static ?PostDao $instance = null;

    public static function getInstance(): PostDao {
        if (self::$instance === null) {
            self::$instance = new FilePost(__DIR__ ."/../../posts");
        }
        return self::$instance;
    }
}