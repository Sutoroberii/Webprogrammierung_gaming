<?php

require_once __DIR__ . "/FileMedia.php";

class Media {
    private static ?MediaDao $instance = null;

    public static function getInstance(): MediaDao {
        if (self::$instance === null) {
            $config = include __DIR__ . '/../../config.php';
            $storage = $config['media_storage'] ?? 'file';

            switch ($storage) {
                case 'file':
                    self::$instance = new FileMedia(__DIR__ . "/../../images", "./images");
                    break;
                default:
                    throw new Exception('Invalid media storage type in config');
            }
        }

        return self::$instance;
    }
}