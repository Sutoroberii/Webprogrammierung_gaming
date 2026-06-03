<?php
require_once "EintragSession.php";

class Eintrag {
    public static function getInstance() {
        return EintragSession::getInstance(); 
    }
}