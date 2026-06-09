<?php
require_once __DIR__ . "/FileMedia.php";

class Media {
    private static ?FileMedia $instance = null;
    
    public static function getInstance(): MediaDao {
        if (self::$instance === null) {
            self::$instance = new FileMedia(__DIR__ . "/../../images", "./images/");
        }
        return self::$instance;
    }
    
}