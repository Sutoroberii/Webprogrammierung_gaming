<?php
require_once "BenutzerSession.php";

class Benutzer {
    public static function getInstance() {
        return BenutzerSession::getInstance(); 
    }
}