<?php
require_once "PostSession.php";

class Post {
    public static function getInstance() {
        return PostSession::getInstance(); 
    }
}